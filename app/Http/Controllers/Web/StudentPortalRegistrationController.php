<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academy\StoreStudentPortalRegistrationRequest;
use App\Models\Course;
use App\Services\Academy\StudentPortalRegistrationService;
use App\Services\Messaging\EmailNotificationService;
use App\Services\Notifications\InAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentPortalRegistrationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()?->hasRole('Student')) {
            return redirect()->route('student.dashboard');
        }

        return view('web.academy.index');
    }

    public function register(Request $request): View|RedirectResponse
    {
        if ($request->user()?->hasRole('Student')) {
            return redirect()->route('student.dashboard');
        }

        return view('web.academy.student-portal-register', [
            'loggedInAsClient' => (bool) $request->user()?->hasRole('Client'),
            'courses' => Course::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'selectedCourseId' => $request->integer('course') ?: old('course_id'),
        ]);
    }

    public function store(
        StoreStudentPortalRegistrationRequest $request,
        StudentPortalRegistrationService $registration,
        InAppNotificationService $notifications,
        EmailNotificationService $email,
    ): RedirectResponse {
        $user = $registration->register($request->validated());

        $notifications->notifyAdmins([
            'title' => 'New student application awaiting approval',
            'message' => $user->name.' applied for physical academy training. Review the course enquiry and contact the applicant before approving portal access.',
            'action_url' => route('admin.students.index', absolute: false),
            'category' => 'student_registration',
        ]);

        $email->queueToUser('academy.application_received', $user);

        return redirect()->route('web.academy.student-portal.create')
            ->with('status', __('web.student_portal.register_success'));
    }
}
