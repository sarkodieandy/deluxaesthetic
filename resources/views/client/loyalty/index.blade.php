@extends('client.layouts.app')
@section('title', 'Loyalty')
@section('heading', 'Loyalty')
@section('content')
<div class="portal-metric-grid mb-6">
    <div class="portal-metric">
        <p class="portal-metric__label">Points balance</p>
        <p class="portal-metric__value">{{ $points }}</p>
    </div>
    <div class="portal-metric">
        <p class="portal-metric__label">Referral code</p>
        <p class="portal-metric__value" style="font-size:1.2rem;">{{ $profile?->referral_code ?? '—' }}</p>
    </div>
</div>
<div class="portal-panel">
    <p class="font-display text-xl mb-2">How loyalty works</p>
    <p class="text-[var(--color-soft-grey)]">Earn points on completed visits and product purchases. Your referral code can be shared with friends — admissions tracks redemptions from the clinic side.</p>
</div>
@endsection
