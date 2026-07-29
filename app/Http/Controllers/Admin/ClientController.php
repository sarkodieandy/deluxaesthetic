<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClientProfile;
use App\Models\ConsultationRequest;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = ClientProfile::query()
            ->with(['user', 'preferredBranch'])
            ->latest('created_at')
            ->paginate(20);

        return view('admin.clients.index', compact('clients'));
    }

    public function show(ClientProfile $client): View
    {
        $client->load(['user', 'preferredBranch']);

        $appointments = Appointment::query()
            ->with(['treatment', 'practitioner.user', 'branch'])
            ->where('client_profile_id', $client->id)
            ->latest('starts_at')
            ->limit(10)
            ->get();

        $consultations = ConsultationRequest::query()
            ->where('user_id', $client->user_id)
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('admin.clients.show', compact('client', 'appointments', 'consultations'));
    }
}
