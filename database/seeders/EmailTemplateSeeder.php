<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $body = <<<'HTML'
<p>Hello {{recipient_name}},</p>
<p>Welcome to {{business_name}}. Your account is ready.</p>
<p><a href="{{dashboard_url}}">Open your dashboard</a></p>
<p>Questions? Contact us at {{business_email}}.</p>
HTML;

        foreach (['en', 'fr'] as $locale) {
            $subject = $locale === 'fr' ? 'Bienvenue chez {{business_name}}' : 'Welcome to {{business_name}}';

            EmailTemplate::query()->updateOrCreate(
                ['key' => 'auth.welcome', 'locale' => $locale],
                [
                    'event' => 'auth.registered',
                    'name' => 'Welcome email',
                    'subject' => $subject,
                    'body_html' => $body,
                    'body_text' => "Hello {{recipient_name}}, welcome to {{business_name}}. Dashboard: {{dashboard_url}}",
                    'available_variables' => ['registration_method'],
                    'active' => true,
                    'system_template' => true,
                ]
            );

            EmailTemplate::query()->updateOrCreate(
                ['key' => 'auth.google_linked', 'locale' => $locale],
                [
                    'event' => 'auth.google_linked',
                    'name' => 'Google linked',
                    'subject' => $locale === 'fr' ? 'Compte Google associé' : 'Google account linked',
                    'body_html' => '<p>Hello {{recipient_name}}, Google sign-in was linked to your account.</p>',
                    'body_text' => 'Google sign-in was linked to your account.',
                    'available_variables' => [],
                    'active' => true,
                    'system_template' => true,
                ]
            );

            EmailTemplate::query()->updateOrCreate(
                ['key' => 'auth.google_unlinked', 'locale' => $locale],
                [
                    'event' => 'auth.google_unlinked',
                    'name' => 'Google unlinked',
                    'subject' => $locale === 'fr' ? 'Compte Google dissocié' : 'Google account unlinked',
                    'body_html' => '<p>Hello {{recipient_name}}, Google sign-in was removed from your account.</p>',
                    'body_text' => 'Google sign-in was removed from your account.',
                    'available_variables' => [],
                    'active' => true,
                    'system_template' => true,
                ]
            );
        }
    }
}
