@extends('admin.layouts.app')
@section('title', 'Clients')
@section('heading', 'Clients')
@section('breadcrumb', 'Clinic / Clients')
@section('content')
<div class="admin-panel mb-6"><div class="admin-panel__head"><h2 class="admin-panel__title">Client records</h2></div><div class="admin-panel__body" style="padding:0;"><table class="admin-table"><thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Loyalty</th><th></th></tr></thead><tbody>@forelse($clients as $client)<tr><td>{{ $client->user?->name }}</td><td>{{ $client->user?->email }}</td><td>{{ $client->user?->phone ?: '—' }}</td><td>{{ $client->loyalty_points }}</td><td class="text-right"><a href="{{ route('admin.clients.show', $client) }}" class="btn btn-secondary">Open</a></td></tr>@empty<tr><td colspan="5"><div class="admin-empty"><p class="admin-empty__title">No clients yet</p><p class="admin-empty__copy">Website clients will appear here automatically.</p></div></td></tr>@endforelse</tbody></table></div></div>{{ $clients->links() }}
@endsection
