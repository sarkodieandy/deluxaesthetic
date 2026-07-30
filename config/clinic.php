<?php

return [
    'name' => env('BUSINESS_NAME', 'De Luxe Aesthetic Clinic'),
    'legal_name' => env('BUSINESS_LEGAL_NAME', 'De Luxe Aesthetic Clinic'),
    'wordmark' => env('BUSINESS_WORDMARK', 'De Luxe'),
    'logo_subtitle' => env('BUSINESS_LOGO_SUBTITLE', 'Aesthetic Clinic'),
    'email' => env('BUSINESS_EMAIL', 'luxeaetheticsacademy@gmail.com'),
    'phone' => env('BUSINESS_PHONE', '+233552248636'),
    'whatsapp' => env('BUSINESS_WHATSAPP', '+233552248636'),
    'address' => env('BUSINESS_ADDRESS', 'East Legon, Dr Tagoe Avenue, GA-375-8490, Accra, Ghana'),
    'hours' => env('BUSINESS_HOURS', 'Daily · 7:00am–7:00pm'),
    'map_embed_url' => env('BUSINESS_MAP_EMBED_URL') ?: 'https://maps.google.com/maps?q=East+Legon+Dr+Tagoe+Avenue+Accra+Ghana&t=&z=16&ie=UTF8&iwloc=&output=embed',
    'map_link' => env('BUSINESS_MAP_LINK') ?: 'https://www.google.com/maps/search/?api=1&query=East+Legon+Dr+Tagoe+Avenue+Accra+Ghana',
    'currency' => env('DEFAULT_CURRENCY', 'GHS'),
    'timezone' => env('APP_TIMEZONE', 'Africa/Accra'),
    'default_locale' => env('DEFAULT_LOCALE', 'en'),
    'supported_locales' => ['en', 'fr'],
    'booking' => [
        'deposit_percent' => (int) env('BOOKING_DEPOSIT_PERCENT', 30),
        'cancellation_hours' => (int) env('BOOKING_CANCELLATION_HOURS', 24),
        'reschedule_hours' => (int) env('BOOKING_RESCHEDULE_HOURS', 12),
    ],
    'ceo' => [
        'name' => env('CEO_NAME', 'Dr Evelyn Ejaife'),
        'title' => env('CEO_TITLE', 'CPD licensed Aesthetic Trainer and Specialist'),
        'portrait_a' => 'assets/web/images/team/ceo-mac-tonto-portrait-a.png',
        'portrait_b' => 'assets/web/images/team/ceo-mac-tonto-portrait-b.png',
    ],
];
