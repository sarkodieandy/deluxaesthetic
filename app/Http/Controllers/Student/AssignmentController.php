<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Services\Student\StudentPortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssignmentController extends Controller
{
    public function __construct(
        private readonly StudentPortalService $portal,
    ) {}

    public function index(Request $request): View
    {
        return $this->portal->viewOrLearningModule(
            $request->user(),
            'student.assignments.index',
            __('student.nav.assignments'),
            fn ($enrolments) => [
                'assignments' => $this->portal->assignmentsForUser($request->user()),
            ],
        );
    }

    public function show(Request $request, Assignment $assignment): View
    {
        $portalEnrolments = $this->portal->portalEnrolments($request->user());

        if ($portalEnrolments->isEmpty()) {
            return view('student.shared.no-enrolment-page', [
                'title' => __('student.nav.assignments'),
                'heading' => __('student.nav.assignments'),
            ]);
        }

        $enrolment = $this->portal->learningEnrolmentForResource(
            $request->user(),
            (int) $assignment->course_id,
            $assignment->enrolment_id ? (int) $assignment->enrolment_id : null,
        );

        if (! $enrolment && $this->portal->learningEnrolments($request->user())->isEmpty()) {
            return view('student.shared.section-restricted', [
                'title' => __('student.nav.assignments'),
                'heading' => __('student.nav.assignments'),
                'enrolment' => $portalEnrolments->first(),
            ]);
        }

        abort_unless($enrolment, Response::HTTP_FORBIDDEN);

        $submission = AssignmentSubmission::query()
            ->where('assignment_id', $assignment->id)
            ->where('enrolment_id', $enrolment->id)
            ->first();

        return view('student.assignments.show', compact('enrolment', 'assignment', 'submission'));
    }

    public function submit(Request $request, Assignment $assignment): RedirectResponse
    {
        $enrolment = $this->portal->learningEnrolmentForResource(
            $request->user(),
            (int) $assignment->course_id,
            $assignment->enrolment_id ? (int) $assignment->enrolment_id : null,
        );

        abort_unless($enrolment, Response::HTTP_FORBIDDEN);

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:5000'],
            'file' => ['nullable', 'file', 'max:5120', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ]);

        $existing = AssignmentSubmission::query()
            ->where('assignment_id', $assignment->id)
            ->where('enrolment_id', $enrolment->id)
            ->first();
        if ($existing && ! $assignment->allow_resubmission) {
            return back()->withErrors(['file' => 'This assignment has already been submitted and resubmission is closed.']);
        }

        $path = $request->file('file')?->store('assignments/submissions', 'academy_private');

        $submission = AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'enrolment_id' => $enrolment->id],
            [
                'file_path' => $path ?: $existing?->file_path,
                'notes' => $data['notes'] ?? null,
                'submitted_at' => now(),
                'is_late' => $assignment->due_at ? now()->gt($assignment->due_at) : false,
            ]
        );

        if ($path && $existing?->file_path && $existing->file_path !== $submission->file_path) {
            Storage::disk('academy_private')->delete($existing->file_path);
            Storage::disk('public')->delete($existing->file_path);
        }

        return back()->with('status', __('student.assignments.submitted'));
    }

    public function download(Request $request, Assignment $assignment): StreamedResponse
    {
        $enrolment = $this->portal->learningEnrolmentForResource(
            $request->user(),
            (int) $assignment->course_id,
            $assignment->enrolment_id ? (int) $assignment->enrolment_id : null,
        );
        abort_unless($enrolment, Response::HTTP_FORBIDDEN);
        abort_unless($assignment->attachment_path, 404);
        abort_unless(Storage::disk('academy_private')->exists($assignment->attachment_path), 404);

        return Storage::disk('academy_private')->download($assignment->attachment_path, basename($assignment->attachment_path));
    }
}
