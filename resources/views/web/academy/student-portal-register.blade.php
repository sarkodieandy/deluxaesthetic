@extends('web.layouts.app')

@section('title', 'Student Application — '.config('clinic.name'))
@section('meta_description', 'Apply for physical aesthetics training at De Luxe Academy. Admissions reviews every application before student portal access is approved.')
@section('robots', 'noindex, follow')

@section('content')
@php
    $whatsapp = preg_replace('/\D+/', '', (string) config('clinic.whatsapp'));
    $whatsappText = rawurlencode('Hello De Luxe Academy, I would like guidance with my physical training application.');
@endphp

<main class="student-apply">
    <section class="student-apply__hero">
        <div class="container-site student-apply__hero-grid">
            <div class="student-apply__hero-copy reveal">
                <p class="text-label">De Luxe Academy · Admissions</p>
                <h1>Your professional journey<br><em>starts in person.</em></h1>
                <p>Apply for hands-on aesthetics training in Accra. Every application is personally reviewed by admissions before student portal access is activated.</p>
                <div class="student-apply__hero-actions">
                    <a href="#student-application" class="btn btn-primary">Start application</a>
                    <a href="{{ route('login') }}" class="btn btn-secondary">Approved student login</a>
                </div>
                <div class="student-apply__trust">
                    <span>Hands-on training</span><i></i><span>Admissions reviewed</span><i></i><span>Secure portal</span>
                </div>
            </div>
            <div class="student-apply__hero-media reveal reveal-delay-2">
                <img src="{{ asset('assets/web/images/academy/academy-training.webp') }}" alt="Professional hands-on aesthetics training at De Luxe Academy" width="1200" height="1500" fetchpriority="high">
                <div class="student-apply__hero-note"><span>Physical academy</span><strong>Accra, Ghana</strong></div>
            </div>
        </div>
    </section>

    <section class="student-apply__journey" aria-labelledby="application-journey-title">
        <div class="container-site">
            <header class="student-apply__section-head">
                <div><p class="text-label">A considered admissions process</p><h2 id="application-journey-title">Apply. Speak with us. Begin.</h2></div>
                <p>Your account remains pending until our team contacts you and confirms that the physical training pathway is right for you.</p>
            </header>
            <ol class="student-apply__steps">
                <li><span>01</span><div><strong>Submit your interest</strong><p>Tell us about your background and the practical skills you want to develop.</p></div></li>
                <li><span>02</span><div><strong>Admissions contacts you</strong><p>We discuss course fit, physical attendance, availability and the next intake.</p></div></li>
                <li><span>03</span><div><strong>Portal access approved</strong><p>Once approved, use Student login to access your academy portal.</p></div></li>
            </ol>
        </div>
    </section>

    <section class="student-apply__form-section" id="student-application">
        <div class="container-site student-apply__layout">
            <aside class="student-apply__aside reveal">
                <p class="text-label">Before you apply</p>
                <h2>Training shaped around real practice.</h2>
                <p>Choose a programme if you already know your direction, or select guidance and our team will help you decide.</p>
                <dl>
                    <div><dt>Location</dt><dd>{{ config('clinic.address') }}</dd></div>
                    <div><dt>Opening hours</dt><dd>{{ config('clinic.hours') }}</dd></div>
                    <div><dt>Delivery</dt><dd>Physical, supervised training</dd></div>
                </dl>
                @if($whatsapp)
                    <a class="student-apply__whatsapp" href="https://wa.me/{{ $whatsapp }}?text={{ $whatsappText }}" target="_blank" rel="noopener noreferrer">
                        @include('web.components.whatsapp-icon', ['class' => 'student-apply__whatsapp-icon'])
                        <span><small>Need guidance?</small><strong>Chat with admissions</strong></span>
                    </a>
                @endif
                <div class="student-apply__approved">
                    <span aria-hidden="true">✓</span><div><strong>Already approved?</strong><p>Use the email and password from your application.</p><a href="{{ route('login') }}">Open Student login →</a></div>
                </div>
            </aside>

            <div class="student-apply__form-card reveal reveal-delay-1">
                @if(session('status'))
                    <div class="student-apply__notice student-apply__notice--success" role="status"><strong>Application received</strong><p>{{ session('status') }}</p><a href="{{ route('login') }}">Go to Student login</a></div>
                @endif
                @if($errors->any())
                    <div class="student-apply__notice student-apply__notice--error" role="alert"><strong>Please check your application</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                @if($loggedInAsClient)
                    <div class="student-apply__notice" role="status">Log out of the current account before submitting a separate student application.</div>
                @endif

                <form method="POST" action="{{ route('web.academy.student-portal.store') }}" class="student-apply__form">
                    @csrf
                    <header><p class="text-label">Student application</p><h2>Tell us about your goals.</h2><p>Fields marked with an asterisk are required. Your login stays inactive until admissions approval.</p></header>

                    <fieldset><legend><span>01</span> Your details</legend><div class="student-apply__fields">
                        <label class="student-apply__field student-apply__field--wide"><span>Full name *</span><input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name" placeholder="Your full legal name"></label>
                        <label class="student-apply__field"><span>Email address *</span><input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@example.com"></label>
                        <label class="student-apply__field"><span>Phone / WhatsApp *</span><input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required autocomplete="tel" placeholder="+233…"></label>
                    </div></fieldset>

                    <fieldset><legend><span>02</span> Training interest</legend><div class="student-apply__fields">
                        <label class="student-apply__field student-apply__field--wide"><span>Course or pathway</span><select id="course_id" name="course_id"><option value="">I need guidance choosing a course</option>@foreach($courses as $course)<option value="{{ $course->id }}" @selected((string)old('course_id') === (string)$course->id)>{{ $course->name }}</option>@endforeach</select></label>
                        <label class="student-apply__field student-apply__field--wide"><span>Professional background</span><input id="professional_background" name="professional_background" value="{{ old('professional_background') }}" placeholder="Beauty therapist, nurse, beginner…"></label>
                        <label class="student-apply__field student-apply__field--wide"><span>What would you like to study? *</span><textarea id="message" name="message" rows="5" required placeholder="Tell us about the techniques, procedures or career goals that interest you.">{{ old('message') }}</textarea></label>
                    </div></fieldset>

                    <fieldset><legend><span>03</span> Future portal access</legend><div class="student-apply__fields">
                        <label class="student-apply__field"><span>Create password *</span><input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Minimum 8 characters"></label>
                        <label class="student-apply__field"><span>Confirm password *</span><input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Repeat your password"></label>
                    </div><p class="student-apply__field-note">Keep this password safe. It works only after the academy approves your application.</p></fieldset>

                    <label class="student-apply__consent"><input type="checkbox" name="privacy_consent" value="1" @checked(old('privacy_consent')) required><span>I agree that {{ config('clinic.name') }} may contact me about academy training and my student application.</span></label>
                    <button type="submit" class="btn btn-primary student-apply__submit"><span>Submit application</span><span aria-hidden="true">→</span></button>
                    <p class="student-apply__privacy">Submitting does not guarantee admission or immediately activate portal access.</p>
                </form>
            </div>
        </div>
    </section>
</main>
@endsection
