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
    @if($newCourseEnquiries !== null)
        <a href="{{ route('admin.course-enquiries.index') }}" class="admin-metric admin-metric--primary" style="text-decoration:none;">
            <p class="admin-metric__label">New academy enquiries</p>
            <p class="admin-metric__value">{{ $newCourseEnquiries }}</p>
        </a>
    @endif
    @can('students.view')
        <a href="{{ route('admin.students.index', ['status' => 'pending']) }}" class="admin-metric admin-metric--primary" style="text-decoration:none;">
            <p class="admin-metric__label">Student applications</p>
            <p class="admin-metric__value">{{ $pendingStudentApplications->count() }}</p>
        </a>
    @endcan
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

@php
    $trendSeries = collect($orderTrend['series']);
    $chartWidth = 800;
    $chartHeight = 230;
    $padX = 26;
    $padY = 24;
    $plotWidth = $chartWidth - ($padX * 2);
    $plotHeight = $chartHeight - ($padY * 2);
    $maxOrders = max(1, (int) $trendSeries->max('orders'));
    $maxRevenue = max(1, (float) $trendSeries->max('revenue'));
    $pointX = fn($index) => $padX + (($trendSeries->count() > 1 ? $index / ($trendSeries->count() - 1) : .5) * $plotWidth);
    $orderPoints = $trendSeries->values()->map(fn($row, $index) => $pointX($index).','.($padY + $plotHeight - (($row['orders'] / $maxOrders) * $plotHeight)))->join(' ');
    $revenuePoints = $trendSeries->values()->map(fn($row, $index) => $pointX($index).','.($padY + $plotHeight - (($row['revenue'] / $maxRevenue) * $plotHeight)))->join(' ');
@endphp
<section class="admin-panel mb-6">
    <div class="admin-panel__head">
        <div><h2 class="admin-panel__title">Store performance trends</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">Paid revenue and orders created over the selected period.</p></div>
        <nav class="admin-chart-range" aria-label="Chart period">
            @foreach([7, 30, 90] as $range)<a href="{{ route('admin.dashboard', ['range' => $range]) }}" @class(['is-active' => $orderTrend['days'] === $range])>{{ $range }}d</a>@endforeach
        </nav>
    </div>
    <div class="admin-panel__body">
        <div class="admin-chart-summary">
            <div><span>Orders</span><strong>{{ number_format($orderTrend['total_orders']) }}</strong></div>
            <div><span>Paid revenue</span><strong>{{ config('clinic.currency', 'GHS') }} {{ number_format($orderTrend['total_revenue'], 2) }}</strong></div>
            <div><span>Average order value</span><strong>{{ config('clinic.currency', 'GHS') }} {{ number_format($orderTrend['average_order_value'], 2) }}</strong></div>
        </div>
        <div class="admin-trend-grid">
            <article class="admin-trend-card">
                <header><div><span class="admin-trend-dot admin-trend-dot--orders"></span><strong>Order volume</strong></div><small>Peak {{ $maxOrders }} order{{ $maxOrders === 1 ? '' : 's' }}/day</small></header>
                <div class="admin-line-chart">
                    <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" role="img" aria-label="Order volume trend">
                        <g class="admin-line-chart__grid"><line x1="{{ $padX }}" y1="{{ $padY }}" x2="{{ $chartWidth - $padX }}" y2="{{ $padY }}"/><line x1="{{ $padX }}" y1="{{ $chartHeight / 2 }}" x2="{{ $chartWidth - $padX }}" y2="{{ $chartHeight / 2 }}"/><line x1="{{ $padX }}" y1="{{ $chartHeight - $padY }}" x2="{{ $chartWidth - $padX }}" y2="{{ $chartHeight - $padY }}"/></g>
                        <polyline class="admin-line-chart__area admin-line-chart__area--orders" points="{{ $padX }},{{ $chartHeight - $padY }} {{ $orderPoints }} {{ $chartWidth - $padX }},{{ $chartHeight - $padY }}"/>
                        <polyline class="admin-line-chart__line admin-line-chart__line--orders" points="{{ $orderPoints }}"/>
                        @foreach($trendSeries as $index => $row)<circle class="admin-line-chart__point admin-line-chart__point--orders" cx="{{ $pointX($index) }}" cy="{{ $padY + $plotHeight - (($row['orders'] / $maxOrders) * $plotHeight) }}" r="3"><title>{{ $row['label'] }}: {{ $row['orders'] }} orders</title></circle>@endforeach
                    </svg>
                    <div class="admin-line-chart__labels"><span>{{ $trendSeries->first()['label'] }}</span><span>{{ $trendSeries->get((int) floor(($trendSeries->count() - 1) / 2))['label'] }}</span><span>{{ $trendSeries->last()['label'] }}</span></div>
                </div>
            </article>
            <article class="admin-trend-card">
                <header><div><span class="admin-trend-dot admin-trend-dot--revenue"></span><strong>Paid revenue</strong></div><small>Peak {{ config('clinic.currency', 'GHS') }} {{ number_format($maxRevenue, 0) }}/day</small></header>
                <div class="admin-line-chart">
                    <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" role="img" aria-label="Paid revenue trend">
                        <g class="admin-line-chart__grid"><line x1="{{ $padX }}" y1="{{ $padY }}" x2="{{ $chartWidth - $padX }}" y2="{{ $padY }}"/><line x1="{{ $padX }}" y1="{{ $chartHeight / 2 }}" x2="{{ $chartWidth - $padX }}" y2="{{ $chartHeight / 2 }}"/><line x1="{{ $padX }}" y1="{{ $chartHeight - $padY }}" x2="{{ $chartWidth - $padX }}" y2="{{ $chartHeight - $padY }}"/></g>
                        <polyline class="admin-line-chart__area admin-line-chart__area--revenue" points="{{ $padX }},{{ $chartHeight - $padY }} {{ $revenuePoints }} {{ $chartWidth - $padX }},{{ $chartHeight - $padY }}"/>
                        <polyline class="admin-line-chart__line admin-line-chart__line--revenue" points="{{ $revenuePoints }}"/>
                        @foreach($trendSeries as $index => $row)<circle class="admin-line-chart__point admin-line-chart__point--revenue" cx="{{ $pointX($index) }}" cy="{{ $padY + $plotHeight - (($row['revenue'] / $maxRevenue) * $plotHeight) }}" r="3"><title>{{ $row['label'] }}: {{ config('clinic.currency', 'GHS') }} {{ number_format($row['revenue'], 2) }}</title></circle>@endforeach
                    </svg>
                    <div class="admin-line-chart__labels"><span>{{ $trendSeries->first()['label'] }}</span><span>{{ $trendSeries->get((int) floor(($trendSeries->count() - 1) / 2))['label'] }}</span><span>{{ $trendSeries->last()['label'] }}</span></div>
                </div>
            </article>
        </div>
    </div>
