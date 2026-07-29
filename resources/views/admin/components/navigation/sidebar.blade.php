@php
    $navigation = $adminNavigation ?? \App\Support\AdminNavigation::forUser(auth()->user());
    $user = auth()->user();
    $unreadInbox = $user?->unreadNotifications()->count() ?? 0;
@endphp

<header class="admin-sidebar__header">
    <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__logo">
        <span class="admin-sidebar__logo-mark">{{ config('clinic.wordmark') }}</span>
        <span class="admin-sidebar__logo-sub">Administration</span>
    </a>
    @if($user)
        <div class="admin-sidebar__user">
            <span class="admin-sidebar__user-avatar" aria-hidden="true">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            <div class="admin-sidebar__user-text">
                <span class="admin-sidebar__user-name">{{ $user->name }}</span>
                <span class="admin-sidebar__user-role">{{ $user->getRoleNames()->first() ?? 'Staff' }}</span>
            </div>
        </div>
    @endif
</header>

<div class="admin-sidebar__body">
    <nav class="admin-sidebar__nav" aria-label="{{ __('Administration') }}">
        @foreach ($navigation as $group)
            <section class="admin-nav-group" aria-labelledby="admin-nav-{{ $group['key'] }}">
                <h2 id="admin-nav-{{ $group['key'] }}" class="admin-nav-group__label">{{ $group['label'] }}</h2>
                <ul class="admin-nav-list">
                    @foreach ($group['items'] as $item)
                        <li>
                            <a
                                href="{{ $item['url'] }}"
                                @class([
                                    'admin-nav-link',
                                    'admin-nav-link--external' => ! empty($item['external']),
                                    'admin-nav-link--notify' => ($item['route'] ?? '') === 'admin.notifications.index',
                                    'has-unread' => ($item['route'] ?? '') === 'admin.notifications.index' && $unreadInbox > 0,
                                    'is-active' => $item['active'],
                                ])
                                @if($item['active']) aria-current="page" @endif
                                @if(! empty($item['external'])) target="_blank" rel="noopener noreferrer" title="{{ __('Opens the public website in a new tab') }}" @endif
                            >
                                @if(($item['route'] ?? '') === 'admin.notifications.index')
                                    @include('shared.notifications.bell-icon', ['class' => 'admin-nav-link__bell'])
                                @endif
                                <span class="admin-nav-link__text">{{ $item['label'] }}</span>
                                @if(($item['route'] ?? '') === 'admin.notifications.index' && $unreadInbox > 0)
                                    <span class="notif-badge" aria-label="{{ $unreadInbox }} unread">{{ $unreadInbox > 99 ? '99+' : $unreadInbox }}</span>
                                @elseif(! empty($item['external']))
                                    <span class="admin-nav-link__external" aria-hidden="true">↗</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach

        <section class="admin-nav-group admin-nav-group--utility" aria-labelledby="admin-nav-account">
            <h2 id="admin-nav-account" class="admin-nav-group__label">{{ __('Account') }}</h2>
            <ul class="admin-nav-list">
                <li>
                    <a
                        href="{{ route('profile.edit') }}"
                        @class(['admin-nav-link', 'is-active' => request()->routeIs('profile.*')])
                        @if(request()->routeIs('profile.*')) aria-current="page" @endif
                    >{{ __('Profile') }}</a>
                </li>
                <li>
                    <a
                        href="{{ route('web.home') }}"
                        class="admin-nav-link admin-nav-link--external"
                        target="_blank"
                        rel="noopener noreferrer"
                        title="{{ __('Opens the public website in a new tab') }}"
                    >
                        <span class="admin-nav-link__text">{{ __('View website') }}</span>
                        <span class="admin-nav-link__external" aria-hidden="true">↗</span>
                    </a>
                </li>
            </ul>
        </section>
    </nav>
</div>
