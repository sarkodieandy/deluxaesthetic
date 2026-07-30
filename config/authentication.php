<?php

return [

    'google' => [
        // Resolved at runtime in AppServiceProvider (services config loads after this file).
        'enabled' => false,
        'public_roles' => ['Student'],
    ],

    'oauth' => [
        'pending_session_key' => 'google_oauth.pending',
        'pending_ttl_minutes' => 30,
    ],

];
