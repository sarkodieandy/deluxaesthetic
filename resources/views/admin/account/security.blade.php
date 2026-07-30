@extends('admin.layouts.app')
@section('title', 'Security & account')
@section('heading', 'Security & account')
@section('breadcrumb', 'Account / Security')
@section('content')
@if (session('status'))
    <div class="admin-callout"><div><p class="admin-callout__title">Account updated</p><p class="admin-callout__copy">{{ match(session('status')) { 'password-updated' => 'Your password has been changed.', 'other-sessions-ended' => 'All other browser sessions have been signed out.', default => 'Your account settings have been saved.' } }}</p></div></div>
@endif

<div class="grid gap-6 xl:grid-cols-2">
    <form method="POST" action="{{ route('password.update') }}" class="admin-panel">
        @csrf
        @method('PUT')
        <div class="admin-panel__head"><div><h2 class="admin-panel__title">Password</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">Use a strong, unique password for this admin account.</p></div></div>
        <div class="admin-panel__body space-y-4">
            <div><label class="admin-label" for="current_password">Current password</label><input id="current_password" name="current_password" type="password" class="admin-input" autocomplete="current-password">@error('current_password', 'updatePassword')<p class="mt-1 text-sm text-[var(--color-error)]">{{ $message }}</p>@enderror</div>
            <div><label class="admin-label" for="password">New password</label><input id="password" name="password" type="password" class="admin-input" autocomplete="new-password">@error('password', 'updatePassword')<p class="mt-1 text-sm text-[var(--color-error)]">{{ $message }}</p>@enderror</div>
            <div><label class="admin-label" for="password_confirmation">Confirm new password</label><input id="password_confirmation" name="password_confirmation" type="password" class="admin-input" autocomplete="new-password"></div>
        </div>
        <div class="admin-panel__foot flex justify-end"><button class="btn btn-primary">Update password</button></div>
    </form>

    <section class="admin-panel">
        <div class="admin-panel__head"><div><h2 class="admin-panel__title">Google sign-in</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">Connect Google as a secure alternative sign-in method.</p></div></div>
        <div class="admin-panel__body">
            @error('google')<p class="mb-4 text-sm text-[var(--color-error)]">{{ $message }}</p>@enderror
            <div class="flex items-center justify-between gap-5 border border-[var(--admin-border)] p-4">
                <div><p class="font-semibold">Google account</p><p class="mt-1 text-sm text-[var(--admin-text-muted)]">{{ $googleLinked ? 'Connected to this account' : 'Not connected' }}</p></div>
                @if($googleLinked)
                    <form method="POST" action="{{ route('account.google.unlink') }}" class="text-right">
                        @csrf @method('DELETE')
                        @if($user->hasUsablePassword())<input name="password" type="password" class="admin-input mb-2 max-w-52" placeholder="Current password" required>@endif
                        <button class="btn btn-danger btn-sm">Disconnect</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('account.google.link') }}">@csrf<button class="btn btn-secondary">Connect Google</button></form>
                @endif
            </div>
        </div>
    </section>
 
    <section class="admin-panel xl:col-span-2">
        <div class="admin-panel__head"><div><h2 class="admin-panel__title">Active sessions</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">Review browsers signed in to your account and close any you do not recognise.</p></div></div>
        <div class="admin-panel__body">
            <div class="divide-y divide-[var(--admin-border)]">
                @forelse($sessions as $session)
                    <div class="flex flex-wrap items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                        <div><p class="font-semibold">{{ $session['user_agent'] }} @if($session['current'])<span class="admin-status admin-status--success ml-2">This device</span>@endif</p><p class="mt-1 text-sm text-[var(--admin-text-muted)]">{{ $session['ip_address'] }} · Active {{ $session['last_active_at']->diffForHumans() }}</p></div>
                    </div>
                @empty
                    <p class="text-sm text-[var(--admin-text-muted)]">Session details are unavailable with the current session storage configuration.</p>
                @endforelse
            </div>
            @if($sessions->where('current', false)->isNotEmpty())
                <form method="POST" action="{{ route('admin.account.sessions.destroy') }}" class="mt-6 border-t border-[var(--admin-border)] pt-5">
                    @csrf @method('DELETE')
                    <label class="admin-label" for="session_password">Confirm your password</label>
                    <div class="flex flex-wrap gap-3"><input id="session_password" name="password" type="password" class="admin-input max-w-sm" required autocomplete="current-password"><button class="btn btn-danger">Sign out other sessions</button></div>
                    @error('password', 'sessions')<p class="mt-2 text-sm text-[var(--color-error)]">{{ $message }}</p>@enderror
                </form>
            @endif
        </div>
    </section>
</div>
@endsection
