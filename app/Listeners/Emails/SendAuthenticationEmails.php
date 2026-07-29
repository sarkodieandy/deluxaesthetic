<?php

namespace App\Listeners\Emails;

use App\Events\Auth\GoogleAccountLinked;
use App\Events\Auth\GoogleAccountUnlinked;
use App\Events\Auth\SocialAccountRegistered;
use App\Services\Messaging\EmailNotificationService;
use Illuminate\Auth\Events\Registered;

class SendAuthenticationEmails
{
    public function __construct(
        private readonly EmailNotificationService $email,
    ) {}

    public function handleWelcome(Registered $event): void
    {
        if ($event->user->socialAccounts()->exists()) {
            return;
        }

        $this->email->queueToUser('auth.welcome', $event->user);
    }

    public function handleSocialRegistered(SocialAccountRegistered $event): void
    {
        $this->email->queueToUser('auth.welcome', $event->user, [
            'registration_method' => $event->provider,
        ]);
    }

    public function handleGoogleLinked(GoogleAccountLinked $event): void
    {
        $this->email->queueToUser('auth.google_linked', $event->user);
    }

    public function handleGoogleUnlinked(GoogleAccountUnlinked $event): void
    {
        $this->email->queueToUser('auth.google_unlinked', $event->user);
    }
}
