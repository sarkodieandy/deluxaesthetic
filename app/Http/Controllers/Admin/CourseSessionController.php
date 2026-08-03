<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academy\StoreCourseSessionRequest;
use App\Models\CourseSchedule;
use App\Models\CourseSession;
use App\Models\Enrolment;
use App\Services\Notifications\InAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourseSessionController extends Controller
{
    public function index(CourseSchedule $courseSchedule): View
    {
        $courseSchedule->load('course');

        $sessions = CourseSession::query()
            ->where('course_schedule_id', $courseSchedule->id)
            ->orderBy('session_date')
            ->orderBy('starts_at')
            ->get();

        return view('admin.course-sessions.index', [
            'schedule' => $courseSchedule,
            'course' => $courseSchedule->course,
            'sessions' => $sessions,
        ]);
    }

    public function store(StoreCourseSessionRequest $request, CourseSchedule $courseSchedule, InAppNotificationService $notifications): RedirectResponse
    {
        $session = CourseSession::query()->create([
            ...$request->validated(),
            'course_schedule_id' => $courseSchedule->id,
        ]);

        Enrolment::query()
            ->with('studentProfile.user')
            ->where('course_schedule_id', $courseSchedule->id)
            ->whereIn('status', \App\Enums\EnrolmentStatus::portalAccessStatuses())
            ->get()
            ->each(function (Enrolment $enrolment) use ($notifications, $session): void {
                $student = $enrolment->studentProfile?->user;
                if ($student) {
                    $notifications->notifyUser($student, [
                        'title' => 'Class scheduled for '.$session->session_date->format('d M Y'),
                        'message' => ($session->topic ?: 'Training session').' starts at '.substr((string) $session->starts_at, 0, 5).'.',
                        'action_url' => route('student.calendar.index', absolute: false),
                        'category' => 'class_schedule',
                    ]);
                }
            });

        return back()->with('status', 'Training session added.');
    }
}
