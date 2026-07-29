<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Client\ClientPortalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ClientPortalService $portal,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $metrics = $this->portal->dashboardMetrics($user);

        return view('client.dashboard.index', [
            'user' => $user,
            'profile' => $user->clientProfile,
            'metrics' => $metrics,
            'appointments' => $metrics['appointments'],
            'upcoming' => $metrics['upcoming'],
        ]);
    }
}
