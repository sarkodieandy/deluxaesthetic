@extends('web.layouts.app')
@section('title', __('web.pages.contact_title').' — '.config('clinic.name'))
@section('meta_description', 'Contact '.config('clinic.name').' for aesthetic treatments, academy training, product support and appointments in East Legon, Accra.')

@php
    $phone = config('clinic.phone');
    $email = config('clinic.email');
    $whatsapp = config('clinic.whatsapp');
    $phoneDigits = $phone ? preg_replace('/\D+/', '', $phone) : '';
    $whatsappDigits = $whatsapp ? preg_replace('/\D+/', '', $whatsapp) : '';
@endphp

@section('content')
<section class="contact-v2-hero">
    <div class="container-site contact-v2-hero__grid">
        <div class="contact-v2-hero__copy reveal">
            <p class="text-label">Contact De Luxe</p>
            <h1>Let’s begin with<br><em>a conversation.</em></h1>
            <p>Whether you are considering a treatment, exploring academy training or need help with an order, our team is here to guide you.</p>
            <div class="contact-v2-hero__actions">
                <a href="{{ route('web.booking.create') }}" class="btn btn-primary">Book a consultation</a>
                @if($whatsappDigits)
                    <a href="https://wa.me/{{ $whatsappDigits }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">Chat on WhatsApp</a>
                @endif
            </div>
            <div class="contact-v2-hero__availability">
                <span>Open</span><strong>{{ config('clinic.hours') }}</strong>
            </div>
        </div>
        <div class="contact-v2-hero__visual reveal reveal-delay-2">
            <img src="{{ asset('assets/web/images/gallery/clinic-ambiance.jpg') }}" alt="The welcoming interior at De Luxe Aesthetic Clinic">
            <div><span>Visit us</span><strong>East Legon<br>Accra, Ghana</strong></div>
        </div>
    </div>
</section>

<section class="contact-v2-routes">
    <div class="container-site">
        <header class="contact-v2-heading reveal">
            <div><p class="text-label">How can we help?</p><h2>Choose the right<br>conversation.</h2></div>
            <p>Reach the team most relevant to your enquiry, or contact reception if you are unsure where to begin.</p>
        </header>
        <div class="contact-v2-routes__grid">
            <article class="reveal">
                <span>01</span><p class="text-label">Clinic</p><h3>Treatments & consultations</h3>
                <p>Ask about suitability, availability, preparation or follow-up for aesthetic and spa treatments.</p>
                <a href="{{ route('web.booking.create') }}">Book an appointment →</a>
            </article>
            <article class="reveal">
                <span>02</span><p class="text-label">Academy</p><h3>Training & admissions</h3>
                <p>Discuss Botox, fillers, advanced skin training, certification and physical enrolment.</p>
                <a href="{{ route('web.enrol') }}">Make an academy enquiry →</a>
            </article>
            <article class="reveal">
                <span>03</span><p class="text-label">Store</p><h3>Products & orders</h3>
                <p>Get help choosing products, checking an order or arranging clinic pickup and delivery.</p>
                <a href="{{ route('web.store.index') }}">Visit the store →</a>
            </article>
        </div>
    </div>
</section>

<section class="contact-v2-details">
    <div class="container-site contact-v2-details__grid">
        <div class="contact-v2-details__copy reveal">
            <p class="text-label">Reach our team</p>
            <h2>We’re here when<br>you’re ready.</h2>
            <div class="contact-v2-details__list">
                <div><span>Call</span>@if($phone)<a href="tel:+{{ $phoneDigits }}">{{ $phone }}</a>@endif</div>
                <div><span>Email</span>@if($email)<a href="mailto:{{ $email }}">{{ $email }}</a>@endif</div>
                <div><span>WhatsApp</span>@if($whatsappDigits)<a href="https://wa.me/{{ $whatsappDigits }}" target="_blank" rel="noopener noreferrer">{{ $whatsapp }} ↗</a>@endif</div>
                <div><span>Opening hours</span><p>{{ config('clinic.hours') }}</p></div>
            </div>
            <p class="contact-v2-details__note">For medical emergencies, please contact the appropriate emergency service. Website messages and WhatsApp are not monitored as emergency channels.</p>
        </div>
        <div class="contact-v2-details__image reveal reveal-delay-2">
            <img src="{{ asset('assets/web/images/hero/spa-treatment-room.jpg') }}" alt="A calm treatment space at De Luxe" loading="lazy">
            <blockquote>“Every confident decision begins with clear, honest guidance.”</blockquote>
        </div>
    </div>
</section>

<section class="contact-v2-location">
    <div class="container-site">
        <header class="contact-v2-heading contact-v2-heading--location reveal">
            <div><p class="text-label">Find De Luxe</p><h2>Visit us in<br>East Legon.</h2></div>
            <div><p>{{ config('clinic.address') }}</p>@if(config('clinic.map_link'))<a href="{{ config('clinic.map_link') }}" target="_blank" rel="noopener noreferrer">Open in Google Maps ↗</a>@endif</div>
        </header>
        <div class="contact-v2-map reveal">
            @include('web.components.contact-map')
        </div>
        <div class="contact-v2-location__tips">
            <div><span>01</span><strong>Before your visit</strong><p>Please arrive a few minutes early for consultation and paperwork.</p></div>
            <div><span>02</span><strong>Appointments</strong><p>Advance booking is recommended so we can reserve the right practitioner and time.</p></div>
            <div><span>03</span><strong>Academy visits</strong><p>Contact admissions before visiting to confirm enrolment guidance and class availability.</p></div>
        </div>
    </div>
</section>

<section class="contact-v2-cta">
    <div class="container-site contact-v2-cta__inner reveal">
        <p class="text-label">Prefer to speak now?</p>
        <h2>Your next step is<br>only a message away.</h2>
        <p>Chat with our team on WhatsApp or reserve a consultation online.</p>
        <div>
            @if($whatsappDigits)<a href="https://wa.me/{{ $whatsappDigits }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">Message on WhatsApp</a>@endif
            <a href="{{ route('web.booking.create') }}" class="btn btn-secondary">Book consultation</a>
        </div>
    </div>
</section>
@endsection
