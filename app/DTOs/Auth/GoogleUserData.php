<?php

namespace App\DTOs\Auth;

readonly class GoogleUserData
{
    public function __construct(
        public string $providerUserId,
        public string $email,
        public string $name,
        public ?string $avatarUrl,
        public bool $emailVerified,
    ) {}

    /**
     * @param  \Laravel\Socialite\Contracts\User  $user
     */
    public static function fromSocialiteUser($user): self
    {
        $raw = $user->user ?? [];
        if ($raw instanceof \Illuminate\Support\Collection) {
            $raw = $raw->all();
        }

        return new self(
            providerUserId: (string) $user->getId(),
            email: strtolower((string) $user->getEmail()),
            name: (string) ($user->getName() ?: $user->getNickname() ?: 'Google User'),
            avatarUrl: $user->getAvatar(),
            emailVerified: (bool) ($raw['email_verified'] ?? $raw['verified_email'] ?? true),
        );
    }
}
