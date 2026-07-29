@extends('web.layouts.app')
@section('title', 'Processing payment — '.config('clinic.name'))
@section('content')
<section class="section">
    <div class="container-site max-w-xl text-center">
        <h1 class="text-page-title mb-4">Processing payment</h1>
        <p class="mb-6 text-[var(--color-soft-grey)]">Order {{ $order->number }}. Please do not refresh this page.</p>
        <a href="{{ route('web.checkout.show') }}" class="btn btn-secondary">Return to checkout</a>
    </div>
</section>
@endsection
