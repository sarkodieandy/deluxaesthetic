<?php

namespace App\Services\Checkout;

use App\Enums\FulfillmentType;
use App\Models\Cart;
use App\Services\Cart\CartService;

class OrderPricingService
{
    public function __construct(private readonly CartService $carts) {}

    /**
     * @return array{
     *     subtotal: float,
     *     discount: float,
     *     delivery_fee: float,
     *     tax: float,
     *     grand_total: float,
     *     currency: string
     * }
     */
    public function quote(Cart $cart, ?string $fulfillmentType = null): array
    {
        $subtotal = $this->carts->subtotal($cart);
        $discount = $this->carts->couponDiscount($cart, $subtotal);
        $type = $fulfillmentType ?: $cart->fulfillment_type ?: FulfillmentType::Delivery->value;

        $deliveryFee = 0.0;
        if ($type === FulfillmentType::Delivery->value && config('ecommerce.delivery_enabled', true)) {
            $threshold = config('ecommerce.free_delivery_threshold');
            $fee = (float) config('ecommerce.delivery_fee', 0);
            $deliveryFee = ($threshold !== null && $subtotal - $discount >= (float) $threshold) ? 0.0 : $fee;
        }

        $taxable = max($subtotal - $discount, 0);
        $tax = round($taxable * ((float) config('ecommerce.tax_percent', 0) / 100), 2);
        $grand = round(max($subtotal - $discount, 0) + $deliveryFee + $tax, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'delivery_fee' => round($deliveryFee, 2),
            'tax' => $tax,
            'grand_total' => $grand,
            'currency' => config('ecommerce.currency', 'GHS'),
        ];
    }
}
