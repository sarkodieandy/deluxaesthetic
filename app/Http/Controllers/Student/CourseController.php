<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Student\StudentPortalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(
        private readonly StudentPortalService $portal,
    ) {}

    public function show(Request $request): View
    {
        $enrolment = $this->portal->primaryEnrolment($request->user());

        if (! $enrolment) {
            return view('student.shared.no-enrolment-page', [
                'title' => __('student.nav.course'),
                'heading' => __('student.nav.course'),
            ]);
        }

        $enrolment->load(['course.category', 'course.trainer.user']);

        return view('student.courses.show', compact('enrolment'));
    }
}
