@extends('admin.layouts.app')
@section('title', 'My profile')
@section('heading', 'My profile')
@section('breadcrumb', 'Account / Profile')
@section('content')
@if (session('status') === 'profile-updated')
    <div class="admin-callout"><div><p class="admin-callout__title">Profile updated</p><p class="admin-callout__copy">Your account details have been saved.</p></div></div>
@endif
@if ($errors->any())
    <div class="mb-6 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div class="grid gap-6 xl:grid-cols-[20rem_minmax(0,1fr)]">
    <aside class="admin-panel self-start">
        <div class="admin-panel__body text-center">
            <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-[var(--color-charcoal)] font-display text-4xl text-white">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h2 class="font-display text-2xl">{{ $user->name }}</h2>
            <p class="mt-1 text-sm text-[var(--admin-text-muted)]">{{ $user->email }}</p>
            <div class="mt-4 flex flex-wrap justify-center gap-2">
                @foreach($user->getRoleNames() as $role)
                    <span class="admin-status admin-status--success">{{ $role }}</span>
                @endforeach
            </div>
            <dl class="mt-6 border-t border-[var(--admin-border)] pt-5 text-left text-sm">
                <div class="flex justify-between gap-4 py-2"><dt class="text-[var(--admin-text-muted)]">Account status</dt><dd>{{ $user->is_active ? 'Active' : 'Inactive' }}</dd></div>
                <div class="flex justify-between gap-4 py-2"><dt class="text-[var(--admin-text-muted)]">Last sign-in</dt><dd class="text-right">{{ $user->last_login_at?->diffForHumans() ?? 'Not recorded' }}</dd></div>
                <div class="flex justify-between gap-4 py-2"><dt class="text-[var(--admin-text-muted)]">Member since</dt><dd>{{ $user->created_at?->format('M Y') }}</dd></div>
            </dl>
            <a href="{{ route('admin.account.security') }}" class="btn btn-secondary mt-5 w-full">Security settings</a>
        </div>
    </aside>

    <form method="POST" action="{{ route('admin.profile.update') }}" class="admin-panel">
        @csrf
        @method('PATCH')
        <div class="admin-panel__head">
            <div><h2 class="admin-panel__title">Personal information</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">Keep your staff identity and contact details current.</p></div>
        </div>
        <div class="admin-panel__body grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="admin-label" for="name">Full name</label>
                <input id="name" name="name" class="admin-input" value="{{ old('name', $user->name) }}" required autocomplete="name">
            </div>
            <div>
                <label class="admin-label" for="email">Email address</label>
                <input id="email" name="email" type="email" class="admin-input" value="{{ old('email', $user->email) }}" required autocomplete="email">
                <p class="mt-1 text-xs text-[var(--admin-text-muted)]">Used for sign-in and account notifications.</p>
            </div>
            <div>
                <label class="admin-label" for="phone">Phone number</label>
                <input id="phone" name="phone" class="admin-input" value="{{ old('phone', $user->phone) }}" autocomplete="tel" placeholder="+233…">
            </div>
            <div>
                <label class="admin-label" for="locale">Preferred language</label>
                <select id="locale" name="locale" class="admin-input" required>
                    <option value="en" @selected(old('locale', $user->locale) === 'en')>English</option>
                    <option value="fr" @selected(old('locale', $user->locale) === 'fr')>French</option>
                </select>
            </div>
        </div>
        <div class="admin-panel__foot flex justify-end">
            <button type="submit" class="btn btn-primary">Save profile</button>
        </div>
    </form>
</div>
@endsection
