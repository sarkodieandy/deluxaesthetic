@php($managedUser = $managedUser ?? null)
@if ($errors->any())
    <div class="mb-6 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]"><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
    <div class="admin-panel">
        <div class="admin-panel__head"><div><h2 class="admin-panel__title">Account details</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">Identity and sign-in information for this team member.</p></div></div>
        <div class="admin-panel__body grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2"><label class="admin-label" for="name">Full name</label><input id="name" name="name" class="admin-input" value="{{ old('name', $managedUser?->name) }}" required></div>
            <div><label class="admin-label" for="email">Email address</label><input id="email" name="email" type="email" class="admin-input" value="{{ old('email', $managedUser?->email) }}" required></div>
            <div><label class="admin-label" for="phone">Phone</label><input id="phone" name="phone" class="admin-input" value="{{ old('phone', $managedUser?->phone) }}" placeholder="+233…"></div>
            <div><label class="admin-label" for="password">{{ $managedUser ? 'New password (optional)' : 'Temporary password' }}</label><input id="password" name="password" type="password" class="admin-input" {{ $managedUser ? '' : 'required' }} autocomplete="new-password"></div>
            <div><label class="admin-label" for="password_confirmation">Confirm password</label><input id="password_confirmation" name="password_confirmation" type="password" class="admin-input" {{ $managedUser ? '' : 'required' }} autocomplete="new-password"></div>
        </div>
    </div>
    <aside class="space-y-6">
        <div class="admin-panel">
            <div class="admin-panel__head"><h2 class="admin-panel__title">Access</h2></div>
            <div class="admin-panel__body space-y-5">
                <div><label class="admin-label" for="role">Staff role</label><select id="role" name="role" class="admin-input" required><option value="">Select a role</option>@foreach($roles as $role)<option value="{{ $role->name }}" @selected(old('role', $managedUser?->getRoleNames()->first()) === $role->name)>{{ $role->name }}</option>@endforeach</select><p class="mt-1 text-xs text-[var(--admin-text-muted)]">The role controls which admin modules are available.</p></div>
                <div><label class="admin-label" for="locale">Language</label><select id="locale" name="locale" class="admin-input"><option value="en" @selected(old('locale', $managedUser?->locale ?? 'en') === 'en')>English</option><option value="fr" @selected(old('locale', $managedUser?->locale) === 'fr')>French</option></select></div>
                <input type="hidden" name="is_active" value="0">
                <label class="admin-check"><input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $managedUser?->is_active ?? true))> Active account</label>
            </div>
        </div>
        <div class="flex flex-wrap justify-end gap-3"><a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a><button class="btn btn-primary">{{ $managedUser ? 'Save account' : 'Create account' }}</button></div>
    </aside>
</div>