</section>

@can('course_enquiries.view')
<section class="admin-panel mb-6">
    <div class="admin-panel__head">
        <div>
            <h2 class="admin-panel__title">Latest academy enquiries</h2>
            <p class="mt-1 text-sm text-[var(--admin-text-muted)]">New training enquiries submitted from the public enrolment page.</p>
        </div>
        <a href="{{ route('admin.course-enquiries.index') }}" class="btn btn-secondary btn-sm">View all enquiries</a>
    </div>
    <div class="admin-panel__body">
        @forelse($latestCourseEnquiries as $enquiry)
            <a href="{{ route('admin.course-enquiries.show', $enquiry) }}" class="admin-activity-list__item" style="text-decoration:none;">
                <span class="admin-activity-list__time">{{ $enquiry->created_at->diffForHumans() }}</span>
                <span class="admin-activity-list__text"><strong>{{ $enquiry->full_name }}</strong> · {{ $enquiry->course?->name ?? 'General academy enquiry' }}</span>
                <span class="admin-status admin-status--info">{{ ucfirst($enquiry->status) }}</span>
            </a>
        @empty
            <div class="admin-empty">
                <p class="admin-empty__title">No academy enquiries yet</p>
                <p class="admin-empty__copy">New submissions from the enrolment page will appear here and in Communication → Inbox.</p>
            </div>
        @endforelse
    </div>
</section>
@endcan

@can('students.view')
<section class="admin-panel mb-6">
    <div class="admin-panel__head">
        <div><h2 class="admin-panel__title">Student applications awaiting approval</h2><p class="mt-1 text-sm text-[var(--admin-text-muted)]">Contact each applicant before activating portal access.</p></div>
        <a href="{{ route('admin.students.index', ['status' => 'pending']) }}" class="btn btn-secondary btn-sm">Review applications</a>
    </div>
    <div class="admin-panel__body">
        @forelse($pendingStudentApplications->take(5) as $application)
            <a href="{{ route('admin.students.index', ['status' => 'pending']) }}" class="admin-activity-list__item" style="text-decoration:none;">
                <span class="admin-activity-list__time">{{ $application->created_at->diffForHumans() }}</span>
                <span class="admin-activity-list__text"><strong>{{ $application->user?->name }}</strong> · {{ $application->user?->phone }}</span>
                <span class="admin-status admin-status--warning">Pending</span>
            </a>
        @empty
            <div class="admin-empty"><p class="admin-empty__title">No pending student applications</p><p class="admin-empty__copy">New academy account requests will appear here and trigger the notification indicator.</p></div>
        @endforelse
    </div>
</section>
@endcan

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
