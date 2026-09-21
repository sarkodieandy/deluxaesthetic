@extends('web.layouts.app')
@section('title', 'Processing payment — '.config('clinic.name'))
@section('content')
<section class="section">
    <div class="container-site max-w-xl text-center">
        <h1 class="text-page-title mb-4">Payment pending</h1>
        <p class="mb-6 text-[var(--color-soft-grey)]">We’re waiting for confirmation of payment for order {{ $order->number }}. If you are paying with mobile money, approve the payment on your phone.</p>
        @if ($errors->any())
            <p class="mb-6 text-[var(--color-error)]">{{ $errors->first() }}</p>
        @endif
        <a href="{{ route('web.checkout.status', $order->number) }}" class="btn btn-primary">Check payment status</a>
        <p class="mt-4 text-sm text-[var(--color-soft-grey)]">You do not need to place another order while your payment is pending.</p>
        <a href="{{ route('web.contact') }}" class="btn btn-secondary mt-6">Contact support</a>
    </div>
</section>
@endsection
