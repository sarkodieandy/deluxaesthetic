@extends('student.layouts.app')
@section('title', __('student.nav.payments'))
@section('heading', __('student.nav.payments'))
@section('content')
<div class="student-metric-grid mb-6">
    <div class="student-metric"><p class="student-metric__label">Course fee</p><p class="student-metric__value">GHS {{ number_format((float) $enrolment->fee, 2) }}</p></div>
    <div class="student-metric"><p class="student-metric__label">Paid</p><p class="student-metric__value">GHS {{ number_format((float) $enrolment->amount_paid, 2) }}</p></div>
    <div class="student-metric"><p class="student-metric__label">Outstanding</p><p class="student-metric__value">GHS {{ number_format((float) $enrolment->outstanding_balance, 2) }}</p></div>
</div>
@if(! $onlinePaymentEnabled)
    <p class="mb-4 text-[var(--color-soft-grey)]">Pay at the academy reception. Online balance payment is disabled.</p>
@endif
<div class="student-panel">
    @forelse($payments as $payment)
        <p class="mb-2 last:mb-0">{{ $payment->reference }} · GHS {{ number_format((float) $payment->amount, 2) }} · <a href="{{ route('student.payments.receipt', $payment->id) }}">Receipt</a></p>
    @empty
        <p class="text-[var(--color-soft-grey)]">No payments recorded yet.</p>
    @endforelse
</div>
@endsection
