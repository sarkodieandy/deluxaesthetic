<x-guest-layout>
    <div class="auth-intro">
        <h1 class="auth-title">{{ __('auth.google.complete_profile_title') }}</h1>
    </div>
    <form method="POST" action="{{ route('auth.google.complete-profile.store') }}" class="auth-form">
        @csrf
        <div class="auth-field">
            <label class="auth-label" for="phone">{{ __('web.student_portal.phone') }}</label>
            <input id="phone" class="field" type="text" name="phone" value="{{ old('phone') }}" required>
        </div>
        <label class="auth-check">
            <input type="checkbox" name="terms_accepted" value="1" required>
            <span>{{ __('I accept the terms and conditions') }}</span>
        </label>
        <label class="auth-check mt-3">
            <input type="checkbox" name="privacy_accepted" value="1" required>
            <span>{{ __('I accept the privacy policy') }}</span>
        </label>
        <label class="auth-check mt-3">
            <input type="checkbox" name="marketing_email_opt_in" value="1">
            <span>{{ __('Send me promotional updates') }}</span>
        </label>
        <button type="submit" class="btn btn-primary auth-submit">{{ __('Create account') }}</button>
    </form>
</x-guest-layout>
