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

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'phone' => trim((string) $this->input('phone')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Rules\Password::min(8)],
            'privacy_consent' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'privacy_consent.accepted' => __('web.student_portal.privacy_required'),
            'email.unique' => 'An account already exists with this email. Please sign in instead.',
            'password.min' => 'Your password must contain at least 8 characters.',
        ];
    }
}
