<?php

return [
    'default' => env('PAYMENT_DRIVER', 'paystack'),
    'currency' => env('DEFAULT_CURRENCY', 'GHS'),
    'mock' => (bool) env('PAYMENT_MOCK', true),
    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
        'webhook_secret' => env('PAYSTACK_WEBHOOK_SECRET'),
        'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
        'callback_url' => env('PAYSTACK_CALLBACK_URL'),
    ],
];
