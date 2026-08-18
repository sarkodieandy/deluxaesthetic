@extends('student.layouts.app')
@section('title', __('student.nav.payments'))
@section('heading', __('student.nav.payments'))
@section('content')
@if($enrolments->count() > 1)
<nav class="student-panel mb-6" aria-label="Select course payments">
    <p class="text-label mb-3">Course account</p>
    <div class="flex flex-wrap gap-2">
        @foreach($enrolments as $item)
            <a class="student-action {{ $item->is($enrolment) ? 'student-action--active' : '' }}" href="{{ route('student.payments.index', ['enrolment' => $item->id]) }}">{{ $item->course?->name }}</a>
        @endforeach
    </div>
</nav>
@endif
@php($currencyPrefix = ($enrolment->currency ?? 'GHS') === 'USD' ? 'US$' : 'GHS ')
<p class="mb-4 text-sm text-[var(--color-soft-grey)]">Account for <strong>{{ $enrolment->course?->name }}</strong> · {{ $enrolment->reference }}</p>
<div class="student-metric-grid mb-6">
    <div class="student-metric"><p class="student-metric__label">Course fee</p><p class="student-metric__value">{{ $currencyPrefix }}{{ number_format((float) $enrolment->fee, 2) }}</p></div>
    <div class="student-metric"><p class="student-metric__label">Paid</p><p class="student-metric__value">{{ $currencyPrefix }}{{ number_format((float) $enrolment->amount_paid, 2) }}</p></div>
    <div class="student-metric"><p class="student-metric__label">Outstanding</p><p class="student-metric__value">{{ $currencyPrefix }}{{ number_format((float) $enrolment->outstanding_balance, 2) }}</p></div>
</div>
@if(! $onlinePaymentEnabled)
    <p class="mb-4 text-[var(--color-soft-grey)]">Pay at the academy reception. Online balance payment is disabled.</p>
@endif
<div class="student-panel">
    @forelse($payments as $payment)
        <p class="mb-2 last:mb-0">{{ $payment->reference }} · {{ ($payment->currency ?? $enrolment->currency ?? 'GHS') === 'USD' ? 'US$' : 'GHS ' }}{{ number_format((float) $payment->amount, 2) }} · <a href="{{ route('student.payments.receipt', $payment->id) }}">Receipt</a></p>
    @empty
        <p class="text-[var(--color-soft-grey)]">No payments recorded yet.</p>
    @endforelse
</div>
@if($instalments->isNotEmpty())
<div class="student-panel mt-6">
    <p class="font-display text-xl mb-3">Instalment schedule</p>
    @foreach($instalments as $instalment)
        <p class="mb-2 last:mb-0">{{ $instalment->due_on?->format('d M Y') ?? 'Date to be confirmed' }} · {{ $currencyPrefix }}{{ number_format((float) $instalment->amount, 2) }} · {{ ucfirst(str_replace('_', ' ', $instalment->status)) }}</p>
    @endforeach
</div>
@endif
@endsection
