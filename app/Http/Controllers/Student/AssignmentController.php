<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Services\Student\StudentPortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;

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
            fn ($enrolment) => [
                'assignments' => Assignment::query()
                    ->where('course_id', $enrolment->course_id)
                    ->where(fn ($query) => $query->whereNull('enrolment_id')->orWhere('enrolment_id', $enrolment->id))
                    ->orderBy('due_at')
                    ->get(),
            ],
        );
    }

    public function show(Request $request, Assignment $assignment): View
    {
        $enrolment = $this->portal->primaryEnrolment($request->user());

        if (! $enrolment) {
            return view('student.shared.no-enrolment-page', [
                'title' => __('student.nav.assignments'),
                'heading' => __('student.nav.assignments'),
            ]);
        }

        if (! $this->portal->hasLearningModuleAccess($enrolment)) {
            return view('student.shared.section-restricted', [
                'title' => __('student.nav.assignments'),
                'heading' => __('student.nav.assignments'),
                'enrolment' => $enrolment,
            ]);
        }

        abort_unless((int) $assignment->course_id === (int) $enrolment->course_id, 403);
        abort_unless($assignment->enrolment_id === null || (int) $assignment->enrolment_id === (int) $enrolment->id, 403);

        $submission = AssignmentSubmission::query()
            ->where('assignment_id', $assignment->id)
            ->where('enrolment_id', $enrolment->id)
            ->first();

        return view('student.assignments.show', compact('enrolment', 'assignment', 'submission'));
    }

    public function submit(Request $request, Assignment $assignment): RedirectResponse
    {
        $enrolment = $this->portal->primaryEnrolment($request->user());

        if (! $this->portal->hasLearningModuleAccess($enrolment)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        abort_unless((int) $assignment->course_id === (int) $enrolment->course_id, 403);
        abort_unless($assignment->enrolment_id === null || (int) $assignment->enrolment_id === (int) $enrolment->id, 403);

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

        $path = $request->file('file')?->store('assignments/submissions', 'public');

        AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'enrolment_id' => $enrolment->id],
            [
                'file_path' => $path ?: $existing?->file_path,
                'notes' => $data['notes'] ?? null,
                'submitted_at' => now(),
                'is_late' => $assignment->due_at ? now()->gt($assignment->due_at) : false,
            ]
        );

        return back()->with('status', __('student.assignments.submitted'));
    }

    public function download(Request $request, Assignment $assignment): StreamedResponse
    {
        $enrolment = $this->portal->primaryEnrolment($request->user());
        abort_unless($this->portal->hasLearningModuleAccess($enrolment), 403);
        abort_unless((int) $assignment->course_id === (int) $enrolment->course_id, 403);
        abort_unless($assignment->enrolment_id === null || (int) $assignment->enrolment_id === (int) $enrolment->id, 403);
        abort_unless($assignment->attachment_path, 404);

        return Storage::disk('public')->download($assignment->attachment_path, basename($assignment->attachment_path));
    }
}
