<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_category_id', 'name', 'slug', 'sku', 'barcode', 'description', 'usage_instructions',
        'ingredients', 'price', 'sale_price', 'cost_price', 'stock_quantity', 'low_stock_threshold',
        'weight_kg', 'delivery_eligible', 'pickup_eligible', 'is_featured', 'is_active', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'weight_kg' => 'decimal:3',
            'delivery_eligible' => 'boolean',
            'pickup_eligible' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderByDesc('is_primary');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function primaryImage(): ?ProductImage
    {
        return $this->images->firstWhere('is_primary', true) ?? $this->images->first();
    }

    public function imageUrl(): ?string
    {
        $image = $this->primaryImage();

        if (! $image?->path) {
            return null;
        }

        return str_starts_with($image->path, 'assets/')
            ? asset($image->path)
            : \Illuminate\Support\Facades\Storage::disk('public')->url($image->path);
    }

    public function effectivePrice(): string
    {
        return (string) ($this->sale_price ?? $this->price);
    }

    public function availableStock(): int
    {
        return (int) $this->stock_quantity;
    }

    public function isPurchasable(): bool
    {
        return $this->is_active && $this->availableStock() > 0;
    }
}
