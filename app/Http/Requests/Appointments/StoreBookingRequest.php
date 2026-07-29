<?php

namespace App\Http\Requests\Appointments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
}
