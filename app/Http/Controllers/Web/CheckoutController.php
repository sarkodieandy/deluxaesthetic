<?php

namespace App\Http\Controllers\Web;

use App\Enums\FulfillmentType;
use App\Enums\OrderPaymentStatus;
use App\Exceptions\Payments\PaymentVerificationMismatchException;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Cart\CartService;
use App\Services\Checkout\CheckoutService;
use App\Services\Checkout\OrderPricingService;
use App\Support\WhatsAppOrder;
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

        if (WhatsAppOrder::enabled()) {
            return redirect()->away(WhatsAppOrder::cartUrl(
                $summary['items'],
                (float) $summary['subtotal'],
                (float) $summary['discount'],
            ));
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
        if (WhatsAppOrder::enabled()) {
            $summary = $this->carts->summary($this->carts->resolve());

            if ($summary['count'] < 1) {
                return redirect()->route('web.cart.index')->withErrors(['cart' => 'Your cart is empty.']);
            }

            return redirect()->away(WhatsAppOrder::cartUrl(
                $summary['items'],
                (float) $summary['subtotal'],
                (float) $summary['discount'],
            ));
        }

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:64'],
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
                ->withErrors(['payment' => 'Online payment is temporarily unavailable. Please try again shortly.']);
        }

        return redirect()->away($result['authorization_url']);
    }

    public function processing(string $number): View|RedirectResponse
    {
        $order = Order::query()->where('number', $number)->firstOrFail();

        if ($order->payment_status === OrderPaymentStatus::Paid) {
            return redirect()->route('web.checkout.success', $number);
        }

        return view('web.store.checkout.processing', compact('order'));
    }

    public function mockPay(string $reference): View|RedirectResponse
    {
        $payment = Payment::query()->where('reference', $reference)->firstOrFail();
        $this->ensureMockPayment($payment);
        $order = $payment->payable;

        if (! $order instanceof Order) {
            abort(404);
        }

        return view('web.store.checkout.payment', compact('payment', 'order'));
    }

    public function mockComplete(string $reference): RedirectResponse
    {
        $payment = Payment::query()->where('reference', $reference)->firstOrFail();
        $this->ensureMockPayment($payment);

        try {
            $order = $this->checkout->confirmPayment($reference);
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('web.checkout.failure', $reference)
                ->withErrors(['payment' => $e->getMessage()]);
        }

        return $this->paymentRedirect($order, $reference);
    }

    public function callback(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reference' => ['nullable', 'string', 'max:64'],
            'order-id' => ['nullable', 'string', 'max:64'],
            'token' => ['nullable', 'string', 'max:1024'],
        ]);
        $reference = $data['order-id'] ?? $data['reference'] ?? '';

        abort_if(isset($data['reference'], $data['order-id']) && $data['reference'] !== $data['order-id'], 422);

        abort_if($reference === '', 422, 'Missing payment reference.');

        $payment = Payment::query()->where('reference', $reference)
            ->where('payable_type', (new Order)->getMorphClass())->firstOrFail();

        if ($payment->gateway === 'expresspay') {
            $token = $payment->metadata['expresspay_token'] ?? '';
            abort_unless(is_string($token) && $token !== '' && isset($data['order-id'], $data['token'])
                && hash_equals($token, $data['token']), 403);
        } else {
            abort_if(isset($data['order-id']), 403);
        }

        try {
            $order = $this->checkout->confirmPayment($reference);
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('web.checkout.failure', $reference)
                ->withErrors(['payment' => $e->getMessage()]);
        } catch (PaymentVerificationMismatchException $e) {
            return redirect()
                ->route('web.checkout.failure', $reference)
                ->withErrors(['payment' => 'Payment details do not match the order.']);
        } catch (RuntimeException $e) {
            report($e);

            return redirect()->route('web.checkout.processing', $payment->payable->number)
                ->withErrors(['payment' => 'We could not check your payment yet. Please check its status again shortly.']);
        }

        return $this->paymentRedirect($order, $reference);
    }

    public function status(string $number): RedirectResponse
    {
        $order = Order::query()->where('number', $number)->firstOrFail();
        $payment = $order->payments()->latest('id')->firstOrFail();

        try {
            $order = $this->checkout->confirmPayment($payment->reference);
        } catch (InvalidArgumentException $e) {
            return redirect()->route('web.checkout.failure', $payment->reference)
                ->withErrors(['payment' => $e->getMessage()]);
        } catch (PaymentVerificationMismatchException $e) {
            return redirect()->route('web.checkout.failure', $payment->reference)
                ->withErrors(['payment' => 'Payment details do not match the order.']);
        } catch (RuntimeException $e) {
            report($e);

            return redirect()->route('web.checkout.processing', $number)
                ->withErrors(['payment' => 'We could not check your payment yet. Please try again shortly.']);
        }

        return $this->paymentRedirect($order, $payment->reference);
    }

    public function success(string $number): View|RedirectResponse
    {
        $order = Order::query()
            ->with(['items', 'address', 'delivery', 'user'])
            ->where('number', $number)
            ->firstOrFail();

        if ($order->payment_status !== OrderPaymentStatus::Paid) {
            return redirect()->route('web.checkout.processing', $number);
        }

        return view('web.store.checkout.success', compact('order'));
    }

    public function failure(string $reference): View
    {
        $payment = Payment::query()->where('reference', $reference)->first();
        $order = $payment?->payable;

        return view('web.store.checkout.failure', compact('payment', 'order', 'reference'));
    }

    private function paymentRedirect(Order $order, string $reference): RedirectResponse
    {
        return match ($order->payment_status) {
            OrderPaymentStatus::Paid => redirect()->route('web.checkout.success', $order->number),
            OrderPaymentStatus::Pending => redirect()->route('web.checkout.processing', $order->number),
            default => redirect()->route('web.checkout.failure', $reference),
        };
    }

    private function ensureMockPayment(Payment $payment): void
    {
        abort_unless(app()->environment('local', 'testing') && config('payments.store_mock')
            && $payment->gateway === 'mock', 404);
    }
}
