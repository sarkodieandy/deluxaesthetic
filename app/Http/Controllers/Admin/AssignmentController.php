<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrolment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\Notifications\InAppNotificationService;

class AssignmentController extends Controller
{
    public function index(): View
    {
        return view('admin.assignments.index', ['assignments' => Assignment::with(['course', 'enrolment.studentProfile.user'])->withCount('submissions')->latest()->paginate(20)]);
    }

    public function create(): View
    {
        return $this->formView('admin.assignments.create', new Assignment);
    }

    public function store(Request $request, InAppNotificationService $notifications): RedirectResponse
    {
        $data = $this->validated($request);
        $data['attachment_path'] = $request->file('attachment')?->store('assignments/resources', 'public');
        $assignment = Assignment::create($data);
        $student = $assignment->enrolment?->studentProfile?->user;
        if ($student) {
            $notifications->notifyUser($student, [
                'title' => 'New assignment: '.$assignment->title,
                'message' => $assignment->due_at
                    ? 'Your assignment is due '.$assignment->due_at->format('d M Y, H:i').'.'
                    : 'A new assignment is available in your student portal.',
                'action_url' => route('student.assignments.show', $assignment, absolute: false),
                'category' => 'assignment',
            ]);
        }

        return redirect()->route('admin.assignments.edit', $assignment)->with('status', 'Assignment published to the course.');
    }

    public function edit(Assignment $assignment): View
    {
        return $this->formView('admin.assignments.edit', $assignment->load(['submissions.enrolment.studentProfile.user']));
    }

    public function update(Request $request, Assignment $assignment): RedirectResponse
    {
        $data = $this->validated($request);
        if ($request->hasFile('attachment')) {
            if ($assignment->attachment_path) Storage::disk('public')->delete($assignment->attachment_path);
            $data['attachment_path'] = $request->file('attachment')->store('assignments/resources', 'public');
        }
        $assignment->update($data);

        return back()->with('status', 'Assignment updated.');
    }

    public function destroy(Assignment $assignment): RedirectResponse
    {
        if ($assignment->attachment_path) Storage::disk('public')->delete($assignment->attachment_path);
        $assignment->delete();

        return redirect()->route('admin.assignments.index')->with('status', 'Assignment deleted.');
    }

    public function review(Request $request, AssignmentSubmission $submission): RedirectResponse
    {
        $data = $request->validate(['score' => ['nullable', 'numeric', 'min:0'], 'feedback' => ['nullable', 'string', 'max:5000']]);
        $submission->update($data);

        return back()->with('status', 'Submission feedback saved.');
    }

    public function downloadSubmission(AssignmentSubmission $submission): StreamedResponse
    {
        abort_unless($submission->file_path, 404);
        return Storage::disk('public')->download($submission->file_path, basename($submission->file_path));
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'], 'enrolment_id' => ['required', 'exists:enrolments,id'], 'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string', 'max:10000'], 'due_at' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'max:51200', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,jpg,jpeg,png'],
            'allow_resubmission' => ['nullable', 'boolean'],
        ]);
        $enrolment = Enrolment::findOrFail($data['enrolment_id']);
        if ((int) $enrolment->course_id !== (int) $data['course_id']) {
            throw \Illuminate\Validation\ValidationException::withMessages(['enrolment_id' => 'The selected student is not enrolled in this course.']);
        }
        $data['allow_resubmission'] = $request->boolean('allow_resubmission');
        unset($data['attachment']);
        return $data;
    }

    private function formView(string $view, Assignment $assignment): View
    {
        return view($view, [
            'assignment' => $assignment,
            'courses' => Course::where('is_active', true)->orderBy('name')->get(),
            'enrolments' => Enrolment::with(['course:id,name', 'studentProfile.user:id,name,email'])
                ->whereIn('status', \App\Enums\EnrolmentStatus::portalAccessStatuses())->latest()->get(),
        ]);
    }
}
