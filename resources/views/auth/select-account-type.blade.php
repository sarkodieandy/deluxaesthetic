<x-guest-layout>
    <div class="auth-intro">
        <h1 class="auth-title">{{ __('auth.google.select_account_type_title') }}</h1>
        <p class="auth-copy">{{ __('auth.google.select_account_type_lead') }}</p>
    </div>
    <form method="POST" action="{{ route('auth.google.select-account-type.store') }}" class="auth-form">
        @csrf
        <label class="auth-check">
            <input type="radio" name="account_type" value="Client" required>
            <span>{{ __('auth.google.client_option') }}</span>
        </label>
        <label class="auth-check mt-4">
            <input type="radio" name="account_type" value="Student" required>
            <span>{{ __('auth.google.student_option') }}</span>
        </label>
        <button type="submit" class="btn btn-primary auth-submit mt-6">{{ __('Continue') }}</button>
    </form>
</x-guest-layout>
