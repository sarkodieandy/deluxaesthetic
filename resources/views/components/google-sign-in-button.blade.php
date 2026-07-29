@props([
    'href' => route('auth.google.redirect'),
    'label' => null,
])

@php
    $label = $label ?? __('auth.google.continue');
@endphp

<a href="{{ $href }}" class="auth-google-btn" aria-label="{{ $label }}">
    @include('components.google-icon')
    <span class="auth-google-btn__text">{{ $label }}</span>
</a>
