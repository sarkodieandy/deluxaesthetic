<?php

namespace App\DTOs;

readonly class PaymentInitiationData
{
    public function __construct(
        public string $email,
        public int $amountMinor,
        public string $currency,
        public string $reference,
        public string $callbackUrl,
        public array $metadata = [],
    ) {}
}
