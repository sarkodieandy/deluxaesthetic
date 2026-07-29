<?php

namespace App\Actions\Appointments;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\AppointmentStatusHistory;
use App\Models\ClientProfile;
use App\Models\PractitionerProfile;
use App\Models\Treatment;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CreateAppointmentAction
{
    /**
     * @param  array{
     *     client_profile_id:int,
     *     treatment_id:int,
     *     practitioner_profile_id:int,
     *     branch_id:int,
     *     starts_at:string|\DateTimeInterface,
     *     booked_by_user_id?:int|null,
     *     client_notes?:string|null,
     *     consultation_answers?:array|null,
     *     status?:string|null
     * }  $data
     */
    public function execute(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            $treatment = Treatment::query()->lockForUpdate()->findOrFail($data['treatment_id']);
            $practitioner = PractitionerProfile::query()->lockForUpdate()->findOrFail($data['practitioner_profile_id']);
            ClientProfile::query()->findOrFail($data['client_profile_id']);

            if (! $treatment->is_active || ! $practitioner->is_active) {
                throw new InvalidArgumentException('Treatment or practitioner is unavailable.');
            }

            $startsAt = CarbonImmutable::parse($data['starts_at'])->utc();
            $endsAt = $startsAt->addMinutes(
                $treatment->duration_minutes + $treatment->buffer_after_minutes
            );

            $conflict = Appointment::query()
                ->where('practitioner_profile_id', $practitioner->id)
                ->whereNotIn('status', [
                    AppointmentStatus::Cancelled->value,
                    AppointmentStatus::Refunded->value,
                    AppointmentStatus::NoShow->value,
                ])
                ->where('starts_at', '<', $endsAt)
                ->where('ends_at', '>', $startsAt->subMinutes($treatment->buffer_before_minutes))
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                throw new InvalidArgumentException('Selected time slot is no longer available.');
            }

            $price = (float) $treatment->effectivePrice();
            $deposit = (float) ($treatment->deposit_amount ?? round($price * (config('clinic.booking.deposit_percent', 30) / 100), 2));

            $appointment = Appointment::create([
                'reference' => 'APT-'.strtoupper(Str::random(10)),
                'client_profile_id' => $data['client_profile_id'],
                'treatment_id' => $treatment->id,
                'practitioner_profile_id' => $practitioner->id,
                'branch_id' => $data['branch_id'],
                'booked_by_user_id' => $data['booked_by_user_id'] ?? null,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => $data['status'] ?? AppointmentStatus::AwaitingPayment->value,
                'price' => $price,
                'deposit_amount' => $deposit,
                'amount_paid' => 0,
                'currency' => config('clinic.currency', 'GHS'),
                'client_notes' => $data['client_notes'] ?? null,
                'consultation_answers' => $data['consultation_answers'] ?? null,
            ]);

            AppointmentStatusHistory::create([
                'appointment_id' => $appointment->id,
                'from_status' => null,
                'to_status' => $appointment->status->value,
                'changed_by' => $data['booked_by_user_id'] ?? null,
                'notes' => 'Appointment created',
            ]);

            return $appointment->fresh(['treatment', 'practitioner', 'branch', 'clientProfile']);
        });
    }
}
