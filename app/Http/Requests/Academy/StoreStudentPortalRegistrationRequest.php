<?php

namespace App\Http\Requests\Academy;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class StoreStudentPortalRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! $this->user()?->hasRole('Student');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'privacy_consent' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'privacy_consent.accepted' => __('web.student_portal.privacy_required'),
        ];
    }
}
