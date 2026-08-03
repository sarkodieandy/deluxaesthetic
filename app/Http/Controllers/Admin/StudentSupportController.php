<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentSupportRequest;
use App\Services\Notifications\InAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentSupportController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.student-support.index', [
            'requests' => StudentSupportRequest::with(['studentProfile.user', 'enrolment.course', 'assignedTo'])
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
                ->latest()->paginate(20)->withQueryString(),
        ]);
    }

    public function show(StudentSupportRequest $studentSupport): View
    {
        return view('admin.student-support.show', ['support' => $studentSupport->load(['studentProfile.user', 'enrolment.course', 'assignedTo'])]);
    }

    public function update(Request $request, StudentSupportRequest $studentSupport, InAppNotificationService $notifications): RedirectResponse
    {
        $data = $request->validate([
            'admin_response' => ['required', 'string', 'max:5000'],
            'status' => ['required', Rule::in(['open', 'in_progress', 'resolved', 'closed'])],
        ]);
        $studentSupport->update([...$data, 'assigned_to' => $request->user()->id]);
        $student = $studentSupport->studentProfile?->user;
        if ($student) {
            $notifications->notifyUser($student, [
                'title' => 'Academy replied to '.$studentSupport->reference,
                'message' => $data['admin_response'],
                'action_url' => route('student.support.index', absolute: false),
                'category' => 'support',
            ]);
        }

        return back()->with('status', 'Reply sent to the student portal.');
    }
}
