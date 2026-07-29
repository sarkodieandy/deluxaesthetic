<?php

namespace App\Http\Requests\Admin\Clients;

use App\Enums\AppointmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return (bool) ($user && ($user->can('appointments.update') || $user->can('appointments.approve') || $user->can('appointments.cancel') || $user->can('appointments.complete')));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_map(fn ($case) => $case->value, AppointmentStatus::cases()))],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
            'cancellation_reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
