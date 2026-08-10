<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseMaterial;
use App\Models\MaterialDownload;
use App\Services\Student\StudentPortalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            fn ($enrolments) => [
                'materials' => $this->portal->publishedMaterialsForUser($request->user()),
            ],
        );
    }

    public function download(Request $request, CourseMaterial $material): StreamedResponse|RedirectResponse
    {
        $enrolment = $this->portal->learningEnrolmentForResource(
            $request->user(),
            (int) $material->course_id,
            $material->enrolment_id ? (int) $material->enrolment_id : null,
        );

        abort_unless($enrolment, Response::HTTP_FORBIDDEN);
        abort_unless($material->is_published && $material->file_path, 404);
        abort_unless(Storage::disk('academy_private')->exists($material->file_path), 404);

        MaterialDownload::query()->create([
            'course_material_id' => $material->id,
            'student_profile_id' => $enrolment->student_profile_id,
            'enrolment_id' => $enrolment->id,
            'ip_address' => $request->ip(),
            'downloaded_at' => now(),
        ]);

        return Storage::disk('academy_private')->download($material->file_path, basename($material->file_path));
    }
}
