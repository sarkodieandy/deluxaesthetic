<?php

namespace App\Http\Requests\Admin\Content;

use App\Models\GalleryItem;
use App\Support\GalleryMedia;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
        $imageUploadRule = [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp,avif',
            'max:'.$this->maxUploadKilobytes(),
        ];

        return [
            'title' => ['required', 'string', 'max:190'],
            'type' => ['required', 'in:gallery,before_after'],
            'location_group' => ['nullable', Rule::in(array_keys(GalleryItem::LOCATION_GROUPS))],
            'treatment_id' => [
                'nullable',
                Rule::exists('treatments', 'id')->whereNull('deleted_at'),
            ],
            'description' => ['nullable', 'string'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'image' => $imageUploadRule,
            'image_url' => $imageUrlRule,
            'before_image' => $imageUploadRule,
            'before_image_url' => $imageUrlRule,
            'after_image' => $imageUploadRule,
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
                $hasImage = $this->file('image') !== null
                    || GalleryMedia::normalizeUrl($this->input('image_url'))
                    || ($existing instanceof GalleryItem && $existing->image_path);

                if (! $hasImage) {
                    $validator->errors()->add('image', 'Upload an image from your device or paste an image URL.');
                }
            }

            if ($type === 'before_after') {
                $hasBefore = $this->file('before_image') !== null
                    || GalleryMedia::normalizeUrl($this->input('before_image_url'))
                    || ($existing instanceof GalleryItem && $existing->before_image_path);

                $hasAfter = $this->file('after_image') !== null
                    || GalleryMedia::normalizeUrl($this->input('after_image_url'))
                    || ($existing instanceof GalleryItem && $existing->after_image_path);

                if (! $hasBefore && ! $validator->errors()->has('before_image')) {
                    $validator->errors()->add('before_image', 'Provide a before image (upload or URL).');
                }

                if (! $hasAfter && ! $validator->errors()->has('after_image')) {
                    $validator->errors()->add('after_image', 'Provide an after image (upload or URL).');
                }
            }
        });
    }

    public function messages(): array
    {
        $maximumMegabytes = (int) ceil($this->maxUploadKilobytes() / 1024);

        return [
            'image.max' => "The gallery image must not be larger than {$maximumMegabytes} MB.",
            'before_image.max' => "The before image must not be larger than {$maximumMegabytes} MB.",
            'after_image.max' => "The after image must not be larger than {$maximumMegabytes} MB.",
            'image.uploaded' => 'The gallery image could not be uploaded. Please try the image again.',
            'before_image.uploaded' => 'The before image could not be uploaded. Please try the image again.',
            'after_image.uploaded' => 'The after image could not be uploaded. Please try the image again.',
        ];
    }

    private function maxUploadKilobytes(): int
    {
        return max(1024, (int) config('media.max_upload_kb', 102400));
    }
}
