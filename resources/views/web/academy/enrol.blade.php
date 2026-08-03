@extends('web.layouts.app')

@section('title', 'Academy Enrolment — '.config('clinic.name'))
@section('meta_description', 'Start your professional aesthetics training journey at De Luxe Aesthetic Academy in Accra. Enquire about courses, fees, schedules and physical enrolment.')

@section('content')
@php
    $whatsapp = preg_replace('/\D+/', '', (string) config('clinic.whatsapp'));
    $academyName = __('web.pages.academy_title');
    $whatsappText = rawurlencode("Hello {$academyName}, I want to enquire about physical enrolment for your academy training.");
@endphp

<section class="enrol-hero">
    <div class="enrol-hero__media" aria-hidden="true">
        <img src="{{ asset('assets/web/images/academy/academy-training.webp') }}" alt="" width="1600" height="1067" fetchpriority="high" decoding="async">
    </div>
    <div class="enrol-hero__veil"></div>
    <div class="container-site enrol-hero__inner">
        <div class="enrol-hero__copy reveal">
            <p class="enrol-eyebrow">De Luxe Aesthetic Academy · Accra</p>
            <h1>Begin with ambition.<br><em>Graduate with confidence.</em></h1>
            <p class="enrol-hero__lead">Tell us where you want your aesthetics career to go. Our admissions team will help you choose the right training pathway and guide you through physical enrolment.</p>
            <div class="enrol-hero__actions">
                <a href="#enrolment-form" class="btn btn-primary">Start your enquiry</a>
                @if ($whatsapp)
                    <a href="https://wa.me/{{ $whatsapp }}?text={{ $whatsappText }}" class="enrol-hero__text-link" target="_blank" rel="noopener noreferrer">Speak with admissions <span aria-hidden="true">↗</span></a>
                @endif
            </div>
        </div>

        <dl class="enrol-hero__proof reveal reveal-delay-2" aria-label="Academy highlights">
            <div><dt>01</dt><dd>Clinic-led education</dd></div>
            <div><dt>02</dt><dd>Hands-on learning</dd></div>
            <div><dt>03</dt><dd>Recognised certification</dd></div>
        </dl>
    </div>
</section>

<section class="enrol-journey" aria-labelledby="journey-title">
    <div class="container-site enrol-journey__grid">
        <header class="enrol-journey__intro reveal">
            <p class="enrol-eyebrow">Your pathway</p>
            <h2 id="journey-title">A considered start to your professional journey.</h2>
        </header>
        <ol class="enrol-steps">
            <li class="reveal">
                <span>01</span>
                <div><h3>Share your goals</h3><p>Choose a programme and tell us about your experience and ambitions.</p></div>
            </li>
            <li class="reveal reveal-delay-1">
                <span>02</span>
                <div><h3>Speak with admissions</h3><p>We will explain availability, fees, requirements and your best next step.</p></div>
            </li>
            <li class="reveal reveal-delay-2">
                <span>03</span>
                <div><h3>Complete enrolment</h3><p>Visit the academy to confirm registration and prepare for your training.</p></div>
            </li>
        </ol>
    </div>
</section>

