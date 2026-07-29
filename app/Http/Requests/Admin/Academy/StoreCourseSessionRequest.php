<?php

namespace App\Http\Requests\Admin\Academy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('courses.update');
    }

    public function rules(): array
    {
        return [
            'session_date' => ['required', 'date'],
            'starts_at' => ['nullable', 'date_format:H:i'],
            'ends_at' => ['nullable', 'date_format:H:i', 'after:starts_at'],
            'topic' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['scheduled', 'rescheduled', 'cancelled', 'holiday'])],
            'is_practical' => ['sometimes', 'boolean'],
            'is_assessment' => ['sometimes', 'boolean'],
            'announcement' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
