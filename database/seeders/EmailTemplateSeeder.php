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
                    'body_text' => 'Hello {{recipient_name}}, welcome to {{business_name}}. Dashboard: {{dashboard_url}}',
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

            EmailTemplate::query()->updateOrCreate(
                ['key' => 'academy.application_received', 'locale' => $locale],
                [
                    'event' => 'academy.application_received',
                    'name' => 'Academy application received',
                    'subject' => $locale === 'fr'
                        ? 'Votre candidature a été reçue par {{business_name}}'
                        : 'Your academy application has been received',
                    'preheader' => 'Our admissions team will review your application and contact you before portal access is approved.',
                    'body_html' => <<<'HTML'
<p>Hello {{recipient_name}},</p>
<p>Thank you for applying for physical academy training at {{business_name}}. We have received your application successfully.</p>
<p>Our admissions team will review your details and contact you about the next steps. Your student portal access will remain inactive until the academy approves your application.</p>
<p>Please keep the password you created safe. Once approved, you can use your email address and that password to sign in.</p>
<p>If you need help, contact us at {{business_email}} or {{business_phone}}.</p>
HTML,
                    'body_text' => 'Hello {{recipient_name}}, we received your academy application. Our admissions team will review it and contact you. Your student portal access remains inactive until approval. Help: {{business_email}} / {{business_phone}}.',
                    'available_variables' => [],
                    'active' => true,
                    'system_template' => true,
                ]
            );
        }
    }
}
