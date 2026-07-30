@extends('admin.layouts.app')
@section('title', 'Staff accounts')
@section('heading', 'Staff accounts')
@section('breadcrumb', 'Access / Users')
@section('content')
<div class="admin-callout">
    <div><p class="admin-callout__title">Controlled staff access</p><p class="admin-callout__copy">Create accounts only for administrators and team members. Student accounts continue to be managed through enrolment.</p></div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add staff account</a>
</div>

<form method="GET" class="admin-panel mb-6">
    <div class="admin-panel__body grid gap-4 md:grid-cols-[minmax(0,1fr)_16rem_auto]">
        <div><label class="admin-label" for="search">Search</label><input id="search" name="search" class="admin-input" value="{{ $filters['search'] }}" placeholder="Name, email or phone"></div>
        <div><label class="admin-label" for="role">Role</label><select id="role" name="role" class="admin-input"><option value="">All roles</option>@foreach($roles as $role)<option value="{{ $role->name }}" @selected($filters['role'] === $role->name)>{{ $role->name }}</option>@endforeach</select></div>
        <div class="flex items-end gap-2"><button class="btn btn-primary">Filter</button><a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a></div>
    </div>
</form>

<div class="admin-panel">
    <div class="admin-panel__head"><div><h2 class="admin-panel__title">Team directory</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">{{ $users->total() }} account{{ $users->total() === 1 ? '' : 's' }}</p></div></div>
    <div class="admin-panel__body" style="padding:0;overflow-x:auto">
        <table class="admin-table">
            <thead><tr><th>User</th><th>Role</th><th>Status</th><th>Last sign-in</th><th></th></tr></thead>
            <tbody>
            @forelse($users as $managedUser)
                <tr>
                    <td><strong>{{ $managedUser->name }}</strong><p class="mt-1 text-xs text-[var(--admin-text-muted)]">{{ $managedUser->email }}{{ $managedUser->phone ? ' · '.$managedUser->phone : '' }}</p></td>
                    <td>{{ $managedUser->getRoleNames()->join(', ') ?: 'No role' }}</td>
                    <td><span class="admin-status {{ $managedUser->is_active ? 'admin-status--success' : 'admin-status--warning' }}">{{ $managedUser->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>{{ $managedUser->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                    <td class="text-right"><a href="{{ route('admin.users.edit', $managedUser) }}" class="btn btn-secondary btn-sm">Manage</a></td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="admin-empty"><p class="admin-empty__title">No accounts found</p><p class="admin-empty__copy">Try clearing the filters or add a staff account.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $users->links() }}</div>
@endsection
