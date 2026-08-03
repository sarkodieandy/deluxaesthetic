<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class SaveBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('blog.manage');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->input('title')),
            'category' => trim((string) $this->input('category')),
            'status' => $this->input('status', 'draft'),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:190'],
            'category' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string', 'min:50'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'cover_image_url' => ['nullable', 'url:http,https', 'max:2000'],
            'cover_image_alt' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['sometimes', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:170'],
        ];
    }
}
