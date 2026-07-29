<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academy\StoreCourseSessionRequest;
use App\Models\CourseSchedule;
use App\Models\CourseSession;
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

    public function store(StoreCourseSessionRequest $request, CourseSchedule $courseSchedule): RedirectResponse
    {
        CourseSession::query()->create([
            ...$request->validated(),
            'course_schedule_id' => $courseSchedule->id,
        ]);

        return back()->with('status', 'Training session added.');
    }
}
