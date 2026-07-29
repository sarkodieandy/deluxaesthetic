<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GoogleOAuthRedirectUri extends Command
{
    protected $signature = 'google:redirect-uri';

    protected $description = 'Print the Google OAuth redirect URI to add in Google Cloud Console';

    public function handle(): int
    {
        $uri = trim((string) config('services.google.redirect'));

        if ($uri === '') {
            $this->error('Google redirect URI is not configured. Set GOOGLE_REDIRECT_URI or APP_URL in .env');

            return self::FAILURE;
        }

        $this->line('');
        $this->info('Add this exact URI under Google Cloud Console → Credentials → OAuth client → Authorized redirect URIs:');
        $this->line('');
        $this->line('  '.$uri);
        $this->line('');
        $this->comment('Open the site using the same host and port as APP_URL (currently '.config('app.url').').');

        return self::SUCCESS;
    }
}
