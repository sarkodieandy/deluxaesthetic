<?php

namespace App\Http\Controllers\Webhooks;

use App\Exceptions\Payments\PaymentVerificationMismatchException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Checkout\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use RuntimeException;

class ExpressPayWebhookController extends Controller
{
    public function __invoke(Request $request, CheckoutService $checkout): JsonResponse
    {
        $data = $request->validate([
            'order-id' => ['required', 'string', 'max:64'],
            'token' => ['required', 'string', 'max:1024'],
        ]);

        $payment = Payment::query()->where('reference', $data['order-id'])
            ->where('gateway', 'expresspay')
            ->where('payable_type', (new Order)->getMorphClass())->firstOrFail();
        $token = $payment->metadata['expresspay_token'] ?? '';

        abort_unless(is_string($token) && $token !== '' && hash_equals($token, $data['token']), 403);

        try {
            // The notification itself is not proof of payment. Always query expressPay.
            $checkout->confirmPayment($payment->reference);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => 'Payment verification did not match the order.'], 422);
        } catch (PaymentVerificationMismatchException $e) {
            return response()->json(['message' => 'Payment verification did not match the order.'], 422);
        } catch (RuntimeException $e) {
            report($e);

            return response()->json(['message' => 'Payment verification is temporarily unavailable.'], 503);
        }

        return response()->json(['status' => 'ok']);
    }
}
