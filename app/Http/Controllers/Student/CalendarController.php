<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseSession;
use App\Services\Student\StudentPortalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __construct(
        private readonly StudentPortalService $portal,
    ) {}

    public function index(Request $request): View
    {
        $enrolments = $this->portal->portalEnrolments($request->user());
        $sessions = $this->portal->calendarSessions($request->user());

        return $this->portal->viewOrNoEnrolment($request->user(), 'student.calendar.index', [
            'title' => __('student.nav.calendar'),
            'heading' => __('student.nav.calendar'),
            'enrolments' => $enrolments,
            'sessions' => $sessions,
            'upcomingSessions' => $sessions->filter(fn (CourseSession $session) => $session->session_date?->isToday() || $session->session_date?->isFuture()),
            'pastSessions' => $sessions->filter(fn (CourseSession $session) => $session->session_date?->isPast() && ! $session->session_date?->isToday())->reverse(),
        ]);
    }
}
