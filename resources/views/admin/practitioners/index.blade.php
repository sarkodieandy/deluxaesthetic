@extends('admin.layouts.app')

@section('title', 'Team')
@section('heading', 'Team members')
@section('breadcrumb', 'Clinic / Practitioners')

@section('content')
@if (session('status'))
    <p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>
@endif

<div class="admin-panel mb-6">
    <div class="admin-panel__head">
        <h2 class="font-display text-xl m-0">Our team</h2>
        <a href="{{ route('admin.practitioners.create') }}" class="btn btn-primary">Add member</a>
    </div>
    <div class="admin-panel__body" style="padding:0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($practitioners as $member)
                    <tr>
                        <td>
                            <img class="admin-avatar" src="{{ $member->photoUrl() }}" alt="{{ $member->user?->name }}">
                        </td>
                        <td>
                            <strong>{{ $member->user?->name }}</strong>
                            @if($member->is_ceo)<span class="admin-status admin-status--info ml-2">CEO</span>@endif
                        </td>
                        <td>{{ $member->displayTitle() }}</td>
                        <td>
                            @if($member->is_active)
                                <span class="admin-status admin-status--success">Active</span>
                            @else
                                <span class="admin-status admin-status--warning">Hidden</span>
                            @endif
                        </td>
                        <td>{{ $member->sort_order }}</td>
                        <td class="text-right whitespace-nowrap">
                            <a href="{{ route('admin.practitioners.edit', $member) }}" class="btn btn-secondary">Edit</a>
                            @unless($member->is_ceo)
                                <form action="{{ route('admin.practitioners.destroy', $member) }}" method="POST" class="inline" onsubmit="return confirm('Remove this team member?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary">Delete</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="admin-empty">
                                <p class="admin-empty__title">No team members yet</p>
                                <p class="admin-empty__copy">Upload portraits and roles so they appear on the public Our Team page.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $practitioners->links() }}
@endsection
