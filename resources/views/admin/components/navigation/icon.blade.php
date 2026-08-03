@props(['name' => 'item'])

@php
    $icons = [
        'dashboard' => '<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="5" rx="2"/><rect x="3" y="14" width="5" height="7" rx="2"/><rect x="12" y="12" width="9" height="9" rx="2"/>',
        'activity' => '<path d="M3 12h4l2.4-6 4.2 12 2.3-6H21"/><path d="M5 4.5A9 9 0 1 1 3.5 16"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M8 3v4M16 3v4M3 10h18"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/>',
        'practitioner' => '<circle cx="12" cy="7" r="4"/><path d="M5 21v-2a7 7 0 0 1 14 0v2M12 15v6M9 18h6"/>',
        'consultation' => '<path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/><path d="M8 9h8M8 13h5"/>',
        'treatment' => '<path d="M12 2v20M2 12h20"/><path d="M5.6 5.6 18.4 18.4M18.4 5.6 5.6 18.4"/><circle cx="12" cy="12" r="5"/>',
        'branch' => '<path d="M3 21h18M5 21V9l7-6 7 6v12M9 21v-6h6v6M9 10h.01M15 10h.01"/>',
        'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/>',
        'academy' => '<path d="m3 10 9-5 9 5-9 5z"/><path d="M7 12.5V17c3 2.3 7 2.3 10 0v-4.5M21 10v6"/>',
        'course' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><path d="M8 7h8M8 11h6"/>',
        'enquiry' => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.7 2.7 0 1 1 4.2 2.2c-1 .6-1.7 1.1-1.7 2.3M12 17h.01"/>',
        'enrolment' => '<path d="M16 4h2a2 2 0 0 1 2 2v15H4V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="5" rx="1"/><path d="M9 13h6M12 10v6"/>',
        'attendance' => '<rect x="3" y="4" width="18" height="17" rx="2"/><path d="M8 2v4M16 2v4M3 9h18M8 14l2 2 5-5"/>',
        'assessment' => '<path d="M9 3h6l1 3h3v15H5V6h3z"/><path d="M9 12h6M9 16h4"/>',
        'certificate' => '<circle cx="12" cy="9" r="6"/><path d="m8.5 14-1 8 4.5-2 4.5 2-1-8M9.5 9l1.5 1.5L14.5 7"/>',
        'store' => '<path d="M3 9l2-6h14l2 6"/><path d="M5 13v8h14v-8M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"/>',
        'inventory' => '<path d="m3 6 9-4 9 4-9 4z"/><path d="M3 6v12l9 4 9-4V6M12 10v12M7.5 8 16.5 4"/>',
        'order' => '<path d="M6 3h12l2 18H4z"/><path d="M9 7a3 3 0 0 0 6 0"/>',
        'delivery' => '<path d="M3 6h11v11H3zM14 10h4l3 3v4h-7z"/><circle cx="7" cy="19" r="2"/><circle cx="18" cy="19" r="2"/>',
        'payment' => '<rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="M2 10h20M6 15h4"/>',
        'refund' => '<path d="M3 10a9 9 0 1 1 2 7M3 10V4M3 10h6"/><path d="M15 8h-4.5a2 2 0 0 0 0 4h3a2 2 0 0 1 0 4H9M12 6v12"/>',
        'report' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
        'content' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h6"/>',
        'gallery' => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/>',
        'media' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m10 9 5 3-5 3z"/>',
        'review' => '<path d="m12 2 3 6 6.5 1-4.7 4.6 1.1 6.4-5.9-3.1L6.1 20l1.1-6.4L2.5 9 9 8z"/>',
        'megaphone' => '<path d="m3 11 15-6v14L3 13z"/><path d="M11 16v4a2 2 0 0 1-4 0v-5M21 9v6"/>',
        'loyalty' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8z"/>',
        'referral' => '<circle cx="8" cy="8" r="3"/><circle cx="18" cy="8" r="3"/><path d="M2 21v-2a6 6 0 0 1 12 0v2M14 14a6 6 0 0 1 8 5v2M14 5h2M15 4v2"/>',
        'notification' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>',
        'mail' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 7L2 7"/>',
        'mail-check' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 7L2 7M15 17l2 2 4-4"/>',
        'language' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18"/><path d="m7 8 3 8M6 13h5M15 8h3M16.5 8v8"/>',
        'sparkles' => '<path d="m12 3-1.2 3.8L7 8l3.8 1.2L12 13l1.2-3.8L17 8l-3.8-1.2zM5 14l-.8 2.2L2 17l2.2.8L5 20l.8-2.2L8 17l-2.2-.8zM19 14l-.8 2.2L16 17l2.2.8L19 20l.8-2.2L22 17l-2.2-.8z"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-4V21a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H2.8v-4H3a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3A1.7 1.7 0 0 0 10 3V2.8h4V3a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2v4H21a1.7 1.7 0 0 0-1.6 1z"/>',
        'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
        'item' => '<circle cx="12" cy="12" r="8"/><path d="M9 12h6"/>',
    ];
    $inner = $icons[$name] ?? $icons['item'];
@endphp

<svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    {!! $inner !!}
</svg>
