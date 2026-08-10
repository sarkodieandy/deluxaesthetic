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
        <a class="mobile-menu__link" href="{{ route('web.home') }}" style="--menu-i: 0" @click="close()" @if(request()->routeIs('web.home')) aria-current="page" @endif>Home</a>
        <a class="mobile-menu__link mobile-menu__link--pillar" href="{{ route('web.clinical.index') }}" style="--menu-i: 1" @click="close()" @if(request()->routeIs('web.clinical.index') || request()->routeIs('web.treatments.*')) aria-current="page" @endif>Clinical Procedures</a>
        <a class="mobile-menu__link mobile-menu__link--pillar" href="{{ route('web.academy.index') }}" style="--menu-i: 2" @click="close()" @if(request()->routeIs('web.academy.*') || request()->routeIs('web.courses.*')) aria-current="page" @endif>Academy</a>
        <a class="mobile-menu__link mobile-menu__link--pillar" href="{{ route('web.store.index') }}" style="--menu-i: 3" @click="close()" @if(request()->routeIs('web.store.*') || request()->routeIs('web.cart.*')) aria-current="page" @endif>Products</a>
        <a class="mobile-menu__link" href="{{ route('web.about') }}" style="--menu-i: 4" @click="close()" @if(request()->routeIs('web.about')) aria-current="page" @endif>About</a>
        <a class="mobile-menu__link" href="{{ route('web.gallery') }}" style="--menu-i: 5" @click="close()" @if(request()->routeIs('web.gallery')) aria-current="page" @endif>Gallery</a>
        <a class="mobile-menu__link" href="{{ route('web.blog.index') }}" style="--menu-i: 6" @click="close()" @if(request()->routeIs('web.blog.*')) aria-current="page" @endif>Blog</a>
        <a class="mobile-menu__link" href="{{ route('web.contact') }}" style="--menu-i: 7" @click="close()" @if(request()->routeIs('web.contact')) aria-current="page" @endif>Contact</a>
        <div class="mobile-menu__actions">
            @guest
                <a href="{{ route('login') }}" class="btn btn-secondary mobile-menu__action" style="--menu-i: 8" @click="close()">{{ __('Student login') }}</a>
            @else
                <a href="{{ route(auth()->user()->portalHomeRoute()) }}" class="btn btn-secondary mobile-menu__action" style="--menu-i: 8" @click="close()">{{ __('web.account') }}</a>
            @endguest
            <a href="{{ route('web.cart.index') }}" class="btn btn-secondary mobile-menu__btn--cart mobile-menu__action" style="--menu-i: 9" @click="close()">
                @include('web.components.icon', ['name' => 'cart', 'class' => 'icon icon--sm'])
                {{ __('web.cart') }}
            </a>
            <a href="{{ route('web.booking.create') }}" class="btn btn-primary mobile-menu__action" style="--menu-i: 10" @click="close()">{{ __('web.book') }}</a>
        </div>
    </div>
</div>
