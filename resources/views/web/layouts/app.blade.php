<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $seoTitle = html_entity_decode(
            trim($cmsPage?->seo_title ?: $__env->yieldContent('title', config('seo.default_title'))),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );
        $seoDescription = \Illuminate\Support\Str::limit(
            trim(strip_tags($cmsPage?->meta_description ?: $__env->yieldContent('meta_description', config('seo.default_description')))),
            160,
            ''
        );
        $canonical = trim($__env->yieldContent('canonical'))
            ?: request()->fullUrlWithoutQuery(['q', 'sort', 'category', 'in_stock']);
        $seoImage = trim($__env->yieldContent('meta_image'))
            ?: asset(config('seo.default_image'));
        $hasFilterQuery = request()->hasAny(['q', 'sort', 'category', 'in_stock']);
        $privatePublicPage = request()->routeIs(
            'web.cart.*',
            'web.checkout.*',
            'web.payments.*',
            'web.booking.confirmation'
        );
        $robots = trim($__env->yieldContent('robots'))
            ?: (($hasFilterQuery || $privatePublicPage)
                ? 'noindex, follow'
                : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1');
        $businessSchema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => ['MedicalBusiness', 'DaySpa'],
                    '@id' => url('/').'#business',
                    'name' => config('clinic.name'),
                    'legalName' => config('clinic.legal_name'),
                    'url' => url('/'),
                    'image' => $seoImage,
                    'telephone' => config('clinic.phone'),
                    'email' => config('clinic.email'),
                    'priceRange' => 'GHS',
                    'currenciesAccepted' => config('clinic.currency'),
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => 'Dr Tagoe Avenue, GA-375-8490',
                        'addressLocality' => 'East Legon, Accra',
                        'addressCountry' => 'GH',
                    ],
                    'openingHoursSpecification' => [[
                        '@type' => 'OpeningHoursSpecification',
                        'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                        'opens' => '07:00',
                        'closes' => '19:00',
                    ]],
                    'sameAs' => config('seo.social_urls'),
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => url('/').'#website',
                    'url' => url('/'),
                    'name' => config('clinic.name'),
                    'publisher' => ['@id' => url('/').'#business'],
                    'inLanguage' => str_replace('_', '-', app()->getLocale()),
                ],
                [
                    '@type' => 'WebPage',
                    '@id' => $canonical.'#webpage',
                    'url' => $canonical,
                    'name' => $seoTitle,
                    'description' => $seoDescription,
                    'isPartOf' => ['@id' => url('/').'#website'],
                    'about' => ['@id' => url('/').'#business'],
                    'inLanguage' => str_replace('_', '-', app()->getLocale()),
                ],
            ],
        ];
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="{{ $robots }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ config('clinic.name') }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:image:alt" content="@yield('meta_image_alt', config('clinic.name'))">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    @if(config('seo.twitter_handle'))
        <meta name="twitter:site" content="{{ config('seo.twitter_handle') }}">
    @endif
    @if(config('seo.google_site_verification'))
        <meta name="google-site-verification" content="{{ config('seo.google_site_verification') }}">
    @endif
    <script type="application/ld+json">{!! json_encode($businessSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @stack('structured_data')
    @stack('preload')
    @vite(['resources/css/web/app.css', 'resources/js/web/app.js'])
</head>
<body class="antialiased" x-data="mobileNav()" @keydown.window="onKey($event)">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:bg-white focus:px-4 focus:py-2">{{ __('web.skip_to_content') }}</a>

    <div class="announcement-bar">
        {{ $announcement ?? __('web.announcement') }}
    </div>

    @if(($livePromotions ?? collect())->isNotEmpty())
        @include('web.components.promotion-banners', ['promotions' => $livePromotions])
    @endif

    @include('web.components.header')

    <main id="main-content">
        @if($cmsPreview ?? false)
            <div class="cms-preview-bar">Draft preview · <a href="{{ route('admin.pages.edit', $cmsPage) }}">Return to editor</a></div>
        @endif
        @yield('content')
        @if($cmsPage && ($cmsPage->is_published || ($cmsPreview ?? false)))
            @include('web.components.cms-sections', ['sections' => $cmsPage->sections ?? []])
        @endif
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
