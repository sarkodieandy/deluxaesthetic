<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'business_name', 'value' => config('clinic.name'), 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'business_email', 'value' => config('clinic.email'), 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'business_phone', 'value' => config('clinic.phone'), 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'default_currency', 'value' => config('clinic.currency'), 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'default_locale', 'value' => config('clinic.default_locale'), 'type' => 'string', 'is_public' => true],
            ['group' => 'features', 'key' => 'ai_enabled', 'value' => config('ai.enabled') ? '1' : '0', 'type' => 'boolean', 'is_public' => false],
            ['group' => 'features', 'key' => 'payment_mock', 'value' => config('payments.mock') ? '1' : '0', 'type' => 'boolean', 'is_public' => false],
            ['group' => 'announcement', 'key' => 'bar_text_en', 'value' => 'Book your consultation or enrol in our professional academy today.', 'type' => 'string', 'is_public' => true],
            ['group' => 'announcement', 'key' => 'bar_text_fr', 'value' => 'Réservez votre consultation ou inscrivez-vous à notre académie professionnelle.', 'type' => 'string', 'is_public' => true],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
