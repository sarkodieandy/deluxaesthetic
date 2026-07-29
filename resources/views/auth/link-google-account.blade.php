<x-guest-layout>
    <div class="auth-intro">
        <h1 class="auth-title">{{ __('auth.google.link_account_title') }}</h1>
        <p class="auth-copy">{{ __('auth.google.link_account_lead') }}</p>
    </div>
    <form method="POST" action="{{ route('auth.google.link-account.store') }}" class="auth-form">
        @csrf
        <div class="auth-field">
            <label class="auth-label" for="password">{{ __('Password') }}</label>
            <input id="password" class="field" type="password" name="password" required>
            <x-input-error :messages="$errors->get('password')" class="auth-error" />
        </div>
        <button type="submit" class="btn btn-primary auth-submit">{{ __('Link Google account') }}</button>
    </form>
</x-guest-layout>