<section class="enrol-application" id="enrolment-form">
    <div class="container-site">
        @if (session('status'))
            <div class="enrol-alert enrol-alert--success" role="status">
                <span aria-hidden="true">✓</span><div><strong>Enquiry received</strong><p>{{ session('status') }}</p></div>
            </div>
        @endif

        @if ($errors->any())
            <div class="enrol-alert enrol-alert--error" role="alert">
                <span aria-hidden="true">!</span><div><strong>Please review your details</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            </div>
        @endif

        <div class="enrol-application__grid">
            <aside class="enrol-admissions reveal">
                <div class="enrol-admissions__image">
                    <img src="{{ asset('assets/web/images/academy/academy-injectables.webp') }}" alt="Aesthetic practitioner receiving professional clinical training" loading="lazy" decoding="async">
                    <span>Admissions<br>2026</span>
                </div>
                <div class="enrol-admissions__body">
                    <p class="enrol-eyebrow">Need personal guidance?</p>
                    <h2>Let’s find the right course for you.</h2>
                    <p>Our academy team can help with course selection, schedules, fees and entry requirements.</p>
                    <dl>
                        <div><dt>Call</dt><dd><a href="tel:{{ preg_replace('/\s+/', '', (string) config('clinic.phone')) }}">{{ config('clinic.phone') }}</a></dd></div>
                        <div><dt>Visit</dt><dd>{{ config('clinic.address') }}</dd></div>
                        <div><dt>Hours</dt><dd>{{ config('clinic.hours') }}</dd></div>
                    </dl>
                    @if ($whatsapp)
                        <a class="btn btn-whatsapp enrol-admissions__whatsapp" href="https://wa.me/{{ $whatsapp }}?text={{ $whatsappText }}" target="_blank" rel="noopener noreferrer">
                            @include('web.components.whatsapp-icon', ['class' => 'btn-whatsapp__icon'])
                            WhatsApp admissions
                        </a>
                    @endif
                </div>
            </aside>

            <form method="POST" action="{{ route('web.enrol.store') }}" class="enrol-form reveal reveal-delay-1" novalidate>
                @csrf
                <header class="enrol-form__header">
                    <p class="enrol-eyebrow">Enrolment enquiry</p>
                    <h2>Tell us about yourself.</h2>
                    <p>Complete the form and our admissions team will contact you. Fields marked <span>*</span> are required.</p>
                </header>

                <fieldset class="enrol-form__section">
                    <legend><span>01</span> Your details</legend>
                    <div class="enrol-form__grid">
                        <div class="enrol-control">
                            <label for="name">Full name <span>*</span></label>
                            <input id="name" name="name" type="text" value="{{ old('name', auth()->user()?->name) }}" autocomplete="name" required @class(['is-invalid' => $errors->has('name')])>
                            @error('name')<small>{{ $message }}</small>@enderror
                        </div>
                        <div class="enrol-control">
                            <label for="phone">Phone / WhatsApp <span>*</span></label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone', auth()->user()?->phone) }}" autocomplete="tel" inputmode="tel" placeholder="+233" required @class(['is-invalid' => $errors->has('phone')])>
                            @error('phone')<small>{{ $message }}</small>@enderror
                        </div>
                        <div class="enrol-control">
                            <label for="email">Email address <span>*</span></label>
                            <input id="email" name="email" type="email" value="{{ old('email', auth()->user()?->email) }}" autocomplete="email" required @class(['is-invalid' => $errors->has('email')])>
                            @error('email')<small>{{ $message }}</small>@enderror
                        </div>
                        <div class="enrol-control">
                            <label for="preferred_channel">Preferred contact <span>*</span></label>
                            <select id="preferred_channel" name="preferred_channel" required @class(['is-invalid' => $errors->has('preferred_channel')])>
                                <option value="whatsapp" @selected(old('preferred_channel', 'whatsapp') === 'whatsapp')>WhatsApp</option>
                                <option value="phone" @selected(old('preferred_channel') === 'phone')>Phone call</option>
                                <option value="email" @selected(old('preferred_channel') === 'email')>Email</option>
                            </select>
                            @error('preferred_channel')<small>{{ $message }}</small>@enderror
                        </div>
                    </div>
                </fieldset>

                <fieldset class="enrol-form__section">
                    <legend><span>02</span> Your training goals</legend>
                    <div class="enrol-form__grid">
                        <div class="enrol-control">
                            <label for="course_id">Course of interest</label>
                            <select id="course_id" name="course_id" @class(['is-invalid' => $errors->has('course_id')])>
                                <option value="">I need help choosing</option>
                                @foreach ($courses ?? [] as $course)
                                    <option value="{{ $course->id }}" @selected((string) old('course_id', $selectedCourseId ?? '') === (string) $course->id)>{{ $course->name }}</option>
                                @endforeach
                            </select>
                            @error('course_id')<small>{{ $message }}</small>@enderror
                        </div>
                        <div class="enrol-control">
                            <label for="preferred_date">Preferred training date</label>
                            <input id="preferred_date" name="preferred_date" type="date" min="{{ now()->toDateString() }}" value="{{ old('preferred_date') }}" @class(['is-invalid' => $errors->has('preferred_date')])>
                            @error('preferred_date')<small>{{ $message }}</small>@enderror
                        </div>
                        <div class="enrol-control enrol-control--wide">
                            <label for="professional_background">Professional background</label>
                            <input id="professional_background" name="professional_background" type="text" value="{{ old('professional_background') }}" placeholder="For example: beauty therapist, nurse or complete beginner" @class(['is-invalid' => $errors->has('professional_background')])>
                            @error('professional_background')<small>{{ $message }}</small>@enderror
                        </div>
                        <div class="enrol-control enrol-control--wide">
                            <label for="message">How can we help? <span>*</span></label>
                            <textarea id="message" name="message" rows="5" placeholder="Tell us about the skills you want to develop, your experience and any questions you have." required @class(['is-invalid' => $errors->has('message')])>{{ old('message') }}</textarea>
                            @error('message')<small>{{ $message }}</small>@enderror
                        </div>
                    </div>
                </fieldset>

                <label class="enrol-consent">
                    <input type="checkbox" name="consent" value="1" required @checked(old('consent'))>
                    <span>I agree that {{ __('web.pages.academy_title') }} may contact me about this training enquiry and physical enrolment. <strong>*</strong></span>
                </label>
                @error('consent')<small class="enrol-form__error">{{ $message }}</small>@enderror

                <footer class="enrol-form__footer">
                    <button type="submit" class="btn btn-primary">Submit enquiry <span aria-hidden="true">→</span></button>
                    <p>Your information is treated with care and used only to respond to your enquiry.</p>
                </footer>
            </form>
        </div>
    </div>
</section>

<section class="enrol-aftercare">
    <div class="container-site enrol-aftercare__inner reveal">
        <p class="enrol-eyebrow">Already enrolled?</p>
        <h2>Your learning continues in the student portal.</h2>
        <p>Access course updates, materials, payments and academy support in one dedicated space.</p>
        <a href="{{ route('login') }}" class="btn btn-secondary">Student portal login</a>
    </div>
</section>
@endsection
