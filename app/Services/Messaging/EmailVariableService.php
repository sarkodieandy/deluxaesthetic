<?php

namespace App\Services\Messaging;

use App\Models\EmailTemplate;
use App\Models\User;
use InvalidArgumentException;

class EmailVariableService
{
    /** @var list<string> */
    private array $globalKeys = [
        'app_name',
        'business_name',
        'business_email',
        'business_phone',
        'business_address',
        'recipient_name',
        'dashboard_url',
        'support_url',
    ];

    /**
     * @param  array<string, string|null>  $variables
     * @return array<string, string>
     */
    public function mergeDefaults(array $variables, ?User $user = null): array
    {
        $defaults = [
            'app_name' => config('app.name'),
            'business_name' => config('clinic.name'),
            'business_email' => config('clinic.email'),
            'business_phone' => config('clinic.phone'),
            'business_address' => config('clinic.address'),
            'recipient_name' => $user?->name ?? ($variables['recipient_name'] ?? ''),
            'dashboard_url' => $user ? url(route($user->portalHomeRoute(), absolute: false)) : url('/'),
            'support_url' => url('/contact'),
        ];

        return array_merge($defaults, array_map(fn ($v) => (string) ($v ?? ''), $variables));
    }

    /**
     * @param  array<string, string>  $variables
     */
    public function assertAllowed(EmailTemplate $template, array $variables): void
    {
        $allowed = array_merge($this->globalKeys, $template->available_variables ?? []);
        foreach (array_keys($variables) as $key) {
            if (! in_array($key, $allowed, true)) {
                throw new InvalidArgumentException("Unknown email variable: {$key}");
            }
        }
    }

    /**
     * @param  array<string, string>  $variables
     */
    public function render(string $content, array $variables): string
    {
        $replacements = [];
        foreach ($variables as $key => $value) {
            $replacements['{{'.$key.'}}'] = e($value);
        }

        return strtr($content, $replacements);
    }
}
