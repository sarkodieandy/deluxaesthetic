<div
    class="mobile-menu-backdrop"
    x-show="open"
    x-cloak
    x-transition:enter="mobile-menu-backdrop-enter"
    x-transition:enter-start="mobile-menu-backdrop-enter-start"
    x-transition:enter-end="mobile-menu-backdrop-enter-end"
    x-transition:leave="mobile-menu-backdrop-leave"
    x-transition:leave-start="mobile-menu-backdrop-leave-start"
    x-transition:leave-end="mobile-menu-backdrop-leave-end"
    @click="close()"
    aria-hidden="true"
></div>

<div
    id="mobile-menu"
    class="mobile-menu"
    x-show="open"
    x-cloak
    x-transition:enter="mobile-menu-panel-enter"
    x-transition:enter-start="mobile-menu-panel-enter-start"
    x-transition:enter-end="mobile-menu-panel-enter-end"
    x-transition:leave="mobile-menu-panel-leave"
    x-transition:leave-start="mobile-menu-panel-leave-start"
    x-transition:leave-end="mobile-menu-panel-leave-end"
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('web.menu') }}"
    :aria-hidden="(!open).toString()"
>
    <div class="mobile-menu__toolbar">
        <p class="mobile-menu__title">{{ __('web.menu') }}</p>
        <button type="button" class="mobile-menu__close" @click="close()" aria-label="{{ __('Close') }}">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="mobile-menu__inner" :class="open && 'mobile-menu__inner--open'">
        @foreach ([
            'web.home' => __('web.nav.home'),
            'web.about' => __('web.nav.about'),
            'web.treatments.index' => __('web.nav.treatments'),
            'web.practitioners.index' => __('web.nav.practitioners'),
            'web.academy.index' => __('web.nav.academy'),
            'web.courses.index' => __('web.nav.courses'),
            'web.store.index' => __('web.nav.store'),
            'web.gallery' => __('web.nav.gallery'),
            'web.blog.index' => __('web.nav.blog'),
            'web.contact' => __('web.nav.contact'),
        ] as $route => $label)
            <a
                class="mobile-menu__link"
                href="{{ route($route) }}"
                style="--menu-i: {{ $loop->index }}"
                @click="close()"
                @if(request()->routeIs($route) || request()->routeIs(str_replace('.index', '.*', $route))) aria-current="page" @endif
            >{{ $label }}</a>
        @endforeach
        <div class="mobile-menu__actions">
            @guest
                <a href="{{ route('login') }}" class="btn btn-secondary mobile-menu__action" style="--menu-i: 10" @click="close()">{{ __('Student login') }}</a>
            @else
                <a href="{{ route(auth()->user()->portalHomeRoute()) }}" class="btn btn-secondary mobile-menu__action" style="--menu-i: 10" @click="close()">{{ __('web.account') }}</a>
            @endguest
            <a href="{{ route('web.store.index') }}" class="btn btn-secondary mobile-menu__btn--cart mobile-menu__action" style="--menu-i: 12" @click="close()">
                @include('web.components.icon', ['name' => 'cart', 'class' => 'icon icon--sm'])
                {{ __('web.cart') }}
            </a>
            <a href="{{ route('web.booking.create') }}" class="btn btn-primary mobile-menu__action" style="--menu-i: 13" @click="close()">{{ __('web.book') }}</a>
        </div>
    </div>
</div>
