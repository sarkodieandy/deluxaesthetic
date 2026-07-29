<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Student\StudentPortalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly StudentPortalService $portal,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $enrolment = $this->portal->primaryEnrolment($user);

        if (! $enrolment) {
            return $this->portal->viewOrNoEnrolment($user, 'student.attendance.index', [
                'title' => __('student.nav.attendance'),
                'heading' => __('student.nav.attendance'),
            ]);
        }

        return view('student.attendance.index', [
            'enrolment' => $enrolment,
            'summary' => $this->portal->attendanceSummary($enrolment),
            'records' => \App\Models\AttendanceRecord::query()
                ->where('enrolment_id', $enrolment->id)
                ->orderByDesc('session_date')
                ->get(),
        ]);
    }
}
