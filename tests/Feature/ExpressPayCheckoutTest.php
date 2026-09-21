<?php

namespace Tests\Feature;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Cart;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ExpressPayCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'expresspay-test-token-1234567890';

    private array $queryOverrides = [];

    private int $queryHttpStatus = 200;

    private bool $rejectInitiation = false;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            // Keep the feature-test cookie host aligned with Laravel's default test host.
            'app.url' => 'http://localhost',
            'ecommerce.whatsapp_checkout' => false,
            'ecommerce.allow_guest_checkout' => true,
            'ecommerce.currency' => 'GHS',
            'ecommerce.delivery_enabled' => true,
            'ecommerce.delivery_fee' => 25,
            'ecommerce.free_delivery_threshold' => null,
            'ecommerce.tax_percent' => 0,
            'payments.mock' => true,
            'payments.store_driver' => 'expresspay',
            'payments.store_mock' => false,
            'payments.expresspay.environment' => 'sandbox',
            'payments.expresspay.merchant_id' => 'test-merchant',
            'payments.expresspay.api_key' => 'test-api-key',
            'payments.expresspay.callback_url' => null,
            'payments.expresspay.post_url' => null,
        ]);

        Http::preventStrayRequests();
        Http::fake([
            'https://sandbox.expresspaygh.com/api/submit.php' => function (Request $request) {
                return Http::response($this->rejectInitiation ? [
                    'status' => 0,
                    'message' => 'Payment initiation rejected',
                ] : [
                    'status' => 1,
                    'order-id' => $request['order-id'],
                    'token' => self::TOKEN,
                ]);
            },
            'https://sandbox.expresspaygh.com/api/query.php' => function () {
                $payment = Payment::query()->latest('id')->firstOrFail();

                return Http::response(array_replace([
                    'result' => 1,
                    'result-text' => 'Approved',
                    'order-id' => $payment->reference,
                    'token' => self::TOKEN,
                    'amount' => '325.00',
                    'currency' => 'GHS',
                    'transaction-id' => 'EXP-TRANSACTION-123',
                ], $this->queryOverrides), $this->queryHttpStatus);
            },
        ]);
    }

    public function test_guest_checkout_submits_the_server_total_and_redirects_to_expresspay(): void
    {
        [$payment, $order, $product, $cart] = $this->startCheckout();

        $this->assertNull($order->user_id);
        $this->assertSame('customer@example.test', $order->guest_email);
        $this->assertSame('expresspay', $payment->gateway);
        $this->assertSame(self::TOKEN, $payment->metadata['expresspay_token']);
        $this->assertSame($cart->id, $payment->metadata['cart_id']);
        $this->assertSame('325.00', $payment->amount);
        $this->assertSame(OrderPaymentStatus::Pending, $order->payment_status);
        $this->assertSame(10, $product->fresh()->stock_quantity);

        Http::assertSent(fn (Request $request) => $request->url() === 'https://sandbox.expresspaygh.com/api/submit.php'
            && $request->method() === 'POST'
            && $request['merchant-id'] === 'test-merchant'
            && $request['api-key'] === 'test-api-key'
            && $request['order-id'] === $payment->reference
            && (float) $request['amount'] === 325.0
            && $request['currency'] === 'GHS'
            && $request['email'] === 'customer@example.test'
        );
        Http::assertSentCount(1);
    }

    public function test_pending_callback_then_webhook_completes_guest_order_without_the_checkout_session(): void
    {
        [$payment, $order, $product, $cart] = $this->startCheckout();
        $this->queryOverrides = ['result' => 4, 'result-text' => 'Pending'];

        $this->get($this->callbackUrl($payment))
            ->assertRedirect(route('web.checkout.processing', $order->number));

        $this->assertSame(PaymentStatus::Pending, $payment->fresh()->status);
        $this->assertSame(OrderPaymentStatus::Pending, $order->fresh()->payment_status);
        $this->assertSame(10, $product->fresh()->stock_quantity);
        $this->assertSame(1, $cart->items()->count());

        session()->invalidate();
        $this->assertNotSame($cart->session_id, session()->getId());
        $this->queryOverrides = [];

        $this->post(route('api.webhooks.expresspay'), $this->notification($payment))
            ->assertOk();

        $this->assertPaid($payment, $order, $product);
        $this->assertSame(0, $cart->items()->count());
    }

    public function test_duplicate_callbacks_and_webhooks_only_fulfill_the_order_once(): void
    {
        [$payment, $order, $product] = $this->startCheckout();

        $this->get($this->callbackUrl($payment))
            ->assertRedirect(route('web.checkout.success', $order->number));
        $this->post(route('api.webhooks.expresspay'), $this->notification($payment))->assertOk();
        $this->get($this->callbackUrl($payment))
            ->assertRedirect(route('web.checkout.success', $order->number));

        $this->assertPaid($payment, $order, $product);
        $this->assertSame(1, InventoryMovement::query()
            ->where('reference_type', Order::class)
            ->where('reference_id', $order->id)
            ->where('reason', 'sale')
            ->count());
        $this->assertSame(1, $order->statusHistories()
            ->where('to_status', OrderStatus::Paid->value)
            ->count());
    }

    public function test_wrong_tokens_are_rejected_before_contacting_the_provider(): void
    {
        [$payment, $order, $product] = $this->startCheckout();

        $this->get(route('web.checkout.callback', [
            'order-id' => $payment->reference,
            'token' => 'wrong-token',
        ]))->assertForbidden();
        $this->post(route('api.webhooks.expresspay'), [
            'order-id' => $payment->reference,
            'token' => 'wrong-token',
        ])->assertForbidden();

        Http::assertSentCount(1);
        $this->assertUnpaid($payment, $order, $product);
    }

    public function test_malformed_callback_and_webhook_are_rejected(): void
    {
        $this->getJson(route('web.checkout.callback'))->assertUnprocessable();
        $this->post(route('api.webhooks.expresspay'), ['order-id' => ['invalid']])
            ->assertUnprocessable();

        Http::assertNothingSent();
    }

    #[DataProvider('mismatchedPaymentDetails')]
    public function test_verified_payment_details_must_match_the_order(array $overrides): void
    {
        [$payment, $order, $product] = $this->startCheckout();
        $this->queryOverrides = $overrides;

        $this->get($this->callbackUrl($payment))
            ->assertRedirect(route('web.checkout.failure', $payment->reference));

        $this->assertUnpaid($payment, $order, $product);
    }

    public static function mismatchedPaymentDetails(): array
    {
        return [
            'different order reference' => [['order-id' => 'PAY-ANOTHER-ORDER']],
            'underpayment' => [['amount' => '3.25']],
            'wrong currency' => [['currency' => 'USD']],
            'different provider token' => [['token' => 'another-provider-token']],
        ];
    }

    public function test_declined_callback_persists_failed_status_without_fulfilling_the_order(): void
    {
        [$payment, $order, $product] = $this->startCheckout();
        $this->queryOverrides = ['result' => 2, 'result-text' => 'Declined'];

        $this->get($this->callbackUrl($payment))
            ->assertRedirect(route('web.checkout.failure', $payment->reference));

        $this->assertSame(PaymentStatus::Failed, $payment->fresh()->status);
        $this->assertSame(OrderPaymentStatus::Failed, $order->fresh()->payment_status);
        $this->assertTrue($payment->attempts()->where('status', PaymentStatus::Failed->value)->exists());
        $this->assertUnpaid($payment, $order, $product);
    }

    #[DataProvider('unpaidWebhookResults')]
    public function test_webhook_acknowledges_pending_and_declined_payments_without_fulfilling(
        int $result,
        PaymentStatus $paymentStatus,
        OrderPaymentStatus $orderPaymentStatus,
    ): void {
        [$payment, $order, $product] = $this->startCheckout();
        $this->queryOverrides = ['result' => $result];

        $this->post(route('api.webhooks.expresspay'), $this->notification($payment))->assertOk();

        $this->assertSame($paymentStatus, $payment->fresh()->status);
        $this->assertSame($orderPaymentStatus, $order->fresh()->payment_status);
        $this->assertUnpaid($payment, $order, $product);
    }

    public static function unpaidWebhookResults(): array
    {
        return [
            'pending' => [4, PaymentStatus::Pending, OrderPaymentStatus::Pending],
            'declined' => [2, PaymentStatus::Failed, OrderPaymentStatus::Failed],
        ];
    }

    public function test_provider_query_outage_returns_retryable_webhook_error_without_fulfilling(): void
    {
        [$payment, $order, $product] = $this->startCheckout();
        $this->queryHttpStatus = 503;

        $this->post(route('api.webhooks.expresspay'), $this->notification($payment))
            ->assertStatus(503);

        $this->assertUnpaid($payment, $order, $product);
    }

    public function test_failed_initiation_preserves_the_cart_and_stock(): void
    {
        $this->rejectInitiation = true;
        $product = $this->addProductToCart();

        $this->from(route('web.checkout.show'))
            ->post(route('web.checkout.pay'), $this->checkoutData())
            ->assertRedirect(route('web.checkout.show'))
            ->assertSessionHasErrors('payment');

        $this->assertSame(10, $product->fresh()->stock_quantity);
        $this->assertSame(1, Cart::query()->firstOrFail()->items()->count());
        $this->assertSame(0, Payment::query()->where('status', PaymentStatus::Successful->value)->count());
        $this->assertSame(0, Order::query()->where('payment_status', OrderPaymentStatus::Paid->value)->count());
        $this->assertDatabaseCount('inventory_movements', 0);
    }

    public function test_confirmation_uses_the_recorded_gateway_when_store_configuration_changes(): void
    {
        [$payment, $order, $product] = $this->startCheckout();
        config(['payments.store_driver' => 'paystack', 'payments.store_mock' => true]);

        $this->get($this->callbackUrl($payment))
            ->assertRedirect(route('web.checkout.success', $order->number));

        Http::assertSent(fn (Request $request) => $request->url() === 'https://sandbox.expresspaygh.com/api/query.php'
            && $request['token'] === self::TOKEN
        );
        $this->assertPaid($payment, $order, $product);
    }

    public function test_expresspay_payment_cannot_be_completed_via_mock_routes(): void
    {
        [$payment, $order, $product] = $this->startCheckout();

        $this->get(route('web.payments.mock', $payment->reference))->assertNotFound();
        $this->post(route('web.payments.mock.complete', $payment->reference))->assertNotFound();

        Http::assertSentCount(1);
        $this->assertUnpaid($payment, $order, $product);
    }

    public function test_success_url_does_not_show_confirmation_for_an_unpaid_order(): void
    {
        [$payment, $order, $product] = $this->startCheckout();

        $this->get(route('web.checkout.success', $order->number))
            ->assertRedirect(route('web.checkout.processing', $order->number));

        Http::assertSentCount(1);
        $this->assertUnpaid($payment, $order, $product);
    }

    public function test_signed_in_customer_can_pay_while_client_portal_routes_are_disabled(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->assertFalse(Route::has('client.orders.show'));
        [$payment, $order, $product, $cart] = $this->startCheckout();

        $this->get($this->callbackUrl($payment))
            ->assertRedirect(route('web.checkout.success', $order->number));

        $this->assertSame($user->id, $order->user_id);
        $this->assertSame($user->id, $payment->user_id);
        $this->assertPaid($payment, $order, $product);
        $this->assertSame(0, $cart->items()->count());
        $this->get(route('web.checkout.success', $order->number))->assertOk();
    }

    private function startCheckout(): array
    {
        $product = $this->addProductToCart();
        $cart = Cart::query()->firstOrFail();

        $this->post(route('web.checkout.pay'), [
            ...$this->checkoutData(),
            'amount' => '0.01',
            'currency' => 'USD',
        ])->assertRedirect('https://sandbox.expresspaygh.com/payment?token='.self::TOKEN);

        $payment = Payment::query()->sole();

        return [$payment, $payment->payable, $product, $cart];
    }

    private function addProductToCart(): Product
    {
        $category = ProductCategory::query()->create([
            'name' => 'Skin Care',
            'slug' => 'expresspay-skin-care',
            'is_active' => true,
        ]);
        $product = Product::query()->create([
            'product_category_id' => $category->id,
            'name' => 'Radiance Facial Cream',
            'slug' => 'expresspay-radiance-cream',
            'sku' => 'EXP-RFC-001',
            'description' => 'Professional facial cream.',
            'price' => 300,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $this->withSession(['expresspay_checkout' => true])
            ->withCookie(config('session.cookie'), session()->getId())
            ->post(route('web.cart.store'), ['product_id' => $product->id, 'quantity' => 1])
            ->assertRedirect(route('web.cart.index'));

        return $product;
    }

    private function checkoutData(): array
    {
        return [
            'name' => 'Ama Customer',
            'email' => 'customer@example.test',
            'phone' => '0241234567',
            'fulfillment_type' => 'delivery',
            'line_1' => '12 Test Street',
            'city' => 'Accra',
            'country' => 'Ghana',
            'terms' => '1',
        ];
    }

    private function notification(Payment $payment): array
    {
        return ['order-id' => $payment->reference, 'token' => self::TOKEN];
    }

    private function callbackUrl(Payment $payment): string
    {
        return route('web.checkout.callback', $this->notification($payment));
    }

    private function assertPaid(Payment $payment, Order $order, Product $product): void
    {
        $this->assertSame(PaymentStatus::Successful, $payment->fresh()->status);
        $this->assertSame('EXP-TRANSACTION-123', $payment->fresh()->provider_reference);
        $this->assertSame(OrderPaymentStatus::Paid, $order->fresh()->payment_status);
        $this->assertSame(OrderStatus::Paid, $order->fresh()->status);
        $this->assertNotNull($order->fresh()->paid_at);
        $this->assertSame(9, $product->fresh()->stock_quantity);
    }

    private function assertUnpaid(Payment $payment, Order $order, Product $product): void
    {
        $this->assertNotSame(PaymentStatus::Successful, $payment->fresh()->status);
        $this->assertNotSame(OrderPaymentStatus::Paid, $order->fresh()->payment_status);
        $this->assertNull($order->fresh()->paid_at);
        $this->assertSame(10, $product->fresh()->stock_quantity);
        $this->assertDatabaseCount('inventory_movements', 0);
    }
}
