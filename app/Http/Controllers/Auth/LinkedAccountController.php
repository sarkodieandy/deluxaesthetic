<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UnlinkGoogleAccountRequest;
use App\Services\Auth\GoogleAuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class LinkedAccountController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $googleLinked = $user->socialAccounts()->where('provider', 'google')->exists();

        return view('auth.linked-accounts', compact('googleLinked'));
    }

    public function linkRedirect(GoogleAuthenticationService $google): RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        session()->put('google_oauth.linking_user_id', auth()->id());

        return Socialite::driver('google')
            ->redirectUrl($google->oauthRedirectUrl())
            ->redirect();
    }

    public function unlink(UnlinkGoogleAccountRequest $request, GoogleAuthenticationService $google): RedirectResponse
    {
        $user = $request->user();

        if ($user->password && ! Hash::check($request->string('password'), $user->password)) {
            return back()->withErrors(['password' => __('auth.password')]);
        }

        try {
            $google->unlink($user);
        } catch (\Throwable $exception) {
            return back()->withErrors(['google' => $exception->getMessage()]);
        }

        return back()->with('status', __('auth.google.unlinked_success'));
    }
}
