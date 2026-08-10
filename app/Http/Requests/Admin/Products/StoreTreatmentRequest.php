<?php

namespace App\Http\Requests\Admin\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTreatmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return (bool) ($user && (
            ($this->isMethod('post') && $user->can('treatments.create'))
            || (! $this->isMethod('post') && $user->can('treatments.update'))
        ));
    }

    public function rules(): array
    {
        return [
            'treatment_category_id' => [
                'required',
                Rule::exists('treatment_categories', 'id')->whereNull('deleted_at'),
            ],
            'name' => ['required', 'string', 'max:160'],
            'short_description' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'recovery_days' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'price' => ['required', 'numeric', 'min:0'],
            'promotional_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'recommended_sessions' => ['nullable', 'integer', 'min:1', 'max:50'],
            'buffer_before_minutes' => ['nullable', 'integer', 'min:0', 'max:240'],
            'buffer_after_minutes' => ['nullable', 'integer', 'min:0', 'max:240'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'benefits' => ['nullable', 'string'],
            'suitable_candidates' => ['nullable', 'string'],
            'contraindications' => ['nullable', 'string'],
            'preparation_instructions' => ['nullable', 'string'],
            'aftercare_instructions' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'image_url' => ['nullable', 'url', 'max:255', 'regex:/^https?:\/\//i'],
            'remove_image' => ['sometimes', 'boolean'],
            'practitioner_profile_ids' => ['nullable', 'array'],
            'practitioner_profile_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('practitioner_profiles', 'id')->whereNull('deleted_at'),
            ],
            'seo_title' => ['nullable', 'string', 'max:190'],
            'seo_description' => ['nullable', 'string', 'max:400'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
