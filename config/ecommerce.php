<?php

return [
    'currency' => env('DEFAULT_CURRENCY', 'GHS'),
    'currency_display' => env('STORE_CURRENCY_DISPLAY', 'GH₵'),
    'tax_percent' => (float) env('STORE_TAX_PERCENT', 0),
    'low_stock_threshold' => (int) env('STORE_LOW_STOCK_THRESHOLD', 5),
    'allow_guest_checkout' => (bool) env('STORE_GUEST_CHECKOUT', true),
    'delivery_enabled' => (bool) env('STORE_DELIVERY_ENABLED', true),
    'pickup_enabled' => (bool) env('STORE_PICKUP_ENABLED', true),
    'delivery_fee' => (float) env('STORE_DELIVERY_FEE', 25),
    'free_delivery_threshold' => env('STORE_FREE_DELIVERY_THRESHOLD') !== null
        ? (float) env('STORE_FREE_DELIVERY_THRESHOLD')
        : null,
];
