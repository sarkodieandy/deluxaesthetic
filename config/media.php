<?php

return [
    'disk' => env('MEDIA_DISK', 'public'),
    'private_disk' => env('MEDIA_PRIVATE_DISK', 'local'),
    // Gallery administrators can upload high-resolution phone and studio photos.
    // Keep this aligned with deploy/php-upload.ini and deploy/nginx.conf.
    'max_upload_kb' => (int) env('MEDIA_MAX_UPLOAD_KB', 102400),
    'allowed_image_mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
    'allowed_document_mimes' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    'demo_manifest' => resource_path('data/asset-sources.json'),
];
