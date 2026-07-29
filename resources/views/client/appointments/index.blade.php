@extends('client.layouts.app')
@section('title', 'Appointments')
@section('heading', 'Appointments')
@section('content')
<div class="flex flex-wrap gap-2 mb-6">
    <a href="{{ route('web.booking.create') }}" class="portal-action">Book new appointment</a>
</div>
<div class="portal-panel portal-table-wrap">
    <table class="portal-table">
        <thead>
            <tr>
                <th>Reference</th>
                <th>When</th>
                <th>Treatment</th>
                <th>Branch</th>
                <th>Status</th>
                <th>Paid</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appointment)
                <tr>
                    <td>{{ $appointment->reference }}</td>
                    <td>{{ $appointment->starts_at?->timezone(config('app.timezone'))->format('d M Y H:i') }}</td>
                    <td>{{ $appointment->treatment?->name ?? '—' }}</td>
                    <td>{{ $appointment->branch?->name ?? '—' }}</td>
                    <td>{{ is_object($appointment->status) ? $appointment->status->value : $appointment->status }}</td>
                    <td>GHS {{ number_format((float) $appointment->amount_paid, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No appointments yet. Book your first treatment to get started.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
