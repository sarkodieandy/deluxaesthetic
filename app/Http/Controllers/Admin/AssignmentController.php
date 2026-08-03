<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssignmentController extends Controller
{
    public function index(): View
    {
        return view('admin.assignments.index', ['assignments' => Assignment::with('course')->withCount('submissions')->latest()->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin.assignments.create', ['assignment' => new Assignment, 'courses' => Course::where('is_active', true)->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['attachment_path'] = $request->file('attachment')?->store('assignments/resources', 'public');
        $assignment = Assignment::create($data);

        return redirect()->route('admin.assignments.edit', $assignment)->with('status', 'Assignment published to the course.');
    }

    public function edit(Assignment $assignment): View
    {
        return view('admin.assignments.edit', [
            'assignment' => $assignment->load(['submissions.enrolment.studentProfile.user']),
            'courses' => Course::where('is_active', true)->orderBy('name')->get(),
        ]);
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
            'course_id' => ['required', 'exists:courses,id'], 'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string', 'max:10000'], 'due_at' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'max:51200', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,jpg,jpeg,png'],
            'allow_resubmission' => ['nullable', 'boolean'],
        ]);
        $data['allow_resubmission'] = $request->boolean('allow_resubmission');
        unset($data['attachment']);
        return $data;
    }
}
