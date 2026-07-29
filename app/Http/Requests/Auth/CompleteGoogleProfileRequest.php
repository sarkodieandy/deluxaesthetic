<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class CompleteGoogleProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'max:50'],
            'terms_accepted' => ['accepted'],
            'privacy_accepted' => ['accepted'],
            'marketing_email_opt_in' => ['sometimes', 'boolean'],
        ];
    }
}
