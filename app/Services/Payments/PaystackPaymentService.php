<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\DTOs\PaymentInitiationData;
use App\DTOs\PaymentVerificationResult;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaystackPaymentService implements PaymentGatewayInterface
{
    public function initialize(PaymentInitiationData $data): array
    {
        $secret = config('payments.paystack.secret_key');

        if (! $secret) {
            throw new RuntimeException('Paystack secret key is not configured.');
        }

        $response = Http::withToken($secret)
            ->baseUrl(config('payments.paystack.base_url'))
            ->post('/transaction/initialize', [
                'email' => $data->email,
                'amount' => $data->amountMinor,
                'currency' => $data->currency,
                'reference' => $data->reference,
                'callback_url' => $data->callbackUrl,
                'metadata' => $data->metadata,
            ]);

        if (! $response->successful() || ! ($response->json('status') === true)) {
            throw new RuntimeException('Unable to initialise Paystack payment.');
        }

        return $response->json('data');
    }

    public function verify(string $reference): PaymentVerificationResult
    {
        $secret = config('payments.paystack.secret_key');

        if (! $secret) {
            throw new RuntimeException('Paystack secret key is not configured.');
        }

        $response = Http::withToken($secret)
            ->baseUrl(config('payments.paystack.base_url'))
            ->get('/transaction/verify/'.rawurlencode($reference));

        $data = $response->json('data') ?? [];
        $status = $response->successful() && $response->json('status') === true
            ? ($data['status'] ?? 'failed')
            : 'failed';

        return new PaymentVerificationResult(
            successful: $status === 'success'
                && hash_equals($reference, (string) ($data['reference'] ?? '')),
            reference: $reference,
            status: $status,
            providerReference: $data['id'] ?? null,
            raw: $data,
        );
    }

    public function supportsWebhooks(): bool
    {
        return true;
    }
}
