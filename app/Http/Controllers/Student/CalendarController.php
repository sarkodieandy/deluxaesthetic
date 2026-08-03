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
        $sessions = $this->sessionsFor($request);

        return $this->portal->viewOrNoEnrolment($request->user(), 'student.calendar.index', [
            'title' => __('student.nav.calendar'),
            'heading' => __('student.nav.calendar'),
            'sessions' => $sessions,
            'upcomingSessions' => $sessions->filter(fn (CourseSession $session) => $session->session_date?->isToday() || $session->session_date?->isFuture()),
            'pastSessions' => $sessions->filter(fn (CourseSession $session) => $session->session_date?->isPast() && ! $session->session_date?->isToday())->reverse(),
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, CourseSession>
     */
    private function sessionsFor(Request $request)
    {
        $enrolment = $this->portal->primaryEnrolment($request->user());
        if (! $enrolment) {
            return collect();
        }

        return CourseSession::query()
            ->where('course_schedule_id', $enrolment->course_schedule_id)
            ->orderBy('session_date')
            ->orderBy('starts_at')
            ->get();
    }
}
