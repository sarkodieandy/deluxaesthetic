@php
    $navigation = $adminNavigation ?? \App\Support\AdminNavigation::forUser(auth()->user());
    $user = auth()->user();
    $unreadInbox = $user?->unreadNotifications()->count() ?? 0;
    $groupIcons = [
        'overview' => 'dashboard',
        'clinic' => 'treatment',
        'academy' => 'academy',
        'store' => 'store',
        'finance' => 'payment',
        'marketing' => 'sparkles',
        'content' => 'content',
        'communication' => 'mail',
        'system' => 'settings',
    ];
@endphp

<header class="admin-sidebar__header">
    <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__logo">
        <span class="admin-sidebar__monogram" aria-hidden="true">DL</span>
        <span class="admin-sidebar__brand-lockup">
            <span class="admin-sidebar__logo-mark">{{ config('clinic.wordmark') }}</span>
            <span class="admin-sidebar__logo-sub">Clinic administration</span>
        </span>
    </a>
    @if($user)
        <a href="{{ route('admin.profile.edit') }}" class="admin-sidebar__user">
            <span class="admin-sidebar__user-avatar" aria-hidden="true">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            <div class="admin-sidebar__user-text">
                <span class="admin-sidebar__user-name">{{ $user->name }}</span>
                <span class="admin-sidebar__user-role">{{ $user->getRoleNames()->first() ?? 'Staff' }}</span>
            </div>
            <span class="admin-sidebar__user-arrow" aria-hidden="true">›</span>
        </a>
    @endif
</header>

<div class="admin-sidebar__body">
    <nav class="admin-sidebar__nav" aria-label="{{ __('Administration') }}">
        @foreach ($navigation as $group)
            @php($groupActive = collect($group['items'])->contains('active', true))
            <section class="admin-nav-group{{ $groupActive ? ' is-active' : '' }}" data-admin-nav-group data-group-key="{{ $group['key'] }}">
                <button
                    type="button"
                    class="admin-nav-group__toggle"
                    data-admin-group-toggle
                    aria-expanded="true"
                    aria-controls="admin-nav-list-{{ $group['key'] }}"
                >
                    <span class="admin-nav-group__identity">
                        <span class="admin-nav-group__icon">
                            @include('admin.components.navigation.icon', ['name' => $groupIcons[$group['key']] ?? 'item'])
                        </span>
                        <span id="admin-nav-{{ $group['key'] }}" class="admin-nav-group__label">{{ $group['label'] }}</span>
                    </span>
                    <span class="admin-nav-group__meta">
                        <span class="admin-nav-group__count">{{ count($group['items']) }}</span>
                        <svg class="admin-nav-group__chevron" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="m6 8 4 4 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </button>
                <ul class="admin-nav-list" id="admin-nav-list-{{ $group['key'] }}">
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
                                <span class="admin-nav-link__icon-wrap">
                                    @include('admin.components.navigation.icon', ['name' => $item['icon'] ?? 'item'])
                                </span>
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
                        href="{{ route('admin.profile.edit') }}"
                        @class(['admin-nav-link', 'is-active' => request()->routeIs('admin.profile.*')])
                        @if(request()->routeIs('admin.profile.*')) aria-current="page" @endif
                    >
                        <span class="admin-nav-link__icon-wrap">@include('admin.components.navigation.icon', ['name' => 'users'])</span>
                        <span class="admin-nav-link__text">{{ __('Profile') }}</span>
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('admin.account.security') }}"
                        @class(['admin-nav-link', 'is-active' => request()->routeIs('admin.account.*')])
                        @if(request()->routeIs('admin.account.*')) aria-current="page" @endif
                    >
                        <span class="admin-nav-link__icon-wrap">@include('admin.components.navigation.icon', ['name' => 'shield'])</span>
                        <span class="admin-nav-link__text">{{ __('Security & account') }}</span>
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('web.home') }}"
                        class="admin-nav-link admin-nav-link--external"
                        target="_blank"
                        rel="noopener noreferrer"
                        title="{{ __('Opens the public website in a new tab') }}"
                    >
                        <span class="admin-nav-link__icon-wrap">@include('admin.components.navigation.icon', ['name' => 'branch'])</span>
                        <span class="admin-nav-link__text">{{ __('View website') }}</span>
                        <span class="admin-nav-link__external" aria-hidden="true">↗</span>
                    </a>
                </li>
            </ul>
        </section>
    </nav>
</div>
