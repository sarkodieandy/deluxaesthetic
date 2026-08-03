<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\Notifications\InAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status', 'pending');
        if (! in_array($status, ['pending', 'active', 'all'], true)) {
            $status = 'pending';
        }

        $students = StudentProfile::query()->with('user')
            ->whereHas('user', fn ($query) => $query
                ->when($status === 'pending', fn ($q) => $q->where('is_active', false))
                ->when($status === 'active', fn ($q) => $q->where('is_active', true))
                ->when($request->filled('q'), fn ($q) => $q->where(fn ($inner) => $inner
                    ->where('name', 'like', '%'.$request->string('q')->trim().'%')
                    ->orWhere('email', 'like', '%'.$request->string('q')->trim().'%')
                    ->orWhere('phone', 'like', '%'.$request->string('q')->trim().'%'))))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.students.index', [
            'students' => $students,
            'status' => $status,
            'pendingCount' => StudentProfile::query()->whereHas('user', fn ($q) => $q->where('is_active', false))->count(),
        ]);
    }

    public function approve(Request $request, User $student, InAppNotificationService $notifications): RedirectResponse
    {
        $request->validate(['contact_confirmed' => ['accepted']]);
        abort_unless($student->hasRole('Student') && $student->studentProfile, 404);

        $student->update(['is_active' => true]);
        $student->studentProfile->update(['portal_activated_at' => now()]);

        $notifications->notifyUser($student, [
            'title' => 'Student portal access approved',
            'message' => 'Admissions approved your account. You can now access your student portal while your physical training enrolment is arranged.',
            'action_url' => route('student.dashboard', absolute: false),
            'category' => 'account',
        ]);

        return back()->with('status', $student->name.' can now sign in to the student portal.');
    }
}
