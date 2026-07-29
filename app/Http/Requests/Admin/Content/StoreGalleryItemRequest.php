<?php

namespace App\Http\Requests\Admin\Content;

use App\Models\GalleryItem;
use App\Support\GalleryMedia;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreGalleryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && ($user->can('gallery.manage') || $user->can('content.manage'));
    }

    public function rules(): array
    {
        $imageUrlRule = ['nullable', 'url', 'max:2000', 'regex:/^https?:\/\//i'];

        return [
            'title' => ['required', 'string', 'max:190'],
            'type' => ['required', 'in:gallery,before_after'],
            'description' => ['nullable', 'string'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'image_url' => $imageUrlRule,
            'before_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'before_image_url' => $imageUrlRule,
            'after_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'after_image_url' => $imageUrlRule,
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (Validator $validator) {
            /** @var GalleryItem|null $existing */
            $existing = $this->route('gallery');
            $type = $this->input('type');

            if ($type === 'gallery') {
                $hasImage = $this->hasFile('image')
                    || GalleryMedia::normalizeUrl($this->input('image_url'))
                    || ($existing instanceof GalleryItem && $existing->image_path);

                if (! $hasImage) {
                    $validator->errors()->add('image', 'Upload an image from your device or paste an image URL.');
                }
            }

            if ($type === 'before_after') {
                $hasBefore = $this->hasFile('before_image')
                    || GalleryMedia::normalizeUrl($this->input('before_image_url'))
                    || ($existing instanceof GalleryItem && $existing->before_image_path);

                $hasAfter = $this->hasFile('after_image')
                    || GalleryMedia::normalizeUrl($this->input('after_image_url'))
                    || ($existing instanceof GalleryItem && $existing->after_image_path);

                if (! $hasBefore) {
                    $validator->errors()->add('before_image', 'Provide a before image (upload or URL).');
                }

                if (! $hasAfter) {
                    $validator->errors()->add('after_image', 'Provide an after image (upload or URL).');
                }
            }
        });
    }
}
