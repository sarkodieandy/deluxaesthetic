@extends('admin.layouts.app')

@section('heading', 'Operations dashboard')
@section('title', 'Dashboard')
@section('breadcrumb', 'Overview / Dashboard')

@section('content')
<div class="admin-metric-grid mb-6">
    <div class="admin-metric admin-metric--primary">
        <p class="admin-metric__label">Today’s appointments</p>
        <p class="admin-metric__value">{{ $metrics['appointments_today'] }}</p>
    </div>
    <div class="admin-metric">
        <p class="admin-metric__label">Pending approval</p>
        <p class="admin-metric__value">{{ $metrics['appointments_pending'] }}</p>
    </div>
    <div class="admin-metric">
        <p class="admin-metric__label">Confirmed today</p>
        <p class="admin-metric__value">{{ $metrics['appointments_confirmed_today'] }}</p>
    </div>
    <div class="admin-metric">
        <p class="admin-metric__label">Completed today</p>
        <p class="admin-metric__value">{{ $metrics['appointments_completed_today'] }}</p>
    </div>
    <div class="admin-metric">
        <p class="admin-metric__label">Monthly clinic revenue</p>
        <p class="admin-metric__value">{{ config('clinic.currency', 'GHS') }} {{ number_format($metrics['monthly_clinic_revenue'], 2) }}</p>
    </div>
    <div class="admin-metric">
        <p class="admin-metric__label">Active clients</p>
        <p class="admin-metric__value">{{ $metrics['active_clients'] }}</p>
    </div>
    <div class="admin-metric">
        <p class="admin-metric__label">Active students</p>
        <p class="admin-metric__value">{{ $metrics['active_students'] }}</p>
    </div>
    <div class="admin-metric">
        <p class="admin-metric__label">Open orders</p>
        <p class="admin-metric__value">{{ $metrics['new_orders'] }}</p>
    </div>
    <div class="admin-metric">
        <p class="admin-metric__label">Low stock products</p>
        <p class="admin-metric__value">{{ $metrics['low_stock_products'] }}</p>
    </div>
    <div class="admin-metric">
        <p class="admin-metric__label">Failed payments (30d)</p>
        <p class="admin-metric__value">{{ $metrics['failed_payments'] }}</p>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel__head">
        <h2 class="admin-panel__title">Recent activity</h2>
    </div>
    <div class="admin-panel__body">
        @if (empty($activity))
            <div class="admin-empty">
                <p class="admin-empty__title">No recent activity</p>
                <p class="admin-empty__copy">Audit entries will appear here as staff use the admin modules.</p>
            </div>
        @else
            <ul class="admin-activity-list">
                @foreach ($activity as $entry)
                    <li class="admin-activity-list__item">
                        <span class="admin-activity-list__time">{{ $entry['created_at'] }}</span>
                        <span class="admin-activity-list__text">{{ $entry['description'] }}</span>
                        @if ($entry['user'])
                            <span class="admin-activity-list__user">{{ $entry['user'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
