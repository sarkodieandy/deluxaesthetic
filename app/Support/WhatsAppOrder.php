<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;

class WhatsAppOrder
{
    public static function enabled(): bool
    {
        return (bool) config('ecommerce.whatsapp_checkout', true);
    }

    public static function productUrl(Product $product, int $quantity = 1): string
    {
        $quantity = max(1, $quantity);
        $total = $quantity * (float) $product->effectivePrice();

        return self::url(implode("\n", [
            'Hello '.config('clinic.ceo.name').', I would like to order this product from the De Luxe store:',
            '',
            '*'.$product->name.'*',
            'Quantity: '.$quantity,
            'Price: GHS '.number_format((float) $product->effectivePrice(), 2),
            'Estimated total: GHS '.number_format($total, 2),
            'Product: '.route('web.store.show', $product->slug),
            '',
            'Please help me confirm availability, delivery and payment details.',
        ]));
    }

    /**
     * @param  Collection<int, mixed>  $items
     */
    public static function cartUrl(Collection $items, float $subtotal, float $discount = 0): string
    {
        $lines = [];

        foreach ($items->values() as $index => $item) {
            $name = $item->product?->name ?? 'Product';
            $quantity = (int) $item->quantity;
            $lineTotal = $quantity * (float) $item->unit_price;

            $lines[] = ($index + 1).'. '.$name.' × '.$quantity.' — GHS '.number_format($lineTotal, 2);
        }

        $total = max($subtotal - $discount, 0);
        $message = array_merge([
            'Hello '.config('clinic.ceo.name').', I would like to place this order from the De Luxe website:',
            '',
        ], $lines, [
            '',
            'Subtotal: GHS '.number_format($subtotal, 2),
        ]);

        if ($discount > 0) {
            $message[] = 'Discount: GHS '.number_format($discount, 2);
        }

        $message = array_merge($message, [
            'Estimated total: GHS '.number_format($total, 2),
            '',
            'Please help me confirm availability, delivery and payment details.',
        ]);

        return self::url(implode("\n", $message));
    }

    private static function url(string $message): string
    {
        $number = preg_replace('/\D+/', '', (string) (config('ecommerce.whatsapp_number') ?: config('clinic.whatsapp')));

        return 'https://wa.me/'.$number.'?text='.rawurlencode($message);
    }
}
