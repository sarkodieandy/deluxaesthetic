<?php

namespace App\Http\Controllers\Web;

use App\Actions\Appointments\CreateAppointmentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appointments\StoreBookingRequest;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\PractitionerProfile;
use App\Models\Treatment;
use App\Services\Appointments\AvailabilityService;
use App\Services\Appointments\ResolveBookingClientProfile;
use App\Services\Notifications\InAppNotificationService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class BookingController extends Controller
{
    public function create(Request $request): View
    {
        $user = $request->user();

        return view('web.consultation.book', [
            'treatments' => Treatment::query()->with('category')->where('is_active', true)->orderBy('name')->get(),
            'branches' => Branch::query()->where('is_active', true)->orderBy('name')->get(),
            'practitioners' => PractitionerProfile::query()->with('user')->where('is_active', true)->orderBy('sort_order')->get(),
            'selectedTreatment' => $request->integer('treatment_id') ?: null,
            'isAuthenticatedClient' => $user?->hasRole('Client') === true,
            'guestDefaults' => [
                'name' => old('guest_name', $user?->name),
                'email' => old('guest_email', $user?->email),
                'phone' => old('guest_phone', $user?->phone),
            ],
        ]);
    }

    public function slots(Request $request, AvailabilityService $availability)
    {
        $data = $request->validate([
            'practitioner_profile_id' => ['required', 'exists:practitioner_profiles,id'],
            'treatment_id' => ['required', 'exists:treatments,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $slots = $availability->slotsForDate(
            (int) $data['practitioner_profile_id'],
            (int) $data['treatment_id'],
            (int) $data['branch_id'],
            CarbonImmutable::parse($data['date'], config('clinic.timezone'))
        );

        return response()->json(['slots' => $slots]);
    }

    public function store(
        StoreBookingRequest $request,
        CreateAppointmentAction $action,
        ResolveBookingClientProfile $resolveClient,
        InAppNotificationService $notifications,
    ): RedirectResponse {
        $user = $request->user();

        $client = $resolveClient->execute($user, [
            'name' => (string) $request->input('guest_name', $user?->name ?? ''),
            'email' => (string) $request->input('guest_email', $user?->email ?? ''),
            'phone' => (string) $request->input('guest_phone', $user?->phone ?? ''),
        ]);

        try {
            $appointment = $action->execute([
                'client_profile_id' => $client->id,
                'treatment_id' => $request->integer('treatment_id'),
                'practitioner_profile_id' => $request->integer('practitioner_profile_id'),
                'branch_id' => $request->integer('branch_id'),
                'starts_at' => $request->string('starts_at')->toString(),
                'booked_by_user_id' => $user?->id,
                'client_notes' => $request->string('client_notes')->toString() ?: null,
                'consultation_answers' => [
                    'goals' => $request->string('goals')->toString(),
                    'consent' => true,
                    'guest_booking' => $client->isGuest(),
                ],
            ]);
        } catch (InvalidArgumentException $exception) {
            return back()->withInput()->withErrors(['booking' => $exception->getMessage()]);
        }

        $notifications->notifyAdmins([
            'title' => 'New appointment booking',
            'message' => $client->displayName().' booked '.$appointment->treatment?->name.' ('.$appointment->reference.').',
            'action_url' => route('admin.appointments.edit', $appointment),
            'category' => 'appointment',
        ]);

        return redirect()
            ->route('web.booking.confirmation', $appointment->reference)
            ->with('status', 'Appointment '.$appointment->reference.' created and awaiting payment confirmation.');
    }

    public function confirmation(string $reference): View
    {
        $appointment = Appointment::query()
            ->with(['treatment', 'practitioner.user', 'branch', 'clientProfile'])
            ->where('reference', $reference)
            ->firstOrFail();

        return view('web.consultation.confirmation', compact('appointment'));
    }
}
