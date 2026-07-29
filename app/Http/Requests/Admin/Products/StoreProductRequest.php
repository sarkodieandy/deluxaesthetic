<?php

namespace App\Http\Requests\Admin\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && $user->can('products.manage');
    }

    public function rules(): array
    {
        return [
            'product_category_id' => [
                'required',
                Rule::exists('product_categories', 'id')->where(fn ($q) => $q->where('is_active', true)->whereNull('deleted_at')),
            ],
            'name' => ['required', 'string', 'max:160'],
            'sku' => ['required', 'string', 'max:120', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'usage_instructions' => ['nullable', 'string'],
            'ingredients' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'weight_kg' => ['nullable', 'numeric', 'min:0'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'seo_title' => ['nullable', 'string', 'max:190'],
            'seo_description' => ['nullable', 'string', 'max:400'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'delivery_eligible' => ['sometimes', 'boolean'],
            'pickup_eligible' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Please upload a product photo (JPG, PNG, or WebP, max 2MB).',
            'image.image' => 'The product photo must be a valid image file.',
            'image.mimes' => 'Use JPG, PNG, or WebP for the product photo.',
            'image.max' => 'The product photo must be 2MB or smaller.',
        ];
    }
}
