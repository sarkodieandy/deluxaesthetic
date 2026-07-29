@php
    $embed = config('clinic.map_embed_url');
    $link = config('clinic.map_link');
@endphp

<div class="contact-map">
    @if($embed)
        <iframe
            class="contact-map__iframe"
            src="{{ $embed }}"
            title="{{ __('web.home.map_label') }} — {{ config('clinic.name') }}"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            allowfullscreen
        ></iframe>
    @else
        <p class="contact-map__fallback">{{ __('web.home.map_placeholder') }}</p>
    @endif

    @if($link)
        <a class="contact-map__directions" href="{{ $link }}" target="_blank" rel="noopener noreferrer">
            {{ __('web.home.map_directions') }}
        </a>
    @endif
</div>
