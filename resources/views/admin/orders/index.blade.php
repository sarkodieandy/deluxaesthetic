@extends('admin.layouts.app')
@section('title', 'Orders')
@section('heading', 'Orders')
@section('breadcrumb', 'Store / Orders')
@section('content')
@if (session('status'))<p class="mb-4 border border-[var(--color-success)] bg-white px-4 py-3 text-[var(--color-success)]">{{ session('status') }}</p>@endif
<div class="admin-panel mb-6"><div class="admin-panel__head"><h2 class="admin-panel__title">Orders</h2></div><div class="admin-panel__body" style="padding:0;"><table class="admin-table"><thead><tr><th>Order</th><th>Client</th><th>Status</th><th>Total</th><th></th></tr></thead><tbody>@forelse($orders as $order)<tr><td>{{ $order->number }}</td><td>{{ $order->user_name }}</td><td><span class="admin-status admin-status--info">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></td><td>GHS {{ number_format((float)$order->grand_total,2) }}</td><td class="text-right"><a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-secondary">Manage</a></td></tr>@empty<tr><td colspan="5"><div class="admin-empty"><p class="admin-empty__title">No orders yet</p><p class="admin-empty__copy">Store orders will appear here.</p></div></td></tr>@endforelse</tbody></table></div></div>{{ $orders->links() }}
@endsection
