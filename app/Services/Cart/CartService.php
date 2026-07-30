<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CartService
{
    public function resolve(?User $user = null, ?string $sessionId = null): Cart
    {
        $user = $user ?? auth()->user();
        $sessionId = $sessionId ?: session()->getId();

        if ($user) {
            $cart = Cart::query()->firstOrCreate(
                ['user_id' => $user->id],
                ['session_id' => $sessionId]
            );

            $this->mergeGuestCart($cart, $sessionId);

            $cart = $cart->load(['items.product.images', 'items.variant']);
            session()->put('cart_count', $this->itemCount($cart));

            return $cart;
        }

        $cart = Cart::query()->firstOrCreate(
            ['session_id' => $sessionId, 'user_id' => null],
            []
        )->load(['items.product.images', 'items.variant']);
        session()->put('cart_count', $this->itemCount($cart));

        return $cart;
    }

    public function add(Product $product, int $quantity = 1, ?int $variantId = null): Cart
    {
        if (! $product->is_active) {
            throw new InvalidArgumentException('This product is not available.');
        }

        $quantity = max(1, $quantity);
        $stock = $product->availableStock();

        if ($stock < 1) {
            throw new InvalidArgumentException('This product is out of stock.');
        }

        $cart = $this->resolve();
        $item = $cart->items()
            ->where('product_id', $product->id)
            ->where(function ($q) use ($variantId) {
                $variantId
                    ? $q->where('product_variant_id', $variantId)
                    : $q->whereNull('product_variant_id');
            })
            ->first();

        $newQty = ($item?->quantity ?? 0) + $quantity;

        if ($newQty > $stock) {
            throw new InvalidArgumentException('Only '.$stock.' unit(s) available.');
        }

        $unitPrice = (float) $product->effectivePrice();

        if ($item) {
            $item->update([
                'quantity' => $newQty,
                'unit_price' => $unitPrice,
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ]);
        }

        $cart = $cart->fresh(['items.product.images', 'items.variant']);
        session()->put('cart_count', $this->itemCount($cart));

        return $cart;
    }

    public function updateQuantity(CartItem $item, int $quantity): Cart
    {
        $cart = $this->ownedCartOrFail($item->cart_id);
        $product = $item->product;

        if (! $product || ! $product->is_active) {
            $item->delete();
            throw new InvalidArgumentException('That product is no longer available.');
        }

        if ($quantity < 1) {
            $item->delete();

            $cart = $cart->fresh(['items.product.images', 'items.variant']);
            session()->put('cart_count', $this->itemCount($cart));

            return $cart;
        }

        $stock = $product->availableStock();
        if ($quantity > $stock) {
            throw new InvalidArgumentException('Only '.$stock.' unit(s) available.');
        }

        $item->update([
            'quantity' => $quantity,
            'unit_price' => (float) $product->effectivePrice(),
        ]);

        $cart = $cart->fresh(['items.product.images', 'items.variant']);
        session()->put('cart_count', $this->itemCount($cart));

        return $cart;
    }

    public function remove(CartItem $item): Cart
    {
        $cart = $this->ownedCartOrFail($item->cart_id);
        $item->delete();

        $cart = $cart->fresh(['items.product.images', 'items.variant']);
        session()->put('cart_count', $this->itemCount($cart));

        return $cart;
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->update(['coupon_code' => null]);
        session()->put('cart_count', 0);
    }

    public function applyCoupon(Cart $cart, string $code): Cart
    {
        $coupon = Coupon::query()
            ->where('code', Str::upper(trim($code)))
            ->where('is_active', true)
            ->first();

        if (! $coupon || ! $coupon->isValid()) {
            throw new InvalidArgumentException('This coupon is not valid.');
        }

        $subtotal = $this->subtotal($cart);
        if ($coupon->minimum_spend && $subtotal < (float) $coupon->minimum_spend) {
            throw new InvalidArgumentException('Minimum spend for this coupon is GHS '.number_format((float) $coupon->minimum_spend, 2));
        }

        $cart->update(['coupon_code' => $coupon->code]);

        return $cart->fresh(['items.product.images', 'items.variant']);
    }

    public function removeCoupon(Cart $cart): Cart
    {
        $cart->update(['coupon_code' => null]);

        return $cart->fresh(['items.product.images', 'items.variant']);
    }

    public function revalidate(Cart $cart): array
    {
        $notices = [];

        foreach ($cart->items as $item) {
            $product = $item->product;
            if (! $product || ! $product->is_active) {
                $item->delete();
                $notices[] = 'A product was removed because it is no longer available.';
                continue;
            }

            $price = (float) $product->effectivePrice();
            if ((float) $item->unit_price !== $price) {
                $item->update(['unit_price' => $price]);
                $notices[] = $product->name.' price was updated.';
            }

            $stock = $product->availableStock();
            if ($item->quantity > $stock) {
                if ($stock < 1) {
                    $item->delete();
                    $notices[] = $product->name.' is out of stock and was removed.';
                } else {
                    $item->update(['quantity' => $stock]);
                    $notices[] = $product->name.' quantity was reduced to available stock.';
                }
            }
        }

        return $notices;
    }

    public function subtotal(Cart $cart): float
    {
        return round((float) $cart->items->sum(fn (CartItem $item) => $item->quantity * (float) $item->unit_price), 2);
    }

    public function itemCount(Cart $cart): int
    {
        return (int) $cart->items->sum('quantity');
    }

    public function summary(Cart $cart): array
    {
        $notices = $this->revalidate($cart);
        $cart->refresh()->load(['items.product.images', 'items.variant']);

        $subtotal = $this->subtotal($cart);
        $discount = $this->couponDiscount($cart, $subtotal);

        return [
            'cart' => $cart,
            'items' => $cart->items,
            'count' => $this->itemCount($cart),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'coupon_code' => $cart->coupon_code,
            'notices' => $notices,
        ];
    }

    public function couponDiscount(Cart $cart, ?float $subtotal = null): float
    {
        if (! $cart->coupon_code) {
            return 0.0;
        }

        $coupon = Coupon::query()->where('code', $cart->coupon_code)->where('is_active', true)->first();
        if (! $coupon || ! $coupon->isValid()) {
            return 0.0;
        }

        $subtotal ??= $this->subtotal($cart);
        if ($coupon->minimum_spend && $subtotal < (float) $coupon->minimum_spend) {
            return 0.0;
        }

        if ($coupon->type === 'fixed') {
            return round(min((float) $coupon->value, $subtotal), 2);
        }

        return round(min($subtotal * ((float) $coupon->value / 100), $subtotal), 2);
    }

    private function mergeGuestCart(Cart $userCart, string $sessionId): void
    {
        $guest = Cart::query()
            ->whereNull('user_id')
            ->where('session_id', $sessionId)
            ->where('id', '!=', $userCart->id)
            ->first();

        if (! $guest) {
            return;
        }

        DB::transaction(function () use ($guest, $userCart) {
            foreach ($guest->items as $guestItem) {
                $existing = $userCart->items()
                    ->where('product_id', $guestItem->product_id)
                    ->where(function ($q) use ($guestItem) {
                        $guestItem->product_variant_id
                            ? $q->where('product_variant_id', $guestItem->product_variant_id)
                            : $q->whereNull('product_variant_id');
                    })
                    ->first();

                if ($existing) {
                    $stock = $existing->product?->availableStock() ?? 0;
                    $existing->update([
                        'quantity' => min($existing->quantity + $guestItem->quantity, max($stock, $existing->quantity)),
                        'unit_price' => (float) ($existing->product?->effectivePrice() ?? $existing->unit_price),
                    ]);
                    $guestItem->delete();
                } else {
                    $guestItem->update(['cart_id' => $userCart->id]);
                }
            }

            if ($guest->coupon_code && ! $userCart->coupon_code) {
                $userCart->update(['coupon_code' => $guest->coupon_code]);
            }

            $guest->delete();
        });
    }

    private function ownedCartOrFail(int $cartId): Cart
    {
        $cart = $this->resolve();

        if ($cart->id !== $cartId) {
            throw new InvalidArgumentException('Cart item not found.');
        }

        return $cart;
    }
}
