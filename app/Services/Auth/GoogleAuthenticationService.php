<?php

namespace App\Services\Auth;

use App\Actions\Auth\CreateGoogleUser;
use App\DTOs\Auth\GoogleUserData;
use App\Events\Auth\GoogleAccountLinked;
use App\Events\Auth\GoogleAccountUnlinked;
use App\Models\SocialAccount;
use App\Models\User;
use App\Support\GoogleAuth;
use App\Support\PortalRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;

class GoogleAuthenticationService
{
    public function __construct(
        private readonly CreateGoogleUser $createGoogleUser,
        private readonly LoginPortalService $loginPortals,
    ) {}

    public function oauthRedirectUrl(): string
    {
        $configured = trim((string) config('services.google.redirect'));

        if ($configured !== '') {
            return $configured;
        }

        return url('/auth/google/callback');
    }

    public function redirect(): SymfonyRedirect
    {
        return Socialite::driver('google')
            ->redirectUrl($this->oauthRedirectUrl())
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function fetchSocialiteUser(): \Laravel\Socialite\Contracts\User
    {
        return Socialite::driver('google')
            ->redirectUrl($this->oauthRedirectUrl())
            ->user();
    }

    /**
     * @return array{type: string, user?: User, message?: string}
     */
    public function handleCallback(): array
    {
        if (! GoogleAuth::enabled()) {
            return ['type' => 'error', 'message' => __('auth.google.disabled')];
        }

        $socialiteUser = $this->fetchSocialiteUser();
        $google = GoogleUserData::fromSocialiteUser($socialiteUser);

        if ($google->email === '') {
            return ['type' => 'error', 'message' => __('auth.google.missing_email')];
        }

        $existingSocial = SocialAccount::query()
            ->where('provider', 'google')
            ->where('provider_user_id', $google->providerUserId)
            ->first();

        if ($existingSocial) {
            $user = $existingSocial->user;
            if (! $user || ! $user->is_active) {
                return ['type' => 'error', 'message' => __('auth.google.account_inactive')];
            }

            if ($this->loginPortals->isAdminAccount($user)) {
                return ['type' => 'error', 'message' => 'Administrator accounts must sign in with their admin email and password. Google sign-in is for students only.'];
            }

            if ($this->loginPortals->isCustomerOnly($user)) {
                return ['type' => 'error', 'message' => 'Customers do not need an account. Please shop or book a consultation as a guest.'];
            }

            $existingSocial->update([
                'provider_email' => $google->email,
                'provider_avatar_url' => $google->avatarUrl,
                'last_used_at' => now(),
            ]);

            $this->loginUser($user);

            return ['type' => 'logged_in', 'user' => $user];
        }

        $existingUser = User::query()->where('email', $google->email)->first();
        if ($existingUser) {
            if (! $existingUser->is_active) {
                return ['type' => 'error', 'message' => __('auth.google.account_inactive')];
            }

            if ($this->loginPortals->isAdminAccount($existingUser)) {
                return ['type' => 'error', 'message' => 'Administrator accounts must sign in with their admin email and password. Google sign-in is for students only.'];
            }

            if ($this->loginPortals->isCustomerOnly($existingUser)) {
                return ['type' => 'error', 'message' => 'Customers do not need an account. Please shop or book a consultation as a guest.'];
            }

            if (! $existingUser->socialAccounts()->where('provider', 'google')->exists()) {
                $this->linkToAuthenticatedUser($existingUser, $google);
            }

            if (! $existingUser->email_verified_at) {
                $existingUser->forceFill(['email_verified_at' => now()])->save();
            }

            $this->loginUser($existingUser);

            return ['type' => 'logged_in', 'user' => $existingUser];
        }

        return [
            'type' => 'error',
            'message' => 'Submit your academy application first. After staff approval, you can use Google with the same email to sign in.',
        ];
    }

    public function loginUser(User $user): void
    {
        $user = $this->loginPortals->prepare($user);

        Auth::login($user);
        request()->session()->regenerate();

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ])->save();
    }

    public function putPendingGoogleData(GoogleUserData $google): void
    {
        session()->put(config('authentication.oauth.pending_session_key'), [
            'provider_user_id' => $google->providerUserId,
            'email' => $google->email,
            'name' => $google->name,
            'avatar_url' => $google->avatarUrl,
            'email_verified' => $google->emailVerified,
            'expires_at' => now()->addMinutes(config('authentication.oauth.pending_ttl_minutes'))->timestamp,
        ]);
    }

    public function pullPendingGoogleData(): ?GoogleUserData
    {
        $payload = session()->pull(config('authentication.oauth.pending_session_key'));
        if (! is_array($payload)) {
            return null;
        }

        if (($payload['expires_at'] ?? 0) < now()->timestamp) {
            return null;
        }

        return new GoogleUserData(
            providerUserId: (string) $payload['provider_user_id'],
            email: (string) $payload['email'],
            name: (string) $payload['name'],
            avatarUrl: $payload['avatar_url'] ?? null,
            emailVerified: (bool) ($payload['email_verified'] ?? false),
        );
    }

    public function peekPendingGoogleData(): ?GoogleUserData
    {
        $payload = session(config('authentication.oauth.pending_session_key'));
        if (! is_array($payload)) {
            return null;
        }

        if (($payload['expires_at'] ?? 0) < now()->timestamp) {
            return null;
        }

        return new GoogleUserData(
            providerUserId: (string) $payload['provider_user_id'],
            email: (string) $payload['email'],
            name: (string) $payload['name'],
            avatarUrl: $payload['avatar_url'] ?? null,
            emailVerified: (bool) ($payload['email_verified'] ?? false),
        );
    }

    /**
     * @param  'Client'|'Student'  $role
     */
    public function registerFromPending(string $role, array $profile, bool $terms, bool $privacy): User
    {
        $google = $this->pullPendingGoogleData();
        if (! $google) {
            throw new \RuntimeException('Google registration session expired.');
        }

        if (! in_array($role, config('authentication.google.public_roles', []), true)) {
            throw new \InvalidArgumentException('Invalid public role.');
        }

        return $this->createGoogleUser->handle($google, $role, $profile, $terms, $privacy);
    }

    public function linkToAuthenticatedUser(User $user, GoogleUserData $google): SocialAccount
    {
        if (SocialAccount::query()->where('provider', 'google')->where('provider_user_id', $google->providerUserId)->exists()) {
            throw new \RuntimeException(__('auth.google.already_linked_elsewhere'));
        }

        if ($user->socialAccounts()->where('provider', 'google')->exists()) {
            throw new \RuntimeException(__('auth.google.already_linked'));
        }

        return DB::transaction(function () use ($user, $google) {
            $account = SocialAccount::create([
                'user_id' => $user->id,
                'provider' => 'google',
                'provider_user_id' => $google->providerUserId,
                'provider_email' => $google->email,
                'provider_avatar_url' => $google->avatarUrl,
                'linked_at' => now(),
                'last_used_at' => now(),
            ]);

            event(new GoogleAccountLinked($user));

            return $account;
        });
    }

    public function linkPendingWithPassword(string $password): RedirectResponse
    {
        $google = $this->pullPendingGoogleData();
        if (! $google) {
            return redirect()->route('login')->withErrors(['email' => __('auth.google.session_expired')]);
        }

        $user = User::query()->where('email', $google->email)->first();
        if (! $user) {
            return redirect()->route('login')->withErrors(['email' => __('auth.failed')]);
        }

        if (! $user->password || ! Hash::check($password, $user->password)) {
            return back()->withErrors(['password' => __('auth.password')]);
        }

        if (! $user->is_active) {
            return redirect()->route('login')->withErrors(['email' => __('auth.google.account_inactive')]);
        }

        $this->linkToAuthenticatedUser($user, $google);
        $this->loginUser($user);

        return PortalRedirect::afterLogin($user, request());
    }

    public function unlink(User $user): void
    {
        if (! $user->password && $user->socialAccounts()->count() <= 1) {
            throw new \RuntimeException(__('auth.google.cannot_remove_last_method'));
        }

        $user->socialAccounts()->where('provider', 'google')->delete();
        event(new GoogleAccountUnlinked($user));
    }
}
