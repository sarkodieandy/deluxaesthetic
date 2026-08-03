<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\Notifications\InAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function destroy(User $student): RedirectResponse
    {
        abort_unless($student->hasRole('Student') && $student->studentProfile, 404);

        if ($student->is_active) {
            return back()->withErrors(['delete' => 'Active students cannot be deleted from the applications queue. Suspend or archive them through student management.']);
        }

        $profile = $student->studentProfile;
        $hasEnrolments = DB::table('enrolments')->where('student_profile_id', $profile->id)->exists();
        $hasConvertedEnquiry = DB::table('course_enquiries')->where('user_id', $student->id)
            ->where(fn ($query) => $query->whereNotNull('converted_enrolment_id')->orWhereNotNull('converted_student_profile_id'))
            ->exists();

        if ($hasEnrolments || $hasConvertedEnquiry) {
            return back()->withErrors(['delete' => 'This applicant already has an admissions or enrolment record and cannot be deleted.']);
        }

        DB::transaction(function () use ($student) {
            DB::table('course_enquiries')->where('user_id', $student->id)->delete();
            $student->delete();
        });

        return back()->with('status', 'Pending student application deleted.');
    }
}
