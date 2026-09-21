<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use App\Support\WhatsAppOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreOnlineCheckoutViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ecommerce.whatsapp_checkout' => false,
            'payments.store_driver' => 'expresspay',
            'payments.store_mock' => false,
        ]);
    }

    public function test_store_and_product_offer_online_purchases(): void
    {
        $product = $this->product();

        $this->get(route('web.store.index'))
            ->assertOk()
            ->assertSee('Secure online checkout')
            ->assertSee('Buy now')
            ->assertDontSee('Order on WhatsApp')
            ->assertDontSee(WhatsAppOrder::productUrl($product), false);

        $this->get(route('web.store.show', $product->slug))
            ->assertOk()
            ->assertSee('Buy now')
            ->assertSee('Add to cart')
            ->assertDontSee('Order on WhatsApp');
    }

    public function test_buy_now_and_cart_lead_to_checkout(): void
    {
        $product = $this->product();

        $this->actingAs(User::factory()->create())
            ->post(route('web.cart.store'), [
                'product_id' => $product->id,
                'quantity' => 2,
                'buy_now' => 1,
            ])->assertRedirect(route('web.checkout.show'));

        $this->get(route('web.cart.index'))
            ->assertOk()
            ->assertSee('Proceed to checkout')
            ->assertSee(route('web.checkout.show'), false)
            ->assertDontSee('Order on WhatsApp');
    }

    public function test_checkout_identifies_expresspay_and_separately_labels_demo_mode(): void
    {
        $this->fillCart();

        $this->get(route('web.checkout.show'))
            ->assertOk()
            ->assertSee('Pay with expressPay')
            ->assertDontSee('No money will be charged')
            ->assertDontSee('Continue on WhatsApp');

        config(['payments.store_mock' => true]);

        $this->get(route('web.checkout.show'))
            ->assertOk()
            ->assertSee('Continue to demo payment')
            ->assertSee('No money will be charged')
            ->assertDontSee('Pay with expressPay');
    }

    public function test_checkout_preserves_paystack_label_when_selected(): void
    {
        config(['payments.store_driver' => 'paystack']);
        $this->fillCart();

        $this->get(route('web.checkout.show'))
            ->assertOk()
            ->assertSee('Pay with Paystack')
            ->assertDontSee('Pay with expressPay');
    }

    private function fillCart(): void
    {
        $product = $this->product();

        $this->actingAs(User::factory()->create())
            ->post(route('web.cart.store'), [
                'product_id' => $product->id,
                'quantity' => 1,
            ])->assertRedirect(route('web.cart.index'));
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
            'sku' => 'RFC-ONLINE-001',
            'description' => 'Professional facial cream.',
            'price' => 300,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);
    }
}
