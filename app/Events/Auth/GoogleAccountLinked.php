<?php

namespace App\Events\Auth;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GoogleAccountLinked
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user,
    ) {}
}
