<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGatewayInterface;
use App\DTOs\PaymentInitiationData;
use App\DTOs\PaymentVerificationResult;

class MockPaymentService implements PaymentGatewayInterface
{
    public function initialize(PaymentInitiationData $data): array
    {
        return [
            'authorization_url' => url('/payments/mock/'.$data->reference),
            'access_code' => 'mock_'.$data->reference,
            'reference' => $data->reference,
            'mock' => true,
        ];
    }

    public function verify(string $reference): PaymentVerificationResult
    {
        return new PaymentVerificationResult(
            successful: true,
            reference: $reference,
            status: 'successful',
            providerReference: 'mock_txn_'.$reference,
            raw: ['mock' => true],
        );
    }

    public function supportsWebhooks(): bool
    {
        return false;
    }
}
