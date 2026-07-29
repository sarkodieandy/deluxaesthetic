<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Clients\UpdateConsultationRequest;
use App\Models\ConsultationRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function index(): View
    {
        $consultations = ConsultationRequest::query()
            ->with(['user:id,name,email'])
            ->latest('created_at')
            ->paginate(20);

        return view('admin.consultations.index', compact('consultations'));
    }

    public function edit(ConsultationRequest $consultation): View
    {
        $staff = User::query()
            ->role(config('admin.roles', []))
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.consultations.edit', [
            'consultation' => $consultation,
            'staff' => $staff,
        ]);
    }

    public function update(UpdateConsultationRequest $request, ConsultationRequest $consultation): RedirectResponse
    {
        $data = $request->validated();

        $consultation->update([
            'status' => $data['status'],
            'assigned_to' => $data['assigned_to'] ?: null,
            'follow_up_date' => $data['follow_up_date'] ?: null,
            'internal_notes' => $data['internal_notes'] ?: null,
            'client_response' => $data['client_response'] ?: null,
        ]);

        return redirect()
            ->route('admin.consultations.edit', $consultation)
            ->with('status', 'Consultation request updated successfully.');
    }
}
