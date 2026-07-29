<?php

namespace App\Http\Requests\Admin\Clinic;

use Illuminate\Foundation\Http\FormRequest;

class StorePractitionerScheduleRequest extends FormRequest
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $exists = \App\Models\PractitionerSchedule::query()
                ->where('practitioner_profile_id', $this->integer('practitioner_profile_id'))
                ->where('branch_id', $this->integer('branch_id'))
                ->where('day_of_week', $this->integer('day_of_week'))
                ->whereTime('starts_at', $this->string('starts_at')->toString())
                ->exists();

            if ($exists) {
                $validator->errors()->add('day_of_week', 'This practitioner already has that start time on this day for the selected branch.');
            }
        });
    }
}
