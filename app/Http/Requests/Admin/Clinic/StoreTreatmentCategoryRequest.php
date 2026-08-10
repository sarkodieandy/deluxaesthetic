<?php

namespace App\Http\Requests\Admin\Clinic;

use Illuminate\Foundation\Http\FormRequest;

class StoreTreatmentCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('treatments.create');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1200'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'image_url' => ['nullable', 'url', 'max:255', 'regex:/^https?:\/\//i'],
            'remove_image' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
