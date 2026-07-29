@extends('client.layouts.app')
@section('title', 'Orders')
@section('heading', 'My orders')
@section('content')
<div class="portal-panel">
    @forelse($orders as $order)
        <div class="border-b border-[var(--color-border)] py-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="font-medium">{{ $order->number }}</p>
                <p class="text-sm text-[var(--color-soft-grey)]">{{ $order->created_at?->timezone(config('clinic.timezone'))->format('d M Y H:i') }} · {{ $order->status->label() }}</p>
            </div>
            <div class="text-right">
                <p class="mb-2">GHS {{ number_format((float) $order->grand_total, 2) }}</p>
                <a href="{{ route('client.orders.show', $order) }}" class="portal-action">View</a>
            </div>
        </div>
    @empty
        <p class="text-[var(--color-soft-grey)]">No orders yet. <a href="{{ route('web.store.index') }}" class="underline">Browse the store</a>.</p>
    @endforelse
    <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
