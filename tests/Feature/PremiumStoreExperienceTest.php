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

    public function test_store_filters_products_by_effective_sale_price(): void
    {
        $category = ProductCategory::create([
            'name' => 'Price Filter Collection',
            'slug' => 'price-filter-collection',
            'is_active' => true,
        ]);

        Product::create([
            'product_category_id' => $category->id,
            'name' => 'Entry Cleanser',
            'slug' => 'entry-cleanser',
            'sku' => 'PRICE-100',
            'price' => 100,
            'stock_quantity' => 2,
            'is_active' => true,
        ]);

        Product::create([
            'product_category_id' => $category->id,
            'name' => 'Sale Treatment Serum',
            'slug' => 'sale-treatment-serum',
            'sku' => 'PRICE-250',
            'price' => 450,
            'sale_price' => 250,
            'stock_quantity' => 2,
            'is_active' => true,
        ]);

        Product::create([
            'product_category_id' => $category->id,
            'name' => 'Luxury Recovery Set',
            'slug' => 'luxury-recovery-set',
            'sku' => 'PRICE-900',
            'price' => 900,
            'stock_quantity' => 2,
            'is_active' => true,
        ]);

        $this->get(route('web.store.index', ['min_price' => 200, 'max_price' => 500]))
            ->assertOk()
            ->assertSee('Filter by price')
            ->assertSee('Sale Treatment Serum')
            ->assertDontSee('Entry Cleanser')
            ->assertDontSee('Luxury Recovery Set')
            ->assertSee('value="200"', false)
            ->assertSee('value="500"', false);

        $this->get(route('web.store.index', ['min_price' => 500, 'max_price' => 200]))
            ->assertOk()
            ->assertSee('Sale Treatment Serum')
            ->assertDontSee('Entry Cleanser')
            ->assertDontSee('Luxury Recovery Set');
    }

    public function test_store_ignores_invalid_price_filters(): void
    {
        $category = ProductCategory::create([
            'name' => 'Visible Collection',
            'slug' => 'visible-collection',
            'is_active' => true,
        ]);

        Product::create([
            'product_category_id' => $category->id,
            'name' => 'Visible Product',
            'slug' => 'visible-product',
            'sku' => 'VISIBLE-001',
            'price' => 300,
            'stock_quantity' => 2,
            'is_active' => true,
        ]);

        $this->get(route('web.store.index', ['min_price' => 'invalid', 'max_price' => -50]))
            ->assertOk()
            ->assertSee('Visible Product');
    }
}
