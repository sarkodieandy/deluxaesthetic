<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Client\ClientPortalService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly ClientPortalService $portal,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('client.appointments.index', [
            'appointments' => $this->portal->appointments($user),
            'upcoming' => $this->portal->upcomingAppointments($user),
        ]);
    }
}
