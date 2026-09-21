<?php

return [
    'default' => env('PAYMENT_DRIVER', 'paystack'),
    'currency' => env('DEFAULT_CURRENCY', 'GHS'),
    'mock' => (bool) env('PAYMENT_MOCK', false),
    'store_driver' => env('STORE_PAYMENT_DRIVER', 'expresspay'),
    'store_mock' => (bool) env('STORE_PAYMENT_MOCK', false),
    'expresspay' => [
        'merchant_id' => env('EXPRESSPAY_MERCHANT_ID'),
        'api_key' => env('EXPRESSPAY_API_KEY'),
        'environment' => env('EXPRESSPAY_ENVIRONMENT', 'sandbox'),
        'callback_url' => env('EXPRESSPAY_CALLBACK_URL'),
        'post_url' => env('EXPRESSPAY_POST_URL'),
    ],
    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
        'webhook_secret' => env('PAYSTACK_WEBHOOK_SECRET'),
        'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
        'callback_url' => env('PAYSTACK_CALLBACK_URL'),
    ],
];
