@extends('web.layouts.app')

@section('title', 'Booking confirmed — '.config('clinic.name'))

@section('content')
<section class="section">
    <div class="container-site max-w-2xl">
        <p class="text-label mb-3">{{ config('clinic.wordmark') }}</p>
        <h1 class="text-page-title mb-4">Booking request received</h1>
        <div class="h-px w-20 bg-[var(--color-bronze)] mb-8"></div>

        @if (session('status'))
            <p class="mb-6 text-[var(--color-success)]" role="status">{{ session('status') }}</p>
        @endif

        <div class="panel space-y-4 p-8">
            <p class="text-sm text-[var(--color-soft-grey)]">Reference</p>
            <p class="text-2xl font-medium">{{ $appointment->reference }}</p>

            <div class="grid gap-4 sm:grid-cols-2 pt-4 border-t border-[var(--color-border)]">
                <div>
                    <p class="text-label mb-1">Client</p>
                    <p>{{ $appointment->clientProfile?->displayName() }}</p>
                    <p class="text-sm text-[var(--color-soft-grey)]">{{ $appointment->clientProfile?->displayEmail() }}</p>
                    <p class="text-sm text-[var(--color-soft-grey)]">{{ $appointment->clientProfile?->displayPhone() }}</p>
                </div>
                <div>
                    <p class="text-label mb-1">Treatment</p>
                    <p>{{ $appointment->treatment?->name }}</p>
                </div>
                <div>
                    <p class="text-label mb-1">When</p>
                    <p>{{ $appointment->starts_at?->timezone(config('clinic.timezone'))->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-label mb-1">Practitioner</p>
                    <p>{{ $appointment->practitioner?->user?->name }}</p>
                </div>
                <div>
                    <p class="text-label mb-1">Branch</p>
                    <p>{{ $appointment->branch?->name }}</p>
                </div>
                <div>
                    <p class="text-label mb-1">Status</p>
                    <p>{{ ucfirst(str_replace('_', ' ', $appointment->status->value ?? (string) $appointment->status)) }}</p>
                </div>
            </div>
        </div>

        <p class="mt-6 text-sm text-[var(--color-soft-grey)]">
            Our team will confirm your appointment and payment details. No account is required for clinic bookings.
            Academy students can
            <a href="{{ route('web.academy.index') }}" class="underline">create a student portal account</a>
            when enrolling.
        </p>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('web.home') }}" class="btn btn-secondary">Back to home</a>
            <a href="{{ route('web.booking.create') }}" class="btn btn-primary">Book another</a>
        </div>
    </div>
</section>
@endsection
