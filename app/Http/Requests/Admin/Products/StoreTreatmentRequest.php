<?php

namespace App\Http\Requests\Admin\Products;

use Illuminate\Foundation\Http\FormRequest;

class StoreTreatmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return (bool) ($user && (
            $user->can('treatments.create')
            || $user->can('treatments.update')
            || $user->can('content.manage')
            || $user->hasAnyRole(['Super Administrator', 'Clinic Administrator'])
        ));
    }

    public function rules(): array
    {
        return [
            'category_name' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:160'],
            'short_description' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'price' => ['required', 'numeric', 'min:0'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'recommended_sessions' => ['nullable', 'integer', 'min:1', 'max:50'],
            'benefits' => ['nullable', 'string'],
            'preparation_instructions' => ['nullable', 'string'],
            'aftercare_instructions' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
