<x-guest-layout>
    <div class="auth-intro">
        <p class="text-label">{{ __('Account recovery') }}</p>
        <h1 class="auth-title">{{ __('Reset password') }}</h1>
        <p class="auth-copy">{{ __('Forgot your password? Enter your email and we will send a reset link.') }}</p>
    </div>

    <x-auth-session-status class="auth-status" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
        @csrf
        <div class="auth-field">
            <label class="auth-label" for="email">{{ __('Email') }}</label>
            <input id="email" class="field" type="email" name="email" value="{{ old('email') }}" required autofocus>
            <x-input-error :messages="$errors->get('email')" class="auth-error" />
        </div>
        <button type="submit" class="btn btn-primary auth-submit">{{ __('Email reset link') }}</button>
    </form>

    <p class="auth-switch">
        <a class="auth-link" href="{{ route('login') }}">{{ __('Back to log in') }}</a>
    </p>
</x-guest-layout>
