<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academy\StoreStudentPortalRegistrationRequest;
use App\Services\Academy\StudentPortalRegistrationService;
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
        ]);
    }

    public function store(
        StoreStudentPortalRegistrationRequest $request,
        StudentPortalRegistrationService $registration,
        InAppNotificationService $notifications,
    ): RedirectResponse {
        $user = $registration->register($request->validated());

        $notifications->notifyAdmins([
            'title' => 'New student application awaiting approval',
            'message' => $user->name.' applied for physical academy training. Contact the applicant before approving portal access.',
            'action_url' => route('admin.students.index', absolute: false),
            'category' => 'student_registration',
        ]);

        return redirect()->route('web.academy.student-portal.create')
            ->with('status', __('web.student_portal.register_success'));
    }
}
