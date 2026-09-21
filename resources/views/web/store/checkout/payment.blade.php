@extends('web.layouts.app')
@section('title', 'Complete payment — '.config('clinic.name'))
@section('content')
<section class="section">
    <div class="container-site max-w-xl">
        <h1 class="text-page-title mb-4">Demo payment</h1>
        <p class="mb-6 text-[var(--color-soft-grey)]">Order {{ $order->number }} · GHS {{ number_format((float) $order->grand_total, 2) }}</p>
        <div class="border border-[var(--color-border)] bg-white p-6">
            <p class="mb-6">This is a demo checkout. No money will be charged. Confirm to try the order confirmation process.</p>
            <form method="POST" action="{{ route('web.payments.mock.complete', $payment->reference) }}">
                @csrf
                <button type="submit" class="btn btn-primary">Confirm demo payment</button>
            </form>
        </div>
    </div>
</section>
@endsection
