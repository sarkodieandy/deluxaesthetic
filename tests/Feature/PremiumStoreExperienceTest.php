<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PremiumStoreExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_and_product_pages_render_the_premium_commerce_experience(): void
    {
        $category = ProductCategory::create([
            'name' => 'Professional Skincare',
            'slug' => 'professional-skincare',
            'is_active' => true,
        ]);

        $product = Product::create([
            'product_category_id' => $category->id,
            'name' => 'Clinical Radiance Cream',
            'slug' => 'clinical-radiance-cream',
            'sku' => 'CRC-001',
            'description' => 'A clinic-selected daily moisturiser.',
            'usage_instructions' => 'Apply to cleansed skin morning and evening.',
            'ingredients' => 'Niacinamide, ceramides and hyaluronic acid.',
            'price' => 320,
            'stock_quantity' => 8,
            'delivery_eligible' => true,
            'pickup_eligible' => true,
            'is_featured' => true,
            'is_active' => true,
        ]);

        Product::create([
            'product_category_id' => $category->id,
            'name' => 'Daily Renewal Serum',
            'slug' => 'daily-renewal-serum',
            'sku' => 'DRS-001',
            'description' => 'A complementary serum.',
            'price' => 260,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $this->get(route('web.store.index'))
            ->assertOk()
            ->assertSee('store-v2-hero', false)
            ->assertSee('Clinical Radiance Cream');

        $this->get(route('web.store.show', $product->slug))
            ->assertOk()
            ->assertSee('product-v3__layout', false)
            ->assertSee('Clinical Radiance Cream')
            ->assertSee('Add to cart')
            ->assertSee('How to use')
            ->assertSee('Ingredients')
            ->assertSee('Related products')
            ->assertSee('Daily Renewal Serum');
    }
}
