<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('clinic.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/admin/admin.css', 'resources/js/admin/admin.js'])
</head>
<body class="admin-body">
    <div class="admin-shell" data-admin-shell>
        <aside class="admin-sidebar" id="admin-sidebar" data-admin-sidebar>
            @include('admin.components.navigation.sidebar')
        </aside>
        <button type="button" class="admin-sidebar__backdrop" data-admin-nav-close aria-label="{{ __('Close navigation') }}"></button>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar__intro">
                    <button
                        type="button"
                        class="admin-burger"
                        data-admin-nav-toggle
                        aria-expanded="false"
                        aria-controls="admin-sidebar"
                    >
                        <span class="sr-only">{{ __('web.menu') }}</span>
                        <span class="admin-burger__lines" aria-hidden="true">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                        <span class="admin-burger__text" aria-hidden="true">{{ __('web.menu') }}</span>
                    </button>
                    <div class="admin-topbar__titles">
                        <p class="admin-breadcrumbs">@yield('breadcrumb', 'Admin')</p>
                        <h1>@yield('heading', 'Dashboard')</h1>
                    </div>
                </div>
                <div class="admin-topbar__actions">
                    @can('notifications.view')
                        @php
                            $unreadInbox = auth()->user()?->unreadNotifications()->count() ?? 0;
                        @endphp
                        <a
                            href="{{ route('admin.notifications.index') }}"
                            class="admin-notify-btn{{ $unreadInbox > 0 ? ' has-unread' : '' }}"
                            aria-label="Inbox{{ $unreadInbox > 0 ? ' ('.$unreadInbox.' unread)' : '' }}"
                            title="Inbox"
                        >
                            @include('shared.notifications.bell-icon', ['class' => 'admin-notify-btn__icon'])
                            <span class="admin-notify-btn__label">Inbox</span>
                            @if($unreadInbox > 0)
                                <span class="notif-badge notif-badge--pulse" aria-hidden="true">{{ $unreadInbox > 99 ? '99+' : $unreadInbox }}</span>
                            @endif
                        </a>
                    @endcan
                    <a
                        href="{{ route('web.home') }}"
                        class="btn btn-secondary btn-sm admin-topbar__website"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        @include('admin.components.navigation.icon', ['name' => 'branch'])
                        <span>{{ __('View website') }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm">{{ __('Log out') }}</button>
                    </form>
                </div>
            </header>
            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
