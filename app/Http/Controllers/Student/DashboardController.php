<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Student\StudentPortalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly StudentPortalService $portal,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $metrics = $this->portal->dashboardMetrics($user);

        return view('student.dashboard.index', [
            'user' => $user,
            'profile' => $user->studentProfile,
            'metrics' => $metrics,
            'enrolment' => $metrics['enrolment'],
        ]);
    }
}
