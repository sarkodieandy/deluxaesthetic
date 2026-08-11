<?php

namespace App\Http\Requests\Admin\Academy;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return (bool) ($user && ($user->can('courses.create') || $user->can('courses.update')));
    }

    public function rules(): array
    {
        return [
            'category_name' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'entry_requirements' => ['nullable', 'string'],
            'duration_hours' => ['required', 'integer', 'min:1'],
            'venue' => ['nullable', 'string', 'max:190'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'waiting_list_capacity' => ['nullable', 'integer', 'min:0'],
            'fee' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'in:GHS,USD'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:10000'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0', 'lte:fee'],
            'curriculum_outline' => ['nullable', 'string', 'max:20000'],
            'seo_title' => ['nullable', 'string', 'max:190'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
