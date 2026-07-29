<div class="student-panel max-w-2xl">
    <p class="font-display text-2xl mb-3">{{ __('student.portal.no_enrolment_title') }}</p>
    <p class="text-[var(--color-soft-grey)] mb-6">{{ __('student.portal.no_enrolment_copy') }}</p>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('student.dashboard') }}" class="student-action">{{ __('student.portal.back_dashboard') }}</a>
        <a href="{{ route('student.support.index') }}" class="student-action">{{ __('student.nav.support') }}</a>
        <a href="{{ route('student.certificates.index') }}" class="student-action">{{ __('student.nav.certificates') }}</a>
    </div>
</div>
