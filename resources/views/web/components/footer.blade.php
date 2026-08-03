<footer class="site-footer">
    <div class="container-site site-footer__grid">
        <div class="site-footer__col site-footer__col--brand">
            <p class="site-footer__brand">{{ config('clinic.name') }}</p>
            <p class="site-footer__tagline">{{ __('web.footer.tagline') }}</p>
        </div>

        <div class="site-footer__col">
            <p class="site-footer__heading">{{ __('web.footer.treatments') }}</p>
            <ul class="site-footer__links">
                <li><a href="{{ route('web.treatments.index') }}">{{ __('web.nav.treatments') }}</a></li>
                <li><a href="{{ route('web.practitioners.index') }}">{{ __('web.nav.practitioners') }}</a></li>
                <li><a href="{{ route('web.booking.create') }}">{{ __('web.book') }}</a></li>
            </ul>
        </div>

        <div class="site-footer__col">
            <p class="site-footer__heading">{{ __('web.footer.academy') }}</p>
            <ul class="site-footer__links">
                <li><a href="{{ route('web.academy.index') }}">{{ __('web.nav.academy') }}</a></li>
                <li><a href="{{ route('web.courses.index') }}">{{ __('web.nav.courses') }}</a></li>
                <li><a href="{{ route('web.academy.student-portal.create') }}">{{ __('web.enrol') }}</a></li>
                <li><a href="{{ route('web.store.index') }}">{{ __('web.nav.store') }}</a></li>
            </ul>
        </div>

        <div class="site-footer__col">
            <p class="site-footer__heading">{{ __('web.footer.visit') }}</p>
            <address class="site-footer__contact">
                <p class="site-footer__contact-line">{{ config('clinic.address') ?: __('web.home.contact_address_value') }}</p>
                <p class="site-footer__contact-line">{{ config('clinic.hours') ?: __('web.home.contact_hours_value') }}</p>
                <p class="site-footer__contact-line">
                    <a href="mailto:{{ config('clinic.email') }}">{{ config('clinic.email') }}</a>
                </p>
                <p class="site-footer__contact-line">
                    <a href="tel:{{ preg_replace('/\s+/', '', (string) config('clinic.phone')) }}">{{ config('clinic.phone') ?: '+233552248636' }}</a>
                </p>
                @if(config('clinic.whatsapp'))
                    <p class="site-footer__contact-line">
                        <a class="site-footer__whatsapp" href="https://wa.me/{{ preg_replace('/\D+/', '', config('clinic.whatsapp')) }}" target="_blank" rel="noopener noreferrer">{{ __('web.whatsapp') }}</a>
                    </p>
                @endif
            </address>
            <ul class="site-footer__links site-footer__links--compact">
                <li><a href="{{ route('web.contact') }}">{{ __('web.nav.contact') }}</a></li>
                <li><a href="{{ route('locale.switch', app()->getLocale() === 'en' ? 'fr' : 'en') }}">
                    {{ app()->getLocale() === 'en' ? 'Français' : 'English' }}
                </a></li>
            </ul>
        </div>
    </div>

    <div class="container-site site-footer__bottom">
        <p>&copy; {{ date('Y') }} {{ config('clinic.name') }}. {{ __('web.footer.rights') }}</p>
        <p class="site-footer__legal">{{ __('web.footer.privacy') }} · {{ __('web.footer.terms') }}</p>
    </div>
</footer>
