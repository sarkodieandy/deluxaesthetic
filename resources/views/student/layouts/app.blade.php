<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Student portal') — {{ config('clinic.name') }}</title>
    @vite(['resources/css/portals/student.css', 'resources/js/portals/student.js'])
</head>
<body class="portal-student">
<div class="student-shell has-sidebar-open" data-student-shell>
    <aside class="student-nav" id="student-sidebar" data-student-sidebar aria-label="Student navigation">
        <div class="student-nav__header">
            <div class="student-nav__brand-block">
                <p class="student-nav__brand">{{ __('web.pages.academy_title') }}</p>
                <p class="student-nav__role">{{ __('student.portal.eyebrow') }}</p>
            </div>
            <button type="button" class="student-nav__collapse" data-student-collapse aria-expanded="true" title="{{ __('student.portal.toggle_sidebar') }}">
                <span aria-hidden="true">‹</span>
            </button>
        </div>
        @php
            $studentUnread = auth()->user()?->unreadNotifications()->count() ?? 0;
        @endphp
        <div class="student-nav__scroll">
            <nav class="student-nav__links">
                <a href="{{ route('student.dashboard') }}" @class(['is-active' => request()->routeIs('student.dashboard')])>{{ __('student.nav.dashboard') }}</a>
                <a href="{{ route('student.course.show') }}" @class(['is-active' => request()->routeIs('student.course.*')])>{{ __('student.nav.course') }}</a>
                <a href="{{ route('student.calendar.index') }}" @class(['is-active' => request()->routeIs('student.calendar.*')])>{{ __('student.nav.calendar') }}</a>
                <a href="{{ route('student.materials.index') }}" @class(['is-active' => request()->routeIs('student.materials.*')])>{{ __('student.nav.materials') }}</a>
                <a href="{{ route('student.attendance.index') }}" @class(['is-active' => request()->routeIs('student.attendance.*')])>{{ __('student.nav.attendance') }}</a>
                <a href="{{ route('student.assignments.index') }}" @class(['is-active' => request()->routeIs('student.assignments.*')])>{{ __('student.nav.assignments') }}</a>
                <a href="{{ route('student.assessments.index') }}" @class(['is-active' => request()->routeIs('student.assessments.*')])>{{ __('student.nav.assessments') }}</a>
                <a href="{{ route('student.payments.index') }}" @class(['is-active' => request()->routeIs('student.payments.*')])>{{ __('student.nav.payments') }}</a>
                <a href="{{ route('student.certificates.index') }}" @class(['is-active' => request()->routeIs('student.certificates.*')])>{{ __('student.nav.certificates') }}</a>
                <a href="{{ route('student.notifications.index') }}" @class(['student-nav__link--notify', 'is-active' => request()->routeIs('student.notifications.*'), 'has-unread' => $studentUnread > 0])>
                    <span class="student-nav__link-label">
                        @include('shared.notifications.bell-icon', ['class' => 'student-nav__bell'])
                        <span>{{ __('student.nav.notifications') }}</span>
                    </span>
                    @if($studentUnread > 0)
                        <span class="notif-badge" aria-label="{{ $studentUnread }} unread">{{ $studentUnread > 99 ? '99+' : $studentUnread }}</span>
                    @endif
                </a>
                <a href="{{ route('student.support.index') }}" @class(['is-active' => request()->routeIs('student.support.*')])>{{ __('student.nav.support') }}</a>
                <a href="{{ route('student.profile.edit') }}" @class(['is-active' => request()->routeIs('student.profile.*')])>{{ __('student.nav.profile') }}</a>
                <a href="{{ route('student.security.index') }}" @class(['is-active' => request()->routeIs('student.security.*')])>{{ __('student.nav.security') }}</a>
            </nav>
        </div>
        <div class="student-nav__resize" data-student-resize role="separator" aria-orientation="vertical" aria-label="{{ __('student.portal.resize_sidebar') }}"></div>
    </aside>
    <div class="student-workspace" data-student-workspace>
        <header class="student-header">
            <div class="student-header__start">
                <button type="button" class="student-nav__menu" data-student-menu aria-expanded="false" aria-controls="student-sidebar">
                    <span class="student-nav__menu-lines" aria-hidden="true"><span></span><span></span><span></span></span>
                    <span class="sr-only">{{ __('student.portal.open_menu') }}</span>
                </button>
                <div>
                    <p class="text-label mb-2">@yield('eyebrow', __('student.portal.eyebrow'))</p>
                    <h1>@yield('heading', 'Overview')</h1>
                </div>
            </div>
            <div class="student-header__actions">
                <a
                    href="{{ route('student.notifications.index') }}"
                    class="student-notify-btn{{ $studentUnread > 0 ? ' has-unread' : '' }}"
                    aria-label="{{ __('student.nav.notifications') }}{{ $studentUnread > 0 ? ' ('.$studentUnread.' unread)' : '' }}"
                >
                    @include('shared.notifications.bell-icon', ['class' => 'student-notify-btn__icon'])
                    @if($studentUnread > 0)
                        <span class="notif-badge notif-badge--pulse" aria-hidden="true">{{ $studentUnread > 99 ? '99+' : $studentUnread }}</span>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="student-action">{{ __('student.portal.logout') }}</button></form>
            </div>
        </header>
        <main class="student-main">
            @if (session('status'))
                <p class="student-flash student-flash--success" role="status">{{ session('status') }}</p>
            @endif
            @if ($errors->any())
                <div class="student-flash student-flash--error" role="alert">
                    <ul class="student-flash__list">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
<div class="student-nav__backdrop" data-student-backdrop hidden aria-hidden="true"></div>
</body>
</html>
