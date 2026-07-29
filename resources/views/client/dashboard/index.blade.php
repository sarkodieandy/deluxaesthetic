@extends('client.layouts.app')

@section('title', 'Client overview')
@section('heading', 'Welcome, '.$user->name)
@section('eyebrow', 'Client portal')

@section('content')
<div class="portal-metric-grid mb-8">
    <div class="portal-metric">
        <p class="portal-metric__label">Next appointment</p>
        <p class="portal-metric__value" style="font-size:1.25rem;">
            @if($metrics['next_appointment'])
                {{ $metrics['next_appointment']->starts_at?->timezone(config('app.timezone'))->format('d M · H:i') }}
            @else
                —
            @endif
        </p>
    </div>
    <div class="portal-metric">
        <p class="portal-metric__label">Appointments</p>
        <p class="portal-metric__value">{{ $metrics['appointment_count'] }}</p>
    </div>
    <div class="portal-metric">
        <p class="portal-metric__label">Loyalty</p>
        <p class="portal-metric__value">{{ $metrics['loyalty_points'] }}</p>
    </div>
    <div class="portal-metric">
        <p class="portal-metric__label">Orders</p>
        <p class="portal-metric__value">{{ $metrics['order_count'] }}</p>
    </div>
</div>

<div class="flex flex-wrap gap-2 mb-8">
    <a href="{{ route('web.booking.create') }}" class="portal-action">Book appointment</a>
    <a href="{{ route('web.store.index') }}" class="portal-action">Shop products</a>
    <a href="{{ route('client.consultations.index') }}" class="portal-action">Request consultation</a>
    <a href="{{ route('client.appointments.index') }}" class="portal-action">My appointments</a>
</div>

@if($upcoming->isNotEmpty())
    <div class="portal-panel mb-6">
        <p class="font-display text-xl mb-4">Upcoming appointments</p>
        <div class="portal-table-wrap">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>Treatment</th>
                        <th>Practitioner</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcoming->take(5) as $appointment)
                        <tr>
                            <td>{{ $appointment->starts_at?->timezone(config('app.timezone'))->format('D, d M Y H:i') }}</td>
                            <td>{{ $appointment->treatment?->name ?? 'Treatment' }}</td>
                            <td>{{ $appointment->practitioner?->user?->name ?? '—' }}</td>
                            <td>{{ is_object($appointment->status) ? $appointment->status->value : $appointment->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="portal-panel">
        <p class="font-display text-2xl mb-2">No appointments yet</p>
        <p class="text-[var(--color-soft-grey)] mb-4">Book a treatment to see it here with status, payment, and reschedule options.</p>
        <a href="{{ route('web.booking.create') }}" class="portal-action">Book now</a>
    </div>
@endif
@endsection
