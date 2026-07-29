@extends('web.layouts.app')
@section('title', 'Payment failed — '.config('clinic.name'))
@section('content')
<section class="section">
    <div class="container-site max-w-xl">
        <h1 class="text-page-title mb-4">Payment unsuccessful</h1>
        <p class="mb-4 text-[var(--color-soft-grey)]">Reference {{ $reference }}. Your card was not charged for a completed order.</p>
        @if($errors->any())
            <p class="mb-4 text-[var(--color-error)]">{{ $errors->first() }}</p>
        @endif
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('web.checkout.show') }}" class="btn btn-primary">Try again</a>
            <a href="{{ route('web.cart.index') }}" class="btn btn-secondary">Return to cart</a>
            <a href="{{ route('web.contact') }}" class="btn btn-secondary">Contact support</a>
        </div>
    </div>
</section>
@endsection
