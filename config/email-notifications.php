<?php

return [

    'enabled' => env('EMAIL_NOTIFICATIONS_ENABLED', true),
    'logging_enabled' => env('EMAIL_LOGGING_ENABLED', true),
    'default_locale' => env('EMAIL_DEFAULT_LOCALE', 'en'),
    'retry_attempts' => (int) env('EMAIL_RETRY_ATTEMPTS', 3),
    'retry_delay_seconds' => (int) env('EMAIL_RETRY_DELAY_SECONDS', 300),

    'addresses' => [
        'admin_alert' => env('EMAIL_ADMIN_ALERT_ADDRESS'),
        'support' => env('EMAIL_SUPPORT_ADDRESS', env('BUSINESS_EMAIL')),
        'bookings' => env('EMAIL_BOOKINGS_ADDRESS', env('BUSINESS_EMAIL')),
        'academy' => env('EMAIL_ACADEMY_ADDRESS', env('BUSINESS_EMAIL')),
        'store' => env('EMAIL_STORE_ADDRESS', env('BUSINESS_EMAIL')),
    ],

    'reply_to' => [
        'address' => env('MAIL_REPLY_TO_ADDRESS', env('BUSINESS_EMAIL')),
        'name' => env('MAIL_REPLY_TO_NAME', env('APP_NAME')),
    ],

];
