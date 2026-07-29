<?php

namespace App\Http\Controllers\Practitioner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('practitioner.dashboard', [
            'user' => $user,
            'profile' => $user->practitionerProfile,
            'metrics' => [
                'appointments_today' => 0,
                'appointments_week' => 0,
                'open_consultations' => 0,
                'clients_seen_month' => 0,
            ],
        ]);
    }
}
