<?php

namespace App\Services\Messaging;

use App\Models\EmailTemplate;

class EmailTemplateService
{
    public function resolve(string $key, string $locale): ?EmailTemplate
    {
        $template = EmailTemplate::query()
            ->where('key', $key)
            ->where('locale', $locale)
            ->where('active', true)
            ->first();

        if ($template) {
            return $template;
        }

        $fallback = config('email-notifications.default_locale', 'en');
        if ($locale !== $fallback) {
            return EmailTemplate::query()
                ->where('key', $key)
                ->where('locale', $fallback)
                ->where('active', true)
                ->first();
        }

        return null;
    }
}
