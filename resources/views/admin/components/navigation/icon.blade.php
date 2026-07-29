@props(['name' => 'item'])

@php
    $icons = [
        'dashboard' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="5"/><rect x="3" y="14" width="5" height="7"/><rect x="10" y="14" width="11" height="7"/>',
        'activity' => '<path d="M4 17V7l7-3v13l-7-3zm9-10v10l6-3V4l-6 3z"/>',
        'calendar' => '<rect x="4" y="5" width="16" height="15"/><path d="M8 3v4M16 3v4M4 10h16"/>',
        'users' => '<circle cx="9" cy="8" r="3"/><path d="M3 19v-1a5 5 0 0 1 5-5h2"/><circle cx="17" cy="9" r="2.5"/><path d="M14 19v-1a3.5 3.5 0 0 1 3.5-3.5H19"/>',
        'consultation' => '<rect x="4" y="5" width="16" height="14"/><path d="M8 10h8M8 14h5"/>',
        'treatment' => '<path d="M12 3l1.5 3H17l-2.5 2 1 4L12 10 8.5 12l1-4L7 6h3.5L12 3z"/>',
        'branch' => '<path d="M12 3 4 10v10h6v-6h4v6h6V10L12 3z"/>',
        'academy' => '<path d="M4 9l8-5 8 5v10H4V9z"/><path d="M10 12h4v7h-4z"/>',
        'course' => '<rect x="5" y="4" width="14" height="16"/><path d="M8 8h8M8 12h8M8 16h5"/>',
        'enquiry' => '<path d="M4 7h16M4 11h10M4 15h16"/>',
        'enrolment' => '<path d="M8 5h8l2 4v11H6V9l2-4z"/><path d="M10 13h4M12 11v4"/>',
        'attendance' => '<rect x="5" y="5" width="14" height="14"/><path d="M8 15V11M12 15V9M16 15V12"/>',
        'assessment' => '<rect x="6" y="3" width="12" height="18"/><path d="M9 8h6M9 12h6M9 16h4"/>',
        'certificate' => '<path d="M6 4h12v9l-6 4-6-4V4z"/><path d="M9 7h6"/>',
        'store' => '<path d="M5 8h14l-1 11H6L5 8z"/><path d="M9 8V6h6v2"/>',
        'inventory' => '<rect x="4" y="6" width="16" height="13"/><path d="M4 10h16"/>',
        'order' => '<path d="M7 7h13l-2 12H8L6 7z"/><path d="M9 7V5h6v2"/>',
        'payment' => '<rect x="4" y="7" width="16" height="11"/><path d="M4 11h16M7 15h6"/>',
        'report' => '<path d="M5 19V5h2v14H5zM11 13v6H9v-6h2zM17 9v10h-2V9h2z"/>',
        'content' => '<rect x="5" y="4" width="14" height="16"/><path d="M8 9h8M8 13h8M8 17h5"/>',
        'gallery' => '<rect x="4" y="6" width="16" height="12"/><circle cx="9" cy="11" r="1.5"/><path d="M4 16l4-4 3 3 4-5 5 6"/>',
        'media' => '<rect x="4" y="5" width="16" height="14"/><path d="M9 10l2 2 3-3 3 4v2H7v-5z"/>',
        'notification' => '<path d="M12 4a4 4 0 0 1 4 4v3l2 2v1H6v-1l2-2V8a4 4 0 0 1 4-4z"/><path d="M10 18a2 2 0 0 0 4 0"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5 5l1.5 1.5M17.5 17.5 19 19M5 19l1.5-1.5M17.5 6.5 19 5"/>',
        'shield' => '<path d="M12 3 5 6v6c0 4.5 3 7.8 7 9 4-1.2 7-4.5 7-9V6l-7-3z"/>',
        'item' => '<rect x="6" y="6" width="12" height="12"/>',
    ];
    $inner = $icons[$name] ?? $icons['item'];
@endphp

<svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" stroke-linejoin="miter" aria-hidden="true">
    {!! $inner !!}
</svg>
