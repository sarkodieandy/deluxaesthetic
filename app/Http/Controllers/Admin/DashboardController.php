<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\Dashboard\DashboardMetricsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardMetricsService $metrics,
    ) {}

    public function index(): View
    {
        return view('admin.dashboard.index', [
            'metrics' => $this->metrics->metrics(),
            'activity' => $this->metrics->recentActivity(),
        ]);
    }
}
