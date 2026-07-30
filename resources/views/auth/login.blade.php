<x-guest-layout>
    <div class="auth-intro">
        <p class="text-label">{{ __('Secure portal') }}</p>
        <h1 class="auth-title">{{ __('Student & staff login') }}</h1>
        <p class="auth-copy">{{ __('This login is for enrolled students and authorised staff. Customers can shop and book consultations without an account.') }}</p>
    </div>

    <x-auth-session-status class="auth-status" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <div class="auth-field">
            <label class="auth-label" for="email">{{ __('Email') }}</label>
            <input id="email" class="field" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            <x-input-error :messages="$errors->get('email')" class="auth-error" />
        </div>

        <div class="auth-field">
            <div class="auth-label-row">
                <label class="auth-label" for="password">{{ __('Password') }}</label>
                @if (Route::has('password.request'))
                    <a class="auth-link" href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
                @endif
            </div>
            <input id="password" class="field" type="password" name="password" required autocomplete="current-password">
            <x-input-error :messages="$errors->get('password')" class="auth-error" />
        </div>

        <label class="auth-check">
            <input id="remember_me" type="checkbox" name="remember">
            <span>{{ __('Remember me') }}</span>
        </label>

        <button type="submit" class="btn btn-primary auth-submit">{{ __('Log in') }}</button>
    </form>

    @if (\App\Support\GoogleAuth::enabled())
        <div class="auth-divider"><span>{{ __('or') }}</span></div>
        <x-google-sign-in-button />
    @endif

    <p class="auth-switch">
        {{ __('Joining the academy?') }}
        <a class="auth-link" href="{{ route('web.academy.student-portal.create') }}">{{ __('Create a student account') }}</a>
    </p>
</x-guest-layout>
