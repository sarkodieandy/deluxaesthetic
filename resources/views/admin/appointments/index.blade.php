@extends('admin.layouts.app')
@section('title', 'Appointments')
@section('heading', 'Appointments')
@section('breadcrumb', 'Clinic / Appointments')
@section('content')
<div class="admin-panel mb-6"><div class="admin-panel__head"><h2 class="admin-panel__title">Bookings</h2></div><div class="admin-panel__body" style="padding:0;"><table class="admin-table"><thead><tr><th>When</th><th>Client</th><th>Treatment</th><th>Practitioner</th><th>Status</th><th></th></tr></thead><tbody>@forelse($appointments as $appointment)<tr><td>{{ $appointment->starts_at?->timezone(config('clinic.timezone'))->format('d M Y H:i') }}</td><td>{{ $appointment->clientProfile?->displayName() }}</td><td>{{ $appointment->treatment?->name }}</td><td>{{ $appointment->practitioner?->user?->name }}</td><td><span class="admin-status admin-status--info">{{ ucfirst(str_replace('_', ' ', $appointment->status->value ?? (string) $appointment->status)) }}</span></td><td class="text-right"><a href="{{ route('admin.appointments.edit', $appointment) }}" class="btn btn-secondary">Open</a></td></tr>@empty<tr><td colspan="6"><div class="admin-empty"><p class="admin-empty__title">No appointments yet</p><p class="admin-empty__copy">Appointments from website booking will appear here.</p></div></td></tr>@endforelse</tbody></table></div></div>{{ $appointments->links() }}
@endsection
