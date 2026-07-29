<?php

namespace App\Http\Requests\Academy;

use Illuminate\Foundation\Http\FormRequest;

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
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'course_interest' => ['nullable', 'string', 'max:255'],
            'professional_background' => ['nullable', 'string', 'max:500'],
            'message' => ['required', 'string', 'max:3000'],
            'consent' => ['accepted'],
        ];
    }
}
