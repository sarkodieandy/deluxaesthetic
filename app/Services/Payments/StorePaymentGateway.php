<?php

namespace App\Services\Payments;

use App\Contracts\Payments\PaymentGatewayInterface;
use RuntimeException;

class StorePaymentGateway
{
    public function driver(): string
    {
        return config('payments.store_mock') ? 'mock' : (string) config('payments.store_driver', 'expresspay');
    }

    public function resolve(?string $driver = null): PaymentGatewayInterface
    {
        $driver ??= $this->driver();

        if ($driver === 'mock' && (! config('payments.store_mock') || ! app()->environment('local', 'testing'))) {
            throw new RuntimeException('Demo payments are only available in local development and tests.');
        }

        return match ($driver) {
            'expresspay' => app(ExpressPayPaymentService::class),
            'paystack' => app(PaystackPaymentService::class),
            'mock' => app(MockPaymentService::class),
            default => throw new RuntimeException('The store payment provider is not supported.'),
        };
    }
}
