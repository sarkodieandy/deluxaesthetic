<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? config('clinic.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/web/app.css', 'resources/js/web/app.js'])
</head>
<body class="auth-body antialiased">
    <a href="#auth-main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:bg-white focus:px-4 focus:py-2">{{ __('web.skip_to_content') }}</a>

    <header class="auth-top">
        <div class="container-site auth-top__inner">
            <a href="{{ route('web.home') }}" class="site-logo">
                <span class="site-logo__mark">{{ config('clinic.wordmark') }}</span>
                <span class="site-logo__sub">{{ config('clinic.logo_subtitle') }}</span>
            </a>
            <a href="{{ route('web.home') }}" class="auth-top__back">← {{ __('web.nav.home') }}</a>
        </div>
    </header>

    <main id="auth-main" class="auth-shell">
        <div class="auth-panel">
            {{ $slot }}
        </div>
    </main>

    <footer class="auth-footer">
        <p>&copy; {{ date('Y') }} {{ config('clinic.name') }}</p>
    </footer>
</body>
</html>
