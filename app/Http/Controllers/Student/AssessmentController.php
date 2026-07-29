<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Student\StudentPortalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function __construct(
        private readonly StudentPortalService $portal,
    ) {}

    public function index(Request $request): View
    {
        return $this->portal->viewOrNoEnrolment($request->user(), 'student.assessments.index', [
            'title' => __('student.nav.assessments'),
            'heading' => __('student.nav.assessments'),
            'results' => $this->resultsFor($request),
        ]);
    }

    private function resultsFor(Request $request)
    {
        $enrolment = $this->portal->primaryEnrolment($request->user());

        return $enrolment ? $this->portal->assessmentResults($enrolment) : collect();
    }
}
