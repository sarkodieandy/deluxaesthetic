<?php

namespace App\Support;

class GoogleAuth
{
    public static function enabled(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'));
    }
}
