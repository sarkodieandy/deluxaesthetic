<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Clients\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\AppointmentStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(): View
    {
        $appointments = Appointment::query()
            ->with(['clientProfile.user', 'treatment', 'practitioner.user', 'branch'])
            ->latest('starts_at')
            ->paginate(20);

        return view('admin.appointments.index', compact('appointments'));
    }

    public function edit(Appointment $appointment): View
    {
        $appointment->load(['clientProfile.user', 'treatment', 'practitioner.user', 'branch', 'statusHistories']);

        return view('admin.appointments.edit', compact('appointment'));
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validated();
        $oldStatus = $appointment->status?->value ?? (string) $appointment->status;

        $appointment->update([
            'status' => $data['status'],
            'internal_notes' => $data['internal_notes'] ?: null,
            'cancellation_reason' => $data['cancellation_reason'] ?: null,
            'amount_paid' => $data['amount_paid'] ?? $appointment->amount_paid,
        ]);

        if ($oldStatus !== $data['status']) {
            AppointmentStatusHistory::create([
                'appointment_id' => $appointment->id,
                'from_status' => $oldStatus,
                'to_status' => $data['status'],
                'changed_by' => $request->user()?->id,
                'notes' => $data['internal_notes'] ?? null,
            ]);
        }

        return redirect()->route('admin.appointments.edit', $appointment)->with('status', 'Appointment updated successfully.');
    }
}
