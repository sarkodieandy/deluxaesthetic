<?php

namespace App\Services\Appointments;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\PractitionerBlockedDate;
use App\Models\PractitionerSchedule;
use App\Models\PractitionerProfile;
use App\Models\Treatment;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class AvailabilityService
{
    public function slotsForDate(
        int $practitionerProfileId,
        int $treatmentId,
        int $branchId,
        CarbonImmutable $date
    ): Collection {
        $timezone = config('clinic.timezone', 'Africa/Accra');
        $date = $date->timezone($timezone)->startOfDay();
        $treatment = Treatment::query()
            ->where('is_active', true)
            ->whereHas('category', fn ($category) => $category->where('is_active', true))
            ->findOrFail($treatmentId);

        if (! Branch::query()->whereKey($branchId)->where('is_active', true)->exists()) {
            return collect();
        }

        $isAssigned = PractitionerProfile::query()
            ->whereKey($practitionerProfileId)
            ->where('is_active', true)
            ->whereHas('user', fn ($user) => $user->where('is_active', true))
            ->whereHas('treatments', fn ($treatments) => $treatments->whereKey($treatment->id))
            ->exists();

        if (! $isAssigned) {
            return collect();
        }

        if ($this->isBlocked($practitionerProfileId, $date)) {
            return collect();
        }

        $schedules = PractitionerSchedule::query()
            ->where('practitioner_profile_id', $practitionerProfileId)
            ->where('branch_id', $branchId)
            ->where('day_of_week', $date->dayOfWeek)
            ->where('is_active', true)
            ->get();

        if ($schedules->isEmpty()) {
            return collect();
        }

        $slotLength = $treatment->duration_minutes + $treatment->buffer_after_minutes;
        $existing = Appointment::query()
            ->where('practitioner_profile_id', $practitionerProfileId)
            ->whereDate('starts_at', $date->toDateString())
            ->whereNotIn('status', [
                AppointmentStatus::Cancelled->value,
                AppointmentStatus::Refunded->value,
            ])
            ->get(['starts_at', 'ends_at']);

        $slots = collect();
        $now = CarbonImmutable::now($timezone);

        foreach ($schedules as $schedule) {
            $cursor = $date->setTimeFromTimeString((string) $schedule->starts_at);
            $windowEnd = $date->setTimeFromTimeString((string) $schedule->ends_at);

            while ($cursor->addMinutes($slotLength)->lessThanOrEqualTo($windowEnd)) {
                $slotStart = $cursor;
                $slotEnd = $slotStart->addMinutes($slotLength);

                $overlaps = $existing->contains(function ($appointment) use ($slotStart, $slotEnd, $treatment) {
                    $bufferedStart = $slotStart->subMinutes($treatment->buffer_before_minutes);

                    return $appointment->starts_at < $slotEnd
                        && $appointment->ends_at > $bufferedStart;
                });

                if (! $overlaps && $slotStart->greaterThan($now)) {
                    $slots->push([
                        'starts_at' => $slotStart->toIso8601String(),
                        'ends_at' => $slotEnd->toIso8601String(),
                        'label' => $slotStart->format('H:i'),
                    ]);
                }

                $cursor = $cursor->addMinutes(15);
            }
        }

        return $slots->values();
    }

    public function isBlocked(int $practitionerProfileId, CarbonImmutable $date): bool
    {
        $day = $date->toDateString();

        return PractitionerBlockedDate::query()
            ->where('practitioner_profile_id', $practitionerProfileId)
            ->whereDate('starts_on', '<=', $day)
            ->whereDate('ends_on', '>=', $day)
            ->exists();
    }
}
