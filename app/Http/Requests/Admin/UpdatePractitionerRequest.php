<?php

namespace App\Http\Requests\Admin;

use App\Models\PractitionerProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePractitionerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Super Administrator', 'Clinic Administrator']) ?? false;
    }

    public function rules(): array
    {
        /** @var PractitionerProfile $practitioner */
        $practitioner = $this->route('practitioner');

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:190',
                Rule::unique('users', 'email')->ignore($practitioner->user_id),
            ],
            'phone' => ['nullable', 'string', 'max:40'],
            'title' => ['nullable', 'string', 'max:160'],
            'professional_title' => ['required', 'string', 'max:160'],
            'biography' => ['nullable', 'string', 'max:5000'],
            'years_experience' => ['nullable', 'integer', 'min:0', 'max:60'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'is_ceo' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_twitter' => ['nullable', 'url', 'max:255'],
            'social_linkedin' => ['nullable', 'url', 'max:255'],
        ];
    }
}
