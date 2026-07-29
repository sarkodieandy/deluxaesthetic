<?php

namespace App\DTOs;

readonly class PaymentVerificationResult
{
    public function __construct(
        public bool $successful,
        public string $reference,
        public string $status,
        public ?string $providerReference = null,
        public array $raw = [],
    ) {}
}
