@extends('web.layouts.app')
@section('title', __('web.pages.contact_title').' — '.config('clinic.name'))
@section('content')
@include('web.components.page-intro', [
    'eyebrow' => __('web.pages.contact_eyebrow'),
    'title' => __('web.pages.contact_title'),
    'lead' => __('web.pages.contact_lead'),
])
<section class="catalogue-shell">
    <div class="container-site contact-band__layout">
        @include('web.components.contact-details')
        <div class="contact-band__map">
            <p class="text-label">{{ __('web.home.map_label') }}</p>
            @include('web.components.contact-map')
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('web.booking.create') }}" class="btn btn-primary">{{ __('web.book') }}</a>
                @if(config('clinic.whatsapp'))
                    <a class="btn btn-whatsapp" href="https://wa.me/{{ preg_replace('/\D+/', '', config('clinic.whatsapp')) }}" target="_blank" rel="noopener noreferrer">
                        @include('web.components.whatsapp-icon', ['class' => 'btn-whatsapp__icon'])
                        {{ __('web.whatsapp') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
