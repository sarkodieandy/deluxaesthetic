<?php

namespace App\Http\Requests\Admin\Clinic;

use Illuminate\Foundation\Http\FormRequest;

class StorePractitionerBlockedDateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('practitioner_schedules.manage') === true;
    }

    public function rules(): array
    {
        return [
            'practitioner_profile_id' => ['required', 'exists:practitioner_profiles,id'],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after_or_equal:starts_on'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
