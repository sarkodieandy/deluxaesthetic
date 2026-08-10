<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EnrolmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrolment;
use App\Services\Notifications\InAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $data['attachment_path'] = $request->file('attachment')?->store('assignments/resources', 'academy_private');
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
        $oldPath = null;
        if ($request->hasFile('attachment')) {
            $oldPath = $assignment->attachment_path;
            $data['attachment_path'] = $request->file('attachment')->store('assignments/resources', 'academy_private');
        }

        DB::transaction(function () use ($assignment, $data, $oldPath): void {
            $assignment->update($data);
            if ($oldPath && ($data['attachment_path'] ?? null) !== $oldPath) {
                DB::afterCommit(fn () => $this->deleteAssignmentFile($oldPath));
            }
        });

        return back()->with('status', 'Assignment updated.');
    }

    public function destroy(Assignment $assignment): RedirectResponse
    {
        DB::transaction(function () use ($assignment): void {
            $paths = collect([$assignment->attachment_path])
                ->merge($assignment->submissions()->whereNotNull('file_path')->pluck('file_path'))
                ->filter()
                ->values()
                ->all();
            $assignment->delete();

            DB::afterCommit(function () use ($paths): void {
                foreach ($paths as $path) {
                    $this->deleteAssignmentFile($path);
                }
            });
        });

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
        abort_unless(Storage::disk('academy_private')->exists($submission->file_path), 404);

        return Storage::disk('academy_private')->download($submission->file_path, basename($submission->file_path));
    }

    public function download(Assignment $assignment): StreamedResponse
    {
        abort_unless($assignment->attachment_path, 404);
        abort_unless(Storage::disk('academy_private')->exists($assignment->attachment_path), 404);

        return Storage::disk('academy_private')->download(
            $assignment->attachment_path,
            basename($assignment->attachment_path),
        );
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
            throw ValidationException::withMessages(['enrolment_id' => 'The selected student is not enrolled in this course.']);
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
                ->whereIn('status', EnrolmentStatus::portalAccessStatuses())->latest()->get(),
        ]);
    }

    private function deleteAssignmentFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        Storage::disk('academy_private')->delete($path);
        Storage::disk('public')->delete($path);
    }
}
