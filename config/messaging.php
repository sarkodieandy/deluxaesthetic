<?php

return [
    'default_channel' => env('MESSAGING_DEFAULT_CHANNEL', 'mail'),
    'queue' => env('MESSAGING_QUEUE', 'notifications'),
    'email' => [
        'driver' => env('MAIL_MAILER', 'log'),
        'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@deluxaesthetic.com'),
        'from_name' => env('MAIL_FROM_NAME', 'De Luxe Aesthetic Clinic'),
    ],
    'sms' => [
        'driver' => env('SMS_DRIVER', 'mock'),
        'api_url' => env('SMS_API_URL'),
        'api_key' => env('SMS_API_KEY'),
        'sender_id' => env('SMS_SENDER_ID', 'DeLux'),
    ],
    'whatsapp' => [
        'driver' => env('WHATSAPP_DRIVER', 'mock'),
        'api_url' => env('WHATSAPP_API_URL'),
        'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
        'webhook_secret' => env('WHATSAPP_WEBHOOK_SECRET'),
        'public_number' => env('BUSINESS_WHATSAPP'),
    ],
];
