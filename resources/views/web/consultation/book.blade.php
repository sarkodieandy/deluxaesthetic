@extends('web.layouts.app')

@section('title', __('web.book').' — '.config('clinic.name'))

@section('content')
<section class="section" x-data="bookingForm()">
    <div class="container-site max-w-3xl">
        <p class="text-label mb-3">{{ config('clinic.wordmark') }}</p>
        <h1 class="text-page-title mb-4">{{ __('web.book') }}</h1>
        <div class="h-px w-20 bg-[var(--color-bronze)] mb-8"></div>

        <p class="mb-6 text-[var(--color-soft-grey)]">
            Book an aesthetic appointment without creating an account. Student portal accounts are only needed when you enrol for academy training.
        </p>

        @if ($errors->any())
            <div class="panel mb-6 border-[var(--color-error)] p-4 text-[var(--color-error)]" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('web.booking.store') }}" class="panel space-y-6 p-8">
            @csrf

            @unless($isAuthenticatedClient)
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="text-label mb-2 block" for="guest_name">Full name</label>
                        <input class="field" id="guest_name" name="guest_name" type="text" value="{{ $guestDefaults['name'] }}" required autocomplete="name">
                    </div>
                    <div>
                        <label class="text-label mb-2 block" for="guest_email">Email</label>
                        <input class="field" id="guest_email" name="guest_email" type="email" value="{{ $guestDefaults['email'] }}" required autocomplete="email">
                    </div>
                    <div>
                        <label class="text-label mb-2 block" for="guest_phone">Phone</label>
                        <input class="field" id="guest_phone" name="guest_phone" type="tel" value="{{ $guestDefaults['phone'] }}" required autocomplete="tel">
                    </div>
                </div>
            @else
                <p class="text-sm text-[var(--color-soft-grey)]">Booking as {{ auth()->user()->name }} ({{ auth()->user()->email }}).</p>
            @endunless

            <div>
                <label class="text-label mb-2 block" for="treatment_id">Treatment</label>
                <select class="field" id="treatment_id" name="treatment_id" x-model="treatmentId" @change="loadSlots()" required>
                    <option value="">Select treatment</option>
                    @foreach ($treatments as $treatment)
                        <option value="{{ $treatment->id }}" @selected(old('treatment_id', $selectedTreatment) == $treatment->id)>
                            {{ $treatment->name }} — GHS {{ number_format((float) $treatment->effectivePrice(), 2) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-label mb-2 block" for="branch_id">Branch</label>
                <select class="field" id="branch_id" name="branch_id" x-model="branchId" @change="loadSlots()" required>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" @selected(old('branch_id', $branches->first()?->id) == $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-label mb-2 block" for="practitioner_profile_id">Practitioner</label>
                <select class="field" id="practitioner_profile_id" name="practitioner_profile_id" x-model="practitionerId" @change="loadSlots()" required>
                    @foreach ($practitioners as $practitioner)
                        <option value="{{ $practitioner->id }}" @selected(old('practitioner_profile_id', $practitioners->first()?->id) == $practitioner->id)>
                            {{ $practitioner->user?->name }} — {{ $practitioner->professional_title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-label mb-2 block" for="date">Date</label>
                <input class="field" type="date" id="date" x-model="date" @change="loadSlots()" :min="minDate" required>
            </div>
            <div>
                <p class="text-label mb-3">Available times</p>
                <p class="mb-3 text-sm text-[var(--color-soft-grey)]" x-show="loading">Checking availability…</p>
                <p class="mb-3 text-sm text-[var(--color-soft-grey)]" x-show="!loading && slots.length === 0">No open slots for this date. Try a weekday.</p>
                <div class="grid grid-cols-3 gap-2 sm:grid-cols-4">
                    <template x-for="slot in slots" :key="slot.starts_at">
                        <button
                            type="button"
                            class="border border-[var(--color-border)] px-3 py-3 text-sm"
                            :class="startsAt === slot.starts_at ? 'bg-[var(--color-charcoal)] text-white border-[var(--color-charcoal)]' : 'bg-white'"
                            @click="startsAt = slot.starts_at"
                            x-text="slot.label"
                        ></button>
                    </template>
                </div>
                <input type="hidden" name="starts_at" :value="startsAtLocal" required>
            </div>
            <div>
                <label class="text-label mb-2 block" for="goals">Consultation notes</label>
                <textarea class="field" id="goals" name="goals" rows="4" required>{{ old('goals') }}</textarea>
            </div>
            <div>
                <label class="text-label mb-2 block" for="client_notes">Additional notes</label>
                <textarea class="field" id="client_notes" name="client_notes" rows="3">{{ old('client_notes') }}</textarea>
            </div>
            <label class="flex items-start gap-3 text-sm">
                <input type="checkbox" name="consent" value="1" required @checked(old('consent'))>
                <span>I accept the consultation and cancellation terms. This form does not provide a medical diagnosis.</span>
            </label>
            <button type="submit" class="btn btn-primary" :disabled="!startsAt">Confirm booking request</button>
        </form>

        <p class="mt-8 text-sm text-[var(--color-soft-grey)]">
            Looking for academy training?
            <a href="{{ route('web.academy.index') }}" class="underline">Create a student portal account</a>
            or
            <a href="{{ route('web.enrol') }}" class="underline">send an enrolment enquiry</a>.
        </p>
    </div>
</section>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bookingForm', () => ({
        treatmentId: @json(old('treatment_id', $selectedTreatment)),
        branchId: @json((string) old('branch_id', $branches->first()?->id)),
        practitionerId: @json((string) old('practitioner_profile_id', $practitioners->first()?->id)),
        date: '',
        slots: [],
        startsAt: '',
        loading: false,
        minDate: new Date().toISOString().slice(0, 10),
        get startsAtLocal() {
            if (!this.startsAt) return '';
            const d = new Date(this.startsAt);
            const pad = (n) => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
        },
        async loadSlots() {
            this.startsAt = '';
            this.slots = [];
            if (!this.treatmentId || !this.branchId || !this.practitionerId || !this.date) return;
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    treatment_id: this.treatmentId,
                    branch_id: this.branchId,
                    practitioner_profile_id: this.practitionerId,
                    date: this.date,
                });
                const res = await fetch(`{{ route('web.booking.slots') }}?${params}`);
                const data = await res.json();
                this.slots = data.slots || [];
            } catch (e) {
                this.slots = [];
            } finally {
                this.loading = false;
            }
        },
    }));
});
</script>
@endsection
