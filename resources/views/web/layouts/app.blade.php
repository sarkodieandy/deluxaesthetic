<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('clinic.name'))</title>
    <meta name="description" content="@yield('meta_description', 'Premium aesthetic clinic, spa, training academy, and beauty store in Ghana.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/web/app.css', 'resources/js/web/app.js'])
</head>
<body class="antialiased" x-data="mobileNav()" @keydown.window="onKey($event)">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:bg-white focus:px-4 focus:py-2">{{ __('web.skip_to_content') }}</a>

    <div class="announcement-bar">
        {{ $announcement ?? __('web.announcement') }}
    </div>

    @include('web.components.header')

    <main id="main-content">
        @yield('content')
    </main>

    @include('web.components.footer')

    @include('web.components.mobile-menu')

    @php($whatsapp = config('clinic.whatsapp'))
    @php($whatsappDigits = $whatsapp ? preg_replace('/\D+/', '', $whatsapp) : '')
    @if($whatsappDigits)
        <a
            class="whatsapp-float"
            href="https://wa.me/{{ $whatsappDigits }}"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="{{ __('web.whatsapp') }}"
        >
            @include('web.components.whatsapp-icon', ['class' => 'whatsapp-float__icon'])
            <span class="whatsapp-float__label">{{ __('web.whatsapp') }}</span>
        </a>
    @endif
</body>
</html>
