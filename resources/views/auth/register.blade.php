<x-guest-layout>
    <div class="auth-intro">
        <p class="text-label">{{ __('Join :brand', ['brand' => config('clinic.wordmark')]) }}</p>
        <h1 class="auth-title">{{ __('Create account') }}</h1>
        <p class="auth-copy">Optional clinic portal access. Aesthetic appointments can be booked without an account — create a student account only when enrolling for academy training.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        <div class="auth-field">
            <label class="auth-label" for="name">{{ __('Full name') }}</label>
            <input id="name" class="field" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            <x-input-error :messages="$errors->get('name')" class="auth-error" />
        </div>

        <div class="auth-field">
            <label class="auth-label" for="email">{{ __('Email') }}</label>
            <input id="email" class="field" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            <x-input-error :messages="$errors->get('email')" class="auth-error" />
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password">{{ __('Password') }}</label>
            <input id="password" class="field" type="password" name="password" required autocomplete="new-password">
            <x-input-error :messages="$errors->get('password')" class="auth-error" />
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password_confirmation">{{ __('Confirm password') }}</label>
            <input id="password_confirmation" class="field" type="password" name="password_confirmation" required autocomplete="new-password">
            <x-input-error :messages="$errors->get('password_confirmation')" class="auth-error" />
        </div>

        <button type="submit" class="btn btn-primary auth-submit">{{ __('Create account') }}</button>
    </form>

    @if (\App\Support\GoogleAuth::enabled())
        <div class="auth-divider"><span>{{ __('or') }}</span></div>
        <x-google-sign-in-button />
    @endif

    <p class="auth-switch">
        {{ __('Already registered?') }}
        <a class="auth-link" href="{{ route('login') }}">{{ __('Log in') }}</a>
    </p>
</x-guest-layout>
