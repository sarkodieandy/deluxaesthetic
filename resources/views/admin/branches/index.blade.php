@extends('admin.layouts.app')
@section('title', 'Branches')
@section('heading', 'Branches')
@section('breadcrumb', 'Clinic / Branches')
@section('content')
@if (session('status'))
    <p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>
@endif
@if ($errors->has('branch'))
    <p class="mb-4 border border-[var(--color-error)] bg-white px-4 py-3 text-[var(--color-error)]">{{ $errors->first('branch') }}</p>
@endif

<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <h2 class="admin-panel__title">Clinic locations</h2>
        <a href="{{ route('admin.branches.create') }}" class="btn btn-primary">Add branch</a>
    </div>
    <div class="admin-panel__body" style="padding:0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>City</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($branches as $branch)
                    <tr>
                        <td>
                            <strong>{{ $branch->name }}</strong>
                            @if($branch->is_primary)
                                <span class="admin-status admin-status--success ml-2">Primary</span>
                            @endif
                            <p class="text-xs text-[var(--admin-text-muted)] mt-1">{{ $branch->slug }}</p>
                        </td>
                        <td>{{ $branch->city ?: '—' }}</td>
                        <td class="text-sm">
                            @if($branch->phone)<span>{{ $branch->phone }}</span>@endif
                            @if($branch->email)<br><span class="text-[var(--admin-text-muted)]">{{ $branch->email }}</span>@endif
                            @if(! $branch->phone && ! $branch->email)—@endif
                        </td>
                        <td>
                            @if($branch->is_active)
                                <span class="admin-status admin-status--success">Active</span>
                            @else
                                <span class="admin-status admin-status--warning">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $branch->sort_order }}</td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('admin.branches.edit', $branch) }}" class="btn btn-secondary">Edit</a>
                            <form action="{{ route('admin.branches.destroy', $branch) }}" method="POST" class="inline" onsubmit="return confirm('Remove this branch?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="admin-empty">
                                <p class="admin-empty__title">No branches yet</p>
                                <p class="admin-empty__copy">Add your clinic locations so clients can choose a branch when booking online.</p>
                                <a href="{{ route('admin.branches.create') }}" class="btn btn-primary mt-4">Add first branch</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $branches->links() }}
@endsection
