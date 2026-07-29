<?php

namespace App\Http\Requests\Admin\Clinic;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePractitionerScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('practitioner_schedules.manage') === true;
    }

    public function rules(): array
    {
        return [
            'practitioner_profile_id' => ['required', 'exists:practitioner_profiles,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'starts_at' => ['required', 'date_format:H:i'],
            'ends_at' => ['required', 'date_format:H:i', 'after:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
