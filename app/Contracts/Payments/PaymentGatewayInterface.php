<?php

namespace App\Contracts\Payments;

use App\DTOs\PaymentInitiationData;
use App\DTOs\PaymentVerificationResult;

interface PaymentGatewayInterface
{
    public function initialize(PaymentInitiationData $data): array;

    public function verify(string $reference): PaymentVerificationResult;

    public function supportsWebhooks(): bool;
}
