<?php

return [
    'default_title' => env('SEO_DEFAULT_TITLE', 'De Luxe Aesthetic Clinic | Aesthetics & Academy in Accra'),
    'default_description' => env(
        'SEO_DEFAULT_DESCRIPTION',
        'Discover professional aesthetic treatments, advanced beauty training and clinic-selected skincare at De Luxe Aesthetic Clinic in East Legon, Accra.'
    ),
    'default_image' => env('SEO_DEFAULT_IMAGE', 'assets/web/images/hero/spa-treatment-room.webp'),
    'twitter_handle' => env('SEO_TWITTER_HANDLE'),
    'google_site_verification' => env('GOOGLE_SITE_VERIFICATION'),
    'social_urls' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('SEO_SOCIAL_URLS', ''))
    ))),
];
