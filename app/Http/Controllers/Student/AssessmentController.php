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
        $user = $request->user();
        $enrolments = $this->portal->portalEnrolments($user);
        $enrolment = $this->portal->portalEnrolment($user, $request->input('enrolment'));
        abort_if($request->filled('enrolment') && ! $enrolment, 403);

        if (! $enrolment) {
            return $this->portal->viewOrNoEnrolment($user, 'student.assessments.index', [
                'title' => __('student.nav.assessments'),
                'heading' => __('student.nav.assessments'),
            ]);
        }

        return view('student.assessments.index', [
            'enrolment' => $enrolment,
            'enrolments' => $enrolments,
            'results' => $this->portal->assessmentResults($enrolment),
        ]);
    }
}
