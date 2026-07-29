@props(['name', 'class' => 'icon'])

@php
    $icons = [
        'cart' => '<path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.5-7 8-7s8 3 8 7"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'chevron-left' => '<path d="m15 18-6-6 6-6"/>',
        'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
        'facebook' => '<path d="M14 8h3V5h-3c-2.2 0-4 1.8-4 4v2H7v3h3v7h3v-7h3l1-3h-4V9c0-.6.4-1 1-1z"/>',
        'instagram' => '<rect x="3" y="3" width="18" height="18" rx="0"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/>',
        'twitter' => '<path d="M4 4l6.5 8L4 20h2.5l5-6.2L16.5 20H20l-6.8-8.4L20 4h-2.5l-4.7 5.8L7.5 4H4z"/>',
        'linkedin' => '<path d="M6 9v12M6 5v.01M10 21v-7a3 3 0 0 1 6 0v7M10 12V9"/>',
    ];
    $paths = $icons[$name] ?? '';
@endphp

<svg {{ $attributes->merge(['class' => $class]) }} xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" stroke-linejoin="miter" aria-hidden="true" focusable="false">
    {!! $paths !!}
</svg>
