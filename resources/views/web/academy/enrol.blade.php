@extends('web.layouts.app')

@section('title', __('web.enrol').' — '.config('clinic.name'))

@section('content')
@php
    $whatsapp = preg_replace('/\D+/', '', (string) config('clinic.whatsapp'));
    $academyName = __('web.pages.academy_title');
    $whatsappText = rawurlencode("Hello {$academyName}, I want to enquire about physical enrolment for your academy training.");
@endphp

<section class="section">
    <div class="container-site max-w-5xl">
        <p class="text-label mb-3">{{ __('web.pages.academy_title') }}</p>
        <h1 class="text-page-title mb-4">{{ __('web.enrol') }}</h1>
        <div class="h-px w-20 bg-[var(--color-bronze)] mb-8"></div>
        <p class="max-w-3xl text-[var(--color-soft-grey)] mb-10">
            Academy enrolment is handled for in-person training at our physical location. Send us a WhatsApp message for quick enquiries, or submit the form below and our team will contact you directly.
        </p>

        @if (session('status'))
            <div class="panel mb-6 border-[var(--color-success)] p-4 text-[var(--color-success)]" role="status">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="panel mb-6 border-[var(--color-error)] p-4 text-[var(--color-error)]" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-8 lg:grid-cols-[0.95fr,1.05fr]">
            <article class="panel p-8">
                <p class="text-label mb-3">Fastest option</p>
                <h2 class="text-section mb-4">Chat with the academy team</h2>
                <p class="mb-6 text-[var(--color-soft-grey)]">
                    Use WhatsApp for course enquiries, schedules, fees, entry requirements, and next available intake.
                </p>
                @if ($whatsapp)
                    <a class="btn btn-whatsapp" href="https://wa.me/{{ $whatsapp }}?text={{ $whatsappText }}" target="_blank" rel="noopener noreferrer">
                        @include('web.components.whatsapp-icon', ['class' => 'btn-whatsapp__icon'])
                        WhatsApp the academy
                    </a>
                @endif

                <div class="mt-8 space-y-4 text-[var(--color-soft-grey)]">
                    <div>
                        <p class="text-label mb-1">Location</p>
                        <p>{{ config('clinic.address') }}</p>
                    </div>
                    <div>
                        <p class="text-label mb-1">Hours</p>
                        <p>{{ config('clinic.hours') }}</p>
                    </div>
                    <div>
                        <p class="text-label mb-1">Phone</p>
                        <p><a href="tel:{{ preg_replace('/\s+/', '', (string) config('clinic.phone')) }}">{{ config('clinic.phone') }}</a></p>
                    </div>
                </div>
            </article>

            <form method="POST" action="{{ route('web.enrol.store') }}" class="panel space-y-6 p-8">
                @csrf
                <div>
                    <p class="text-label mb-3">Request a callback</p>
                    <h2 class="text-section mb-3">Send your enrolment enquiry</h2>
                    <p class="text-[var(--color-soft-grey)]">
                        We will review your enquiry and contact you to guide you through physical registration.
                    </p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="text-label mb-2 block" for="name">Full name</label>
                        <input class="field" id="name" name="name" type="text" value="{{ old('name') }}" required>
                    </div>
                    <div>
                        <label class="text-label mb-2 block" for="phone">Phone / WhatsApp</label>
                        <input class="field" id="phone" name="phone" type="text" value="{{ old('phone') }}" required>
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="text-label mb-2 block" for="email">Email</label>
                        <input class="field" id="email" name="email" type="email" value="{{ old('email', auth()->user()?->email) }}" required>
                    </div>
                    <div>
                        <label class="text-label mb-2 block" for="preferred_channel">Preferred contact</label>
                        <select class="field" id="preferred_channel" name="preferred_channel" required>
                            <option value="whatsapp" @selected(old('preferred_channel') === 'whatsapp')>WhatsApp</option>
                            <option value="phone" @selected(old('preferred_channel') === 'phone')>Phone call</option>
                            <option value="email" @selected(old('preferred_channel') === 'email')>Email</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="text-label mb-2 block" for="course_id">Course of interest</label>
                        <select class="field" id="course_id" name="course_id">
                            <option value="">Select a course</option>
                            @foreach ($courses ?? [] as $course)
                                <option value="{{ $course->id }}" @selected((string) old('course_id', $selectedCourseId ?? '') === (string) $course->id)>{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-label mb-2 block" for="preferred_date">Preferred training date</label>
                        <input class="field" id="preferred_date" name="preferred_date" type="date" value="{{ old('preferred_date') }}">
                    </div>
                </div>

                <div>
                    <label class="text-label mb-2 block" for="professional_background">Professional background</label>
                    <input class="field" id="professional_background" name="professional_background" type="text" value="{{ old('professional_background') }}" placeholder="Example: Beauty therapist, nurse, beginner">
                </div>

                <div>
                    <label class="text-label mb-2 block" for="message">Message</label>
                    <textarea class="field" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                </div>

                <label class="flex items-start gap-3 text-sm">
                    <input type="checkbox" name="consent" value="1" required @checked(old('consent'))>
                    <span>I agree that {{ __('web.pages.academy_title') }} may contact me about my training enquiry and physical enrolment.</span>
                </label>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="btn btn-primary">Send enquiry</button>
                    <a href="{{ route('web.academy.index') }}" class="btn btn-secondary">Back to academy</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
