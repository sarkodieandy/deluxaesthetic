@extends('student.layouts.app')
@section('title', __('student.nav.security'))
@section('heading', __('student.nav.security'))
@section('content')
<div class="grid gap-6 lg:grid-cols-2">
    <div class="student-panel space-y-4">
        <p class="font-display text-xl">{{ __('student.security.password_title') }}</p>
        <p class="text-[var(--color-soft-grey)]">{{ __('student.security.password_copy') }}</p>
        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')
            <input type="password" name="current_password" class="field" autocomplete="current-password" placeholder="{{ __('student.security.current_password') }}" required>
            <input type="password" name="password" class="field" autocomplete="new-password" placeholder="{{ __('student.security.new_password') }}" required>
            <input type="password" name="password_confirmation" class="field" autocomplete="new-password" placeholder="{{ __('student.security.confirm_password') }}" required>
            <button type="submit" class="student-action">{{ __('student.security.save_password') }}</button>
        </form>
    </div>
    <div class="student-panel space-y-4">
        <p class="font-display text-xl">{{ __('student.security.google_title') }}</p>
        <p class="text-[var(--color-soft-grey)]">{{ __('student.security.google_copy') }}</p>
        <a href="{{ route('account.linked-accounts') }}" class="student-action">{{ __('student.security.linked_accounts') }}</a>
    </div>
</div>
@endsection
