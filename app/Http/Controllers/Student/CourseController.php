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
        $enrolments = $this->portal->portalEnrolments($request->user());
        $enrolment = $enrolments->first();

        if (! $enrolment) {
            return view('student.shared.no-enrolment-page', [
                'title' => __('student.nav.course'),
                'heading' => __('student.nav.course'),
            ]);
        }

        return view('student.courses.show', compact('enrolment', 'enrolments'));
    }
}
