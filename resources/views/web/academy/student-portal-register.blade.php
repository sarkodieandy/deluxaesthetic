@extends('web.layouts.app')

@section('title', __('web.student_portal.title').' — '.config('clinic.name'))

@section('content')
@php
    $whatsapp = preg_replace('/\D+/', '', (string) config('clinic.whatsapp'));
    $academyName = __('web.pages.academy_title');
    $whatsappText = rawurlencode("Hello {$academyName}, I have created my student portal account and need help with physical enrolment.");
@endphp

<section class="section">
    <div class="container-site max-w-5xl">
        <p class="text-label mb-3">{{ __('web.pages.academy_eyebrow') }}</p>
        <h1 class="text-page-title mb-4">{{ __('web.student_portal.title') }}</h1>
        <div class="h-px w-20 bg-[var(--color-bronze)] mb-8"></div>
        <p class="max-w-3xl text-[var(--color-soft-grey)] mb-10">
            {{ __('web.student_portal.lead') }}
        </p>

        @if ($loggedInAsClient)
            <div class="panel mb-6 border-[var(--color-warning)] p-4 text-[var(--color-soft-grey)]" role="status">
                {{ __('web.student_portal.client_logged_in') }}
            </div>
        @endif

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
                <p class="text-label mb-3">{{ __('web.student_portal.steps_label') }}</p>
                <h2 class="text-section mb-4">{{ __('web.student_portal.steps_title') }}</h2>
                <ol class="space-y-4 text-[var(--color-soft-grey)] list-decimal pl-5">
                    <li>{{ __('web.student_portal.step_1') }}</li>
                    <li>{{ __('web.student_portal.step_2') }}</li>
                    <li>{{ __('web.student_portal.step_3') }}</li>
                </ol>

                <div class="mt-8 space-y-4 text-[var(--color-soft-grey)]">
                    <div>
                        <p class="text-label mb-1">{{ __('web.home.contact_address') }}</p>
                        <p>{{ config('clinic.address') }}</p>
                    </div>
                    <div>
                        <p class="text-label mb-1">{{ __('web.home.contact_hours') }}</p>
                        <p>{{ config('clinic.hours') }}</p>
                    </div>
                </div>

                @if ($whatsapp)
                    <a class="btn btn-whatsapp mt-8" href="https://wa.me/{{ $whatsapp }}?text={{ $whatsappText }}" target="_blank" rel="noopener noreferrer">
                        @include('web.components.whatsapp-icon', ['class' => 'btn-whatsapp__icon'])
                        {{ __('web.student_portal.whatsapp') }}
                    </a>
                @endif

                <p class="auth-switch mt-8 mb-0">
                    {{ __('web.student_portal.already_have_account') }}
                    <a class="auth-link" href="{{ route('login') }}">{{ __('web.student_portal.sign_in') }}</a>
                </p>
            </article>

            <form method="POST" action="{{ route('web.academy.student-portal.store') }}" class="panel space-y-6 p-8">
                @csrf
                <div>
                    <p class="text-label mb-3">{{ __('web.student_portal.form_label') }}</p>
                    <h2 class="text-section mb-3">{{ __('web.student_portal.form_title') }}</h2>
                    <p class="text-[var(--color-soft-grey)]">{{ __('web.student_portal.form_copy') }}</p>
                </div>

                @if (\App\Support\GoogleAuth::enabled())
                    <x-google-sign-in-button :label="__('auth.google.continue_student')" />
                    <div class="auth-divider my-2"><span>{{ __('or') }}</span></div>
                @endif

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="text-label mb-2 block" for="name">{{ __('web.student_portal.full_name') }}</label>
                        <input class="field" id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name">
                    </div>
                    <div>
                        <label class="text-label mb-2 block" for="email">{{ __('web.student_portal.email') }}</label>
                        <input class="field" id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    <div>
                        <label class="text-label mb-2 block" for="phone">{{ __('web.student_portal.phone') }}</label>
                        <input class="field" id="phone" name="phone" type="text" value="{{ old('phone') }}" required autocomplete="tel">
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="text-label mb-2 block" for="password">{{ __('web.student_portal.password') }}</label>
                        <input class="field" id="password" name="password" type="password" required autocomplete="new-password">
                    </div>
                    <div>
                        <label class="text-label mb-2 block" for="password_confirmation">{{ __('web.student_portal.password_confirm') }}</label>
                        <input class="field" id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
                    </div>
                </div>

                <label class="flex gap-3 items-start text-sm text-[var(--color-soft-grey)]">
                    <input class="mt-1" type="checkbox" name="privacy_consent" value="1" @checked(old('privacy_consent')) required>
                    <span>{{ __('web.student_portal.privacy', ['brand' => config('clinic.name')]) }}</span>
                </label>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="submit" class="btn btn-primary">{{ __('web.student_portal.submit') }}</button>
                    <a href="{{ route('web.courses.index') }}" class="btn btn-secondary">{{ __('web.nav.courses') }}</a>
                    <a href="{{ route('web.enrol') }}" class="btn btn-secondary">{{ __('web.enrol') }}</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
