@extends('web.layouts.app')
@section('title', 'Order confirmed — '.config('clinic.name'))
@section('content')
<section class="section">
    <div class="container-site max-w-3xl">
        <p class="text-label mb-3 text-[var(--color-success)]">Payment successful</p>
        <h1 class="text-page-title mb-4">Thank you for your order</h1>
        <p class="mb-8 text-[var(--color-soft-grey)]">Order <strong>{{ $order->number }}</strong> is confirmed. A confirmation will be sent to {{ $order->customerEmail() }}.</p>
        <div class="border border-[var(--color-border)] bg-white p-6 mb-8 space-y-3">
            <div class="flex justify-between"><span>Status</span><span>{{ $order->status->label() }}</span></div>
            <div class="flex justify-between"><span>Total paid</span><span>GHS {{ number_format((float) $order->grand_total, 2) }}</span></div>
            <div class="flex justify-between"><span>Fulfilment</span><span>{{ ucfirst($order->fulfillment_type->value) }}</span></div>
            <ul class="pt-4 border-t border-[var(--color-border)] space-y-2">
                @foreach($order->items as $item)
                    <li class="flex justify-between text-sm"><span>{{ $item->name }} × {{ $item->quantity }}</span><span>GHS {{ number_format((float) $item->line_total, 2) }}</span></li>
                @endforeach
            </ul>
        </div>
        <div class="flex flex-wrap gap-3">
            @auth
                @if(auth()->user()->hasRole('Client'))
                    <a href="{{ route('client.orders.show', $order) }}" class="btn btn-primary">View my order</a>
                @endif
            @endauth
            <a href="{{ route('web.store.index') }}" class="btn btn-secondary">Continue shopping</a>
            <a href="{{ route('web.contact') }}" class="btn btn-secondary">Contact support</a>
        </div>
    </div>
</section>
@endsection
