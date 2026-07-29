<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Client portal') — {{ config('clinic.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Manrope:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/portals/client.css', 'resources/js/portals/client.js'])
</head>
<body class="portal-client">
<div class="portal-shell">
    <aside class="portal-nav" aria-label="Client navigation">
        <p class="portal-nav__brand">{{ config('clinic.wordmark') }}</p>
        <p class="portal-nav__role">Client portal</p>
        <nav class="portal-nav__links">
            <a href="{{ route('client.dashboard') }}" @class(['is-active' => request()->routeIs('client.dashboard')])>Overview</a>
            <a href="{{ route('client.appointments.index') }}" @class(['is-active' => request()->routeIs('client.appointments.*')])>Appointments</a>
            <a href="{{ route('client.consultations.index') }}" @class(['is-active' => request()->routeIs('client.consultations.*')])>Consultations</a>
            <a href="{{ route('client.payments.index') }}" @class(['is-active' => request()->routeIs('client.payments.*')])>Payments</a>
            <a href="{{ route('client.orders.index') }}" @class(['is-active' => request()->routeIs('client.orders.*')])>Orders</a>
            <a href="{{ route('client.loyalty.index') }}" @class(['is-active' => request()->routeIs('client.loyalty.*')])>Loyalty</a>
            @php
                $clientUnread = auth()->user()?->unreadNotifications()->count() ?? 0;
            @endphp
            <a href="{{ route('client.notifications.index') }}" @class(['portal-nav__link--notify', 'is-active' => request()->routeIs('client.notifications.*'), 'has-unread' => $clientUnread > 0])>
                <span class="portal-nav__link-label">
                    @include('shared.notifications.bell-icon', ['class' => 'portal-nav__bell'])
                    Notifications
                </span>
                @if($clientUnread > 0)
                    <span class="notif-badge">{{ $clientUnread > 99 ? '99+' : $clientUnread }}</span>
                @endif
            </a>
            <a href="{{ route('client.profile.edit') }}" @class(['is-active' => request()->routeIs('client.profile.*')])>Profile</a>
            <a href="{{ route('web.home') }}">Website</a>
            <a href="{{ route('web.academy.index') }}">Academy / student</a>
        </nav>
    </aside>
    <div class="portal-workspace">
        <header class="portal-header">
            <div>
                <p class="text-label mb-2">@yield('eyebrow', 'Client')</p>
                <h1>@yield('heading', 'Overview')</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('web.booking.create') }}" class="portal-action">Book</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="portal-action">Logout</button>
                </form>
            </div>
        </header>
        <main class="portal-main">
            @if (session('status'))
                <p class="portal-flash" role="status">{{ session('status') }}</p>
            @endif
            @if ($errors->any())
                <div class="portal-flash portal-flash--error" role="alert">
                    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
