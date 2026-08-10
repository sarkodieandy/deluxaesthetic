<?php

namespace App\Http\Requests\Appointments;

use App\Models\Branch;
use App\Models\PractitionerProfile;
use App\Models\Treatment;
use App\Services\Appointments\AvailabilityService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isAuthenticatedClient = $this->user()?->hasRole('Client') === true;

        return [
            'guest_name' => [Rule::requiredIf(! $isAuthenticatedClient), 'nullable', 'string', 'max:120'],
            'guest_email' => [Rule::requiredIf(! $isAuthenticatedClient), 'nullable', 'email', 'max:255'],
            'guest_phone' => [Rule::requiredIf(! $isAuthenticatedClient), 'nullable', 'string', 'max:40'],
            'treatment_id' => ['required', 'exists:treatments,id'],
            'practitioner_profile_id' => ['required', 'exists:practitioner_profiles,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'starts_at' => ['required', 'date', 'after:now'],
            'goals' => ['required', 'string', 'max:2000'],
            'client_notes' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $treatmentId = $this->integer('treatment_id');
            $practitionerId = $this->integer('practitioner_profile_id');

            $branchId = $this->integer('branch_id');

            if (! $treatmentId || ! $practitionerId || ! $branchId || $validator->errors()->isNotEmpty()) {
                return;
            }

            $treatmentIsPublished = Treatment::query()
                ->whereKey($treatmentId)
                ->where('is_active', true)
                ->whereHas('category', fn ($category) => $category->where('is_active', true))
                ->exists();

            if (! $treatmentIsPublished) {
                $validator->errors()->add('treatment_id', 'The selected treatment is not available.');

                return;
            }

            $practitionerIsAssigned = PractitionerProfile::query()
                ->whereKey($practitionerId)
                ->where('is_active', true)
                ->whereHas('treatments', fn ($treatments) => $treatments->whereKey($treatmentId))
                ->exists();

            if (! $practitionerIsAssigned) {
                $validator->errors()->add('practitioner_profile_id', 'Choose a practitioner assigned to this treatment.');

                return;
            }

            if (! Branch::query()->whereKey($branchId)->where('is_active', true)->exists()) {
                $validator->errors()->add('branch_id', 'The selected clinic branch is not available.');

                return;
            }

            $timezone = config('clinic.timezone', 'Africa/Accra');
            $requestedStart = CarbonImmutable::parse((string) $this->input('starts_at'), $timezone);
            $available = app(AvailabilityService::class)
                ->slotsForDate($practitionerId, $treatmentId, $branchId, $requestedStart)
                ->contains(fn (array $slot) => CarbonImmutable::parse($slot['starts_at'])
                    ->equalTo($requestedStart));

            if (! $available) {
                $validator->errors()->add('starts_at', 'Choose one of the currently available appointment times.');
            }
        }];
    }
}
