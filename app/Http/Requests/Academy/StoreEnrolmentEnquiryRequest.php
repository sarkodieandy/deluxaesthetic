<?php

namespace App\Http\Requests\Academy;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreEnrolmentEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'preferred_channel' => ['required', 'in:whatsapp,phone,email'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'course_id' => ['nullable', 'integer', Rule::exists('courses', 'id')->where(
                fn ($query) => $query->where('is_active', true)->whereNull('deleted_at')
            )],
            'course_interest' => ['nullable', 'string', 'max:255'],
            'professional_background' => ['nullable', 'string', 'max:500'],
            'message' => ['required', 'string', 'max:3000'],
            'consent' => ['accepted'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $courseId = $this->integer('course_id');
                if (! $courseId || $validator->errors()->has('course_id')) {
                    return;
                }

                if (! Course::query()
                    ->whereKey($courseId)
                    ->where('is_active', true)
                    ->whereHas('category', fn ($query) => $query->where('is_active', true))
                    ->exists()) {
                    $validator->errors()->add('course_id', 'The selected course is not currently open for applications.');
                }
            },
        ];
    }
}
