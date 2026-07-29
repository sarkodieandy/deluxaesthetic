<?php

namespace App\Events\Auth;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SocialAccountRegistered
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user,
        public string $provider,
    ) {}
}
