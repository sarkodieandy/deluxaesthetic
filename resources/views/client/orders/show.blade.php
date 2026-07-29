@extends('client.layouts.app')
@section('title', 'Order '.$order->number)
@section('heading', 'Order '.$order->number)
@section('content')
<div class="portal-panel space-y-4">
    <p><strong>Status:</strong> {{ $order->status->label() }}</p>
    <p><strong>Payment:</strong> {{ ucfirst($order->payment_status->value) }}</p>
    <p><strong>Total:</strong> GHS {{ number_format((float) $order->grand_total, 2) }}</p>
    <ul class="space-y-2 border-t border-[var(--color-border)] pt-4">
        @foreach($order->items as $item)
            <li class="flex justify-between text-sm"><span>{{ $item->name }} × {{ $item->quantity }}</span><span>GHS {{ number_format((float) $item->line_total, 2) }}</span></li>
        @endforeach
    </ul>
    @if($order->statusHistories->isNotEmpty())
        <div class="border-t border-[var(--color-border)] pt-4">
            <h2 class="font-medium mb-3">Tracking</h2>
            <ul class="space-y-2 text-sm">
                @foreach($order->statusHistories->sortByDesc('created_at') as $history)
                    <li>{{ $history->created_at?->timezone(config('clinic.timezone'))->format('d M Y H:i') }} — {{ ucfirst(str_replace('_', ' ', $history->to_status)) }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
