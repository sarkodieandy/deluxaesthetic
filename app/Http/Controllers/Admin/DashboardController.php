<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseEnquiry;
use App\Models\StudentProfile;
use App\Services\Admin\Dashboard\DashboardMetricsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardMetricsService $metrics,
    ) {}

    public function index(Request $request): View
    {
        $range = (int) $request->integer('range', 30);

        $canViewEnquiries = $request->user()?->can('course_enquiries.view') === true;
        $canViewStudents = $request->user()?->can('students.view') === true;

        return view('admin.dashboard.index', [
            'metrics' => $this->metrics->metrics(),
            'activity' => $this->metrics->recentActivity(),
            'orderTrend' => $this->metrics->orderTrend($range),
            'newCourseEnquiries' => $canViewEnquiries
                ? CourseEnquiry::query()->whereIn('status', ['submitted', 'new'])->count()
                : null,
            'latestCourseEnquiries' => $canViewEnquiries
                ? CourseEnquiry::query()->with('course')->latest()->limit(5)->get()
                : collect(),
            'pendingStudentApplications' => $canViewStudents
                ? StudentProfile::query()->with('user')->whereHas('user', fn ($query) => $query->where('is_active', false))->latest()->get()
                : collect(),
        ]);
    }
}
