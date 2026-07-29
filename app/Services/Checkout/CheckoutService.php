<?php

namespace App\Services\Checkout;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\DTOs\PaymentInitiationData;
use App\Enums\FulfillmentType;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Branch;
use App\Models\Cart;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\User;
use App\Services\Cart\CartService;
use App\Services\Inventory\InventoryService;
use App\Services\Notifications\InAppNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CheckoutService
{
    public function __construct(
        private readonly CartService $carts,
        private readonly OrderPricingService $pricing,
        private readonly PaymentGatewayInterface $payments,
        private readonly InventoryService $inventory,
        private readonly InAppNotificationService $notifications,
    ) {}

    public function placePendingOrder(Cart $cart, array $contact, array $address, string $fulfillmentType, ?int $branchId, ?User $user): array
    {
        $this->carts->revalidate($cart);
        $cart->refresh()->load(['items.product']);

        if ($cart->items->isEmpty()) {
            throw new InvalidArgumentException('Your cart is empty.');
        }

        if (! in_array($fulfillmentType, [FulfillmentType::Delivery->value, FulfillmentType::Pickup->value], true)) {
            throw new InvalidArgumentException('Choose delivery or pickup.');
        }

        if ($fulfillmentType === FulfillmentType::Pickup->value) {
            if (! $branchId || ! Branch::query()->whereKey($branchId)->where('is_active', true)->exists()) {
                throw new InvalidArgumentException('Select a pickup branch.');
            }
        }

        if (! $user && ! config('ecommerce.allow_guest_checkout')) {
            throw new InvalidArgumentException('Please sign in to checkout.');
        }

        $quote = $this->pricing->quote($cart, $fulfillmentType);

        return DB::transaction(function () use ($cart, $contact, $address, $fulfillmentType, $branchId, $user, $quote) {
            foreach ($cart->items as $item) {
                $stock = $item->product?->availableStock() ?? 0;
                if (! $item->product?->is_active || $stock < $item->quantity) {
                    throw new InvalidArgumentException(($item->product?->name ?? 'A product').' is no longer available in that quantity.');
                }
            }

            $order = Order::create([
                'number' => $this->uniqueOrderNumber(),
                'user_id' => $user?->id,
                'guest_email' => $user ? null : ($contact['email'] ?? null),
                'guest_phone' => $user ? null : ($contact['phone'] ?? null),
                'guest_name' => $user ? null : ($contact['name'] ?? null),
                'status' => OrderStatus::AwaitingPayment->value,
                'payment_status' => OrderPaymentStatus::Pending->value,
                'fulfillment_type' => $fulfillmentType,
                'branch_id' => $fulfillmentType === FulfillmentType::Pickup->value ? $branchId : null,
                'subtotal' => $quote['subtotal'],
                'discount_total' => $quote['discount'],
                'delivery_fee' => $quote['delivery_fee'],
                'tax_total' => $quote['tax'],
                'grand_total' => $quote['grand_total'],
                'currency' => $quote['currency'],
                'coupon_code' => $cart->coupon_code,
                'notes' => $contact['notes'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => round($item->quantity * (float) $item->unit_price, 2),
                ]);
            }

            if ($fulfillmentType === FulfillmentType::Delivery->value) {
                OrderAddress::create([
                    'order_id' => $order->id,
                    'type' => 'shipping',
                    'name' => $contact['name'],
                    'phone' => $contact['phone'] ?? null,
                    'line_1' => $address['line_1'],
                    'line_2' => $address['line_2'] ?? null,
                    'city' => $address['city'],
                    'region' => $address['region'] ?? null,
                    'country' => $address['country'] ?? 'Ghana',
                    'postal_code' => $address['postal_code'] ?? null,
                ]);

                Delivery::create([
                    'order_id' => $order->id,
                    'status' => 'pending',
                ]);
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => OrderStatus::AwaitingPayment->value,
                'changed_by' => $user?->id,
                'notes' => 'Checkout started',
            ]);

            $reference = 'PAY-'.strtoupper(Str::random(14));
            $payment = Payment::create([
                'reference' => $reference,
                'user_id' => $user?->id,
                'payable_type' => Order::class,
                'payable_id' => $order->id,
                'amount' => $order->grand_total,
                'currency' => $order->currency,
                'gateway' => config('payments.mock') ? 'mock' : config('payments.default', 'paystack'),
                'status' => PaymentStatus::Initiated->value,
                'metadata' => [
                    'order_number' => $order->number,
                    'email' => $contact['email'],
                ],
            ]);

            $initiation = $this->payments->initialize(new PaymentInitiationData(
                email: $contact['email'],
                amountMinor: (int) round(((float) $order->grand_total) * 100),
                currency: $order->currency,
                reference: $reference,
                callbackUrl: route('web.checkout.callback'),
                metadata: ['order_id' => $order->id, 'order_number' => $order->number],
            ));

            $payment->attempts()->create([
                'status' => PaymentStatus::Initiated->value,
                'request_payload' => ['reference' => $reference, 'amount' => $order->grand_total],
                'response_payload' => $initiation,
            ]);

            $cart->update([
                'fulfillment_type' => $fulfillmentType,
                'branch_id' => $branchId,
                'checkout_contact' => $contact,
                'checkout_address' => $address,
            ]);

            return [
                'order' => $order->fresh(['items', 'address']),
                'payment' => $payment,
                'authorization_url' => $initiation['authorization_url'] ?? route('web.checkout.processing', $order->number),
            ];
        });
    }

    public function confirmPayment(string $reference): Order
    {
        return DB::transaction(function () use ($reference) {
            /** @var Payment $payment */
            $payment = Payment::query()->where('reference', $reference)->lockForUpdate()->firstOrFail();

            if ($payment->status === PaymentStatus::Successful) {
                return $payment->payable()->firstOrFail();
            }

            $result = $this->payments->verify($reference);

            $payment->attempts()->create([
                'status' => $result->successful ? PaymentStatus::Successful->value : PaymentStatus::Failed->value,
                'response_payload' => $result->raw,
            ]);

            if (! $result->successful) {
                $payment->update(['status' => PaymentStatus::Failed->value]);
                if ($payment->payable instanceof Order) {
                    $payment->payable->update(['payment_status' => OrderPaymentStatus::Failed->value]);
                }

                throw new InvalidArgumentException('Payment could not be verified.');
            }

            $expectedMinor = (int) round(((float) $payment->amount) * 100);
            $paidMinor = (int) ($result->raw['amount'] ?? $expectedMinor);
            $paidCurrency = strtoupper((string) ($result->raw['currency'] ?? $payment->currency));
            if (! config('payments.mock')
                && ($paidMinor !== $expectedMinor || $paidCurrency !== strtoupper($payment->currency))) {
                throw new InvalidArgumentException('Payment details do not match the order total.');
            }

            /** @var Order $order */
            $order = Order::query()->lockForUpdate()->findOrFail($payment->payable_id);

            $this->inventory->decrementForOrder($order, $order->user_id);

            $from = $order->status instanceof \BackedEnum ? $order->status->value : $order->status;
            $order->update([
                'status' => OrderStatus::Paid->value,
                'payment_status' => OrderPaymentStatus::Paid->value,
                'paid_at' => now(),
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => $from,
                'to_status' => OrderStatus::Paid->value,
                'changed_by' => $order->user_id,
                'notes' => 'Payment confirmed',
            ]);

            $payment->update([
                'status' => PaymentStatus::Successful->value,
                'provider_reference' => $result->providerReference,
                'paid_at' => now(),
            ]);

            if ($order->user_id) {
                $userCart = Cart::query()->where('user_id', $order->user_id)->first();
                if ($userCart) {
                    $this->carts->clear($userCart);
                }
            } else {
                $guestCart = Cart::query()->whereNull('user_id')->where('session_id', session()->getId())->first();
                if ($guestCart) {
                    $this->carts->clear($guestCart);
                }
            }

            $this->notifications->notifyAdmins([
                'title' => 'New paid order',
                'message' => 'Order '.$order->number.' paid — '.$order->currency.' '.$order->grand_total,
                'action_url' => route('admin.orders.edit', $order),
                'category' => 'order',
            ]);

            if ($order->user) {
                $this->notifications->notifyUser($order->user, [
                    'title' => 'Order confirmed',
                    'message' => 'Payment received for order '.$order->number.'.',
                    'action_url' => route('client.orders.show', $order),
                    'category' => 'order',
                ]);
            }

            return $order->fresh(['items', 'address', 'delivery']);
        });
    }

    private function uniqueOrderNumber(): string
    {
        do {
            $number = 'ORD-'.now()->format('ymd').'-'.strtoupper(Str::random(6));
        } while (Order::query()->where('number', $number)->exists());

        return $number;
    }
}
