<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use App\Support\WhatsAppOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ecommerce.whatsapp_checkout' => true,
            'ecommerce.whatsapp_number' => '+233 55 224 8636',
            'clinic.ceo.name' => 'Dr Evelyn Ejaife',
        ]);
    }

    public function test_store_promotes_whatsapp_instead_of_paystack(): void
    {
        $product = $this->product();

        $this->get(route('web.store.index'))
            ->assertOk()
            ->assertSee('Order directly on WhatsApp')
            ->assertSee('Order on WhatsApp')
            ->assertSee(WhatsAppOrder::productUrl($product), false)
            ->assertDontSee('Paystack');
    }

    public function test_buy_now_opens_prefilled_whatsapp_order(): void
    {
        $product = $this->product();

        $this->post(route('web.cart.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
            'buy_now' => 1,
        ])->assertRedirect(WhatsAppOrder::productUrl($product, 2));
    }

    public function test_checkout_url_redirects_cart_to_whatsapp(): void
    {
        $product = $this->product();
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('web.cart.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertRedirect(route('web.cart.index'));

        $response = $this->get(route('web.checkout.show'));
        $response->assertRedirect();

        $location = (string) $response->headers->get('Location');
        $this->assertStringContainsString('https://wa.me/233552248636', $location);
        $this->assertStringContainsString(rawurlencode($product->name), $location);
    }

    private function product(): Product
    {
        $category = ProductCategory::query()->create([
            'name' => 'Skin Care',
            'slug' => 'skin-care',
            'is_active' => true,
        ]);

        return Product::query()->create([
            'product_category_id' => $category->id,
            'name' => 'Radiance Facial Cream',
            'slug' => 'radiance-facial-cream',
            'sku' => 'RFC-001',
            'description' => 'Professional facial cream.',
            'price' => 300,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);
    }
}
