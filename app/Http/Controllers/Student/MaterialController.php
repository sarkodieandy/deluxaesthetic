<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseMaterial;
use App\Models\MaterialDownload;
use App\Services\Student\StudentPortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaterialController extends Controller
{
    public function __construct(
        private readonly StudentPortalService $portal,
    ) {}

    public function index(Request $request): View
    {
        return $this->portal->viewOrLearningModule(
            $request->user(),
            'student.materials.index',
            __('student.nav.materials'),
            fn ($enrolment) => [
                'materials' => $this->portal->publishedMaterials($enrolment),
            ],
        );
    }

    public function download(Request $request, CourseMaterial $material): StreamedResponse|RedirectResponse
    {
        $enrolment = $this->portal->primaryEnrolment($request->user());

        if (! $this->portal->hasLearningModuleAccess($enrolment)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        abort_unless($enrolment && (int) $material->course_id === (int) $enrolment->course_id, 403);
        abort_unless($material->enrolment_id === null || (int) $material->enrolment_id === (int) $enrolment->id, 403);
        abort_unless($material->is_published && $material->file_path, 404);

        MaterialDownload::query()->create([
            'course_material_id' => $material->id,
            'student_profile_id' => $enrolment->student_profile_id,
            'enrolment_id' => $enrolment->id,
            'ip_address' => $request->ip(),
            'downloaded_at' => now(),
        ]);

        return \Illuminate\Support\Facades\Storage::disk('public')->download($material->file_path, basename($material->file_path));
    }
}
