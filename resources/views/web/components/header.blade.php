<header class="site-header">
    <div class="header-utility">
        <div class="container-site header-utility__inner">
            <p class="header-utility__meta">
                <span>{{ config('clinic.phone') ?: '+233552248636' }}</span>
                <span class="header-utility__sep" aria-hidden="true">|</span>
                <span>{{ config('clinic.hours') ?: __('web.home.contact_hours_value') }}</span>
            </p>
            <div class="header-utility__actions">
                <a href="{{ route('locale.switch', app()->getLocale() === 'en' ? 'fr' : 'en') }}" class="header-icon-link" aria-label="{{ __('web.language') }}">
                    {{ strtoupper(app()->getLocale() === 'en' ? 'FR' : 'EN') }}
                </a>
                @auth
                    <a href="{{ route(auth()->user()->portalHomeRoute()) }}" class="header-icon-link" aria-label="{{ __('web.account') }}">
                        @include('web.components.icon', ['name' => 'user', 'class' => 'icon icon--sm'])
                        <span class="header-icon-link__label">{{ __('web.account') }}</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="header-icon-link" aria-label="{{ __('Student and staff login') }}">
                        @include('web.components.icon', ['name' => 'user', 'class' => 'icon icon--sm'])
                        <span class="header-icon-link__label">{{ __('Student login') }}</span>
                    </a>
                @endauth
                @php
                    $cartCount = 0;
                    try {
                        $cartCount = app(\App\Services\Cart\CartService::class)->itemCount(
                            app(\App\Services\Cart\CartService::class)->resolve()
                        );
                    } catch (\Throwable) {
                        $cartCount = 0;
                    }
                @endphp
                <a href="{{ route('web.cart.index') }}" class="header-cart" aria-label="{{ __('web.cart') }}">
                    @include('web.components.icon', ['name' => 'cart', 'class' => 'icon icon--sm'])
                    <span class="header-cart__label">{{ __('web.cart') }}</span>
                    <span class="header-cart__count" aria-hidden="true">{{ $cartCount }}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="header-main">
        <div class="container-site header-main__inner">
            <a href="{{ route('web.home') }}" class="site-logo">
                <span class="site-logo__mark">{{ config('clinic.wordmark') }}</span>
                <span class="site-logo__sub">{{ config('clinic.logo_subtitle') }}</span>
            </a>

            <nav class="header-nav" aria-label="{{ __('web.primary_nav') }}">
                @foreach ([
                    'web.home' => __('web.nav.home'),
                    'web.about' => __('web.nav.about'),
                    'web.treatments.index' => __('web.nav.treatments'),
                    'web.practitioners.index' => __('web.nav.practitioners'),
                    'web.academy.index' => __('web.nav.academy'),
                    'web.store.index' => __('web.nav.store'),
                    'web.gallery' => __('web.nav.gallery'),
                    'web.blog.index' => __('web.nav.blog'),
                    'web.contact' => __('web.nav.contact'),
                ] as $route => $label)
                    <a class="nav-link" href="{{ route($route) }}" @if(request()->routeIs($route) || request()->routeIs(str_replace('.index', '.*', $route))) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>

            <div class="header-cta-group">
                <div class="header-cta">
                    <a href="{{ route('web.enrol') }}" class="btn btn-ghost">{{ __('web.enrol') }}</a>
                    <a href="{{ route('web.booking.create') }}" class="btn btn-primary">{{ __('web.book_short') }}</a>
                </div>

                <button
                    type="button"
                    class="header-burger"
                    @click="toggle()"
                    :aria-expanded="open.toString()"
                    aria-controls="mobile-menu"
                >
                    <span class="sr-only">{{ __('web.menu') }}</span>
                    <span class="header-burger__lines" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                    <span class="header-burger__text" aria-hidden="true">{{ __('web.menu') }}</span>
                </button>
            </div>
        </div>
    </div>
</header>
