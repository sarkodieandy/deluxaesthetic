<?php

namespace App\Http\Controllers\Web;

use App\Enums\FulfillmentType;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Cart\CartService;
use App\Services\Checkout\CheckoutService;
use App\Services\Checkout\OrderPricingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $carts,
        private readonly OrderPricingService $pricing,
        private readonly CheckoutService $checkout,
    ) {}

    public function show(): View|RedirectResponse
    {
        $cart = $this->carts->resolve();
        $summary = $this->carts->summary($cart);

        if ($summary['count'] < 1) {
            return redirect()->route('web.cart.index')->withErrors(['cart' => 'Your cart is empty.']);
        }

        if (! auth()->check() && ! config('ecommerce.allow_guest_checkout')) {
            return redirect()->route('login')->with('status', 'Sign in to checkout.');
        }

        $user = auth()->user();
        $quote = $this->pricing->quote($cart, $cart->fulfillment_type?->value);

        return view('web.store.checkout.review', [
            ...$summary,
            'quote' => $quote,
            'branches' => Branch::query()->where('is_active', true)->orderBy('name')->get(),
            'contact' => old('name') ? request()->old() : [
                'name' => $cart->checkout_contact['name'] ?? $user?->name,
                'email' => $cart->checkout_contact['email'] ?? $user?->email,
                'phone' => $cart->checkout_contact['phone'] ?? $user?->phone,
                'notes' => $cart->checkout_contact['notes'] ?? '',
            ],
            'address' => $cart->checkout_address ?? [],
            'fulfillmentType' => old('fulfillment_type', $cart->fulfillment_type?->value ?? FulfillmentType::Delivery->value),
            'branchId' => old('branch_id', $cart->branch_id),
        ]);
    }

    public function pay(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'fulfillment_type' => ['required', 'in:delivery,pickup'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'line_1' => ['required_if:fulfillment_type,delivery', 'nullable', 'string', 'max:255'],
            'line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required_if:fulfillment_type,delivery', 'nullable', 'string', 'max:120'],
            'region' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'terms' => ['accepted'],
        ];

        $data = $request->validate($rules);
        $cart = $this->carts->resolve();

        try {
            $result = $this->checkout->placePendingOrder(
                $cart,
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'notes' => $data['notes'] ?? null,
                ],
                [
                    'line_1' => $data['line_1'] ?? '',
                    'line_2' => $data['line_2'] ?? null,
                    'city' => $data['city'] ?? '',
                    'region' => $data['region'] ?? null,
                    'country' => $data['country'] ?? 'Ghana',
                    'postal_code' => $data['postal_code'] ?? null,
                ],
                $data['fulfillment_type'],
                isset($data['branch_id']) ? (int) $data['branch_id'] : null,
                $request->user(),
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['checkout' => $e->getMessage()]);
        } catch (RuntimeException $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['payment' => 'Paystack checkout is temporarily unavailable. Please try again.']);
        }

        return redirect()->away($result['authorization_url']);
    }

    public function processing(string $number): View
    {
        $order = Order::query()->where('number', $number)->firstOrFail();

        return view('web.store.checkout.processing', compact('order'));
    }

    public function mockPay(string $reference): View|RedirectResponse
    {
        $payment = Payment::query()->where('reference', $reference)->firstOrFail();
        $order = $payment->payable;

        if (! $order instanceof Order) {
            abort(404);
        }

        return view('web.store.checkout.payment', compact('payment', 'order'));
    }

    public function mockComplete(string $reference): RedirectResponse
    {
        try {
            $order = $this->checkout->confirmPayment($reference);
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('web.checkout.failure', $reference)
                ->withErrors(['payment' => $e->getMessage()]);
        }

        return redirect()->route('web.checkout.success', $order->number);
    }

    public function callback(Request $request): RedirectResponse
    {
        $reference = (string) $request->query('reference', $request->input('reference'));

        if ($reference === '') {
            return redirect()->route('web.cart.index')->withErrors(['payment' => 'Missing payment reference.']);
        }

        try {
            $order = $this->checkout->confirmPayment($reference);
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('web.checkout.failure', $reference)
                ->withErrors(['payment' => $e->getMessage()]);
        }

        return redirect()->route('web.checkout.success', $order->number);
    }

    public function success(string $number): View
    {
        $order = Order::query()
            ->with(['items', 'address', 'delivery', 'user'])
            ->where('number', $number)
            ->firstOrFail();

        return view('web.store.checkout.success', compact('order'));
    }

    public function failure(string $reference): View
    {
        $payment = Payment::query()->where('reference', $reference)->first();
        $order = $payment?->payable;

        return view('web.store.checkout.failure', compact('payment', 'order', 'reference'));
    }
}
