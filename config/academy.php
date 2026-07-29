<?php

return [
    'name' => env('ACADEMY_NAME', 'De Lux Professional Training Academy'),
    'default_deposit_percent' => (int) env('ACADEMY_DEPOSIT_PERCENT', 40),
    'waiting_list_enabled' => (bool) env('ACADEMY_WAITING_LIST', true),
    'certificate_prefix' => env('CERTIFICATE_PREFIX', 'DLX'),
    'attendance_pass_percent' => (int) env('ATTENDANCE_PASS_PERCENT', 80),
    'online_balance_payment_enabled' => (bool) env('ACADEMY_ONLINE_BALANCE_PAYMENT_ENABLED', false),
];
