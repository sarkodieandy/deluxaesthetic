<?php

namespace App\Http\Controllers\Auth;

use App\Support\GoogleAuth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CompleteGoogleProfileRequest;
use App\Http\Requests\Auth\SelectGoogleAccountTypeRequest;
use App\Services\Auth\GoogleAuthenticationService;
use App\Support\PortalRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;

class GoogleAuthController extends Controller
{
    public function redirect(GoogleAuthenticationService $google): RedirectResponse|SymfonyRedirect
    {
        if (! GoogleAuth::enabled()) {
            return redirect()->route('login')->withErrors(['email' => __('auth.google.disabled')]);
        }

        return $google->redirect();
    }

    public function callback(GoogleAuthenticationService $google): RedirectResponse
    {
        if ($linkUserId = session()->pull('google_oauth.linking_user_id')) {
            return $this->handleLinkCallback($google, (int) $linkUserId);
        }

        try {
            $result = $google->handleCallback();
        } catch (\Laravel\Socialite\Two\InvalidStateException) {
            return redirect()->route('login')->withErrors(['email' => __('auth.google.session_state_mismatch')]);
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('login')->withErrors(['email' => __('auth.google.provider_error')]);
        }

        if (($result['type'] ?? null) === 'logged_in') {
            $user = $result['user'];
            $isClientOnly = $user->hasRole('Client')
                && ! $user->hasAnyRole(array_merge(config('admin.roles', []), ['Student']));

            if ($isClientOnly) {
                auth()->logout();

                return redirect()->route('login')->withErrors([
                    'email' => 'Customers do not need an account. Please shop or book a consultation as a guest.',
                ]);
            }
        }

        return match ($result['type']) {
            'logged_in' => PortalRedirect::afterLogin($result['user'], request()),
            'select_account_type' => redirect()->route('auth.google.select-account-type'),
            default => redirect()->route('login')->withErrors(['email' => $result['message'] ?? __('auth.google.provider_error')]),
        };
    }

    private function handleLinkCallback(GoogleAuthenticationService $google, int $linkUserId): RedirectResponse
    {
        if (! auth()->check() || auth()->id() !== $linkUserId) {
            return redirect()->route('login')->withErrors(['email' => __('auth.google.link_session_invalid')]);
        }

        try {
            $socialiteUser = $google->fetchSocialiteUser();
            $googleData = \App\DTOs\Auth\GoogleUserData::fromSocialiteUser($socialiteUser);
            $google->linkToAuthenticatedUser(auth()->user(), $googleData);
        } catch (\Laravel\Socialite\Two\InvalidStateException) {
            return redirect()->route('account.linked-accounts')->withErrors(['google' => __('auth.google.session_state_mismatch')]);
        } catch (\Throwable $exception) {
            return redirect()->route('account.linked-accounts')->withErrors(['google' => $exception->getMessage()]);
        }

        return redirect()->route('account.linked-accounts')->with('status', __('auth.google.linked_success'));
    }

    public function selectAccountType(GoogleAuthenticationService $google): View|RedirectResponse
    {
        if (! $google->peekPendingGoogleData()) {
            return redirect()->route('login')->withErrors(['email' => __('auth.google.session_expired')]);
        }

        return view('auth.select-account-type');
    }

    public function storeAccountType(SelectGoogleAccountTypeRequest $request, GoogleAuthenticationService $google): RedirectResponse
    {
        session()->put('google_oauth.selected_role', $request->validated('account_type'));

        return redirect()->route('auth.google.complete-profile');
    }

    public function completeProfile(GoogleAuthenticationService $google): View|RedirectResponse
    {
        if (! $google->peekPendingGoogleData() || ! session('google_oauth.selected_role')) {
            return redirect()->route('login')->withErrors(['email' => __('auth.google.session_expired')]);
        }

        return view('auth.complete-social-profile', [
            'accountType' => session('google_oauth.selected_role'),
        ]);
    }

    public function storeCompleteProfile(CompleteGoogleProfileRequest $request, GoogleAuthenticationService $google): RedirectResponse
    {
        $role = session()->pull('google_oauth.selected_role');
        if (! in_array($role, config('authentication.google.public_roles', []), true)) {
            return redirect()->route('login')->withErrors(['email' => __('auth.google.session_expired')]);
        }

        try {
            $user = $google->registerFromPending(
                $role,
                $request->safe()->only(['phone', 'marketing_email_opt_in']),
                $request->boolean('terms_accepted'),
                $request->boolean('privacy_accepted'),
            );
        } catch (\Throwable) {
            return redirect()->route('login')->withErrors(['email' => __('auth.google.session_expired')]);
        }

        $google->loginUser($user);

        return PortalRedirect::afterRegistration($user)
            ->with('status', __('auth.google.registration_complete'));
    }

    public function linkAccountForm(GoogleAuthenticationService $google): View|RedirectResponse
    {
        if (! $google->peekPendingGoogleData()) {
            return redirect()->route('login')->withErrors(['email' => __('auth.google.session_expired')]);
        }

        return view('auth.link-google-account');
    }

    public function linkAccount(Request $request, GoogleAuthenticationService $google): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        return $google->linkPendingWithPassword($request->string('password')->toString());
    }
}
