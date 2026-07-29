<?php

return [
    'disk' => env('MEDIA_DISK', 'public'),
    'private_disk' => env('MEDIA_PRIVATE_DISK', 'local'),
    'max_upload_kb' => (int) env('MEDIA_MAX_UPLOAD_KB', 5120),
    'allowed_image_mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
    'allowed_document_mimes' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    'demo_manifest' => resource_path('data/asset-sources.json'),
];
