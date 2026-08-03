<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\Cart\CartService;
use App\Support\WhatsAppOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class CartController extends Controller
{
    public function __construct(private readonly CartService $carts) {}

    public function index(): View
    {
        $summary = $this->carts->summary($this->carts->resolve());

        return view('web.store.cart', [
            ...$summary,
            'whatsAppCheckoutUrl' => WhatsAppOrder::cartUrl(
                $summary['items'],
                (float) $summary['subtotal'],
                (float) $summary['discount'],
            ),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
            'buy_now' => ['nullable', 'boolean'],
        ]);

        $product = Product::query()->whereKey($data['product_id'])->where('is_active', true)->firstOrFail();

        try {
            $this->carts->add($product, (int) ($data['quantity'] ?? 1));
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['cart' => $e->getMessage()]);
        }

        if (($data['buy_now'] ?? false) && WhatsAppOrder::enabled()) {
            return redirect()->away(WhatsAppOrder::productUrl($product, (int) ($data['quantity'] ?? 1)));
        }

        return redirect()
            ->route(($data['buy_now'] ?? false) ? 'web.checkout.show' : 'web.cart.index')
            ->with('status', $product->name.' added to cart.');
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        try {
            $this->carts->updateQuantity($item, (int) $data['quantity']);
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['cart' => $e->getMessage()]);
        }

        return back()->with('status', 'Cart updated.');
    }

    public function destroy(CartItem $item): RedirectResponse
    {
        try {
            $this->carts->remove($item);
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['cart' => $e->getMessage()]);
        }

        return back()->with('status', 'Item removed.');
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:40']]);

        try {
            $this->carts->applyCoupon($this->carts->resolve(), $data['code']);
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['coupon' => $e->getMessage()]);
        }

        return back()->with('status', 'Coupon applied.');
    }

    public function removeCoupon(): RedirectResponse
    {
        $this->carts->removeCoupon($this->carts->resolve());

        return back()->with('status', 'Coupon removed.');
    }
}
