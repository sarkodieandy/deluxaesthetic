@extends('admin.layouts.app')
@section('title', 'Reports')
@section('heading', 'Reports')
@section('breadcrumb', 'Finance / Reports')
@section('content')
<div class="admin-metric-grid mb-6"><div class="admin-metric"><p class="admin-metric__label">Appointments</p><p class="admin-metric__value">{{ $metrics['appointments'] }}</p></div><div class="admin-metric"><p class="admin-metric__label">Enrolments</p><p class="admin-metric__value">{{ $metrics['enrolments'] }}</p></div><div class="admin-metric"><p class="admin-metric__label">Orders</p><p class="admin-metric__value">{{ $metrics['orders'] }}</p></div><div class="admin-metric"><p class="admin-metric__label">Completed payments</p><p class="admin-metric__value">GHS {{ number_format($metrics['payments'], 2) }}</p></div><div class="admin-metric"><p class="admin-metric__label">Low stock products</p><p class="admin-metric__value">{{ $metrics['products_low_stock'] }}</p></div></div>
<div class="admin-panel"><div class="admin-panel__head"><h2 class="admin-panel__title">Operational summary</h2></div><div class="admin-panel__body"><p class="admin-empty__copy">This report page is connected to live appointment, academy, order, payment, and product data. Next step is adding filters and CSV/PDF exports.</p></div></div>
@endsection
