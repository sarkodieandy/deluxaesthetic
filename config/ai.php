<?php

return [
    'enabled' => (bool) env('AI_ENABLED', true),
    'driver' => env('AI_DRIVER', 'mock'),
    'provider' => env('AI_PROVIDER', 'openai'),
    'api_key' => env('AI_API_KEY'),
    'api_url' => env('AI_API_URL'),
    'model' => env('AI_MODEL', 'gpt-4o-mini'),
    'rate_limit_per_minute' => (int) env('AI_RATE_LIMIT', 10),
    'retention_days' => (int) env('AI_RETENTION_DAYS', 30),
    'safety' => [
        'forbid_diagnosis' => true,
        'forbid_prescription' => true,
        'forbid_private_data' => true,
        'escalate_phrase' => 'I can connect you with our clinical team for personalised advice.',
    ],
];
