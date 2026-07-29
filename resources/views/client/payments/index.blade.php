@extends('client.layouts.app')
@section('title', 'Payments')
@section('heading', 'Payments')
@section('content')
<div class="portal-panel portal-table-wrap">
    <table class="portal-table">
        <thead>
            <tr>
                <th>Reference</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Gateway</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->reference }}</td>
                    <td>GHS {{ number_format((float) $payment->amount, 2) }}</td>
                    <td>{{ ucfirst($payment->status) }}</td>
                    <td>{{ ucfirst($payment->gateway) }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($payment->created_at)->format('d M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No payments recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
