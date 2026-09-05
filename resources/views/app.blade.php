<!---
Site: Symfonix
Created At: 01-01-2026
Developed By: Hadi Hilal
--->
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{app()->getLocale() == 'ar' ? 'rtl' :'ltr'}}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $robotsDirectives = $page['props']['meta']['robots'] ?? 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';
        $robotsLower = strtolower($robotsDirectives);
        if (! str_contains($robotsLower, 'noindex')) {
            if (! str_contains($robotsLower, 'max-snippet')) {
                $robotsDirectives .= ', max-snippet:-1';
            }
            if (! str_contains($robotsLower, 'max-image-preview')) {
                $robotsDirectives .= ', max-image-preview:large';
            }
            if (! str_contains($robotsLower, 'max-video-preview')) {
                $robotsDirectives .= ', max-video-preview:-1';
            }
        }
    @endphp
    <meta name="robots" content="{{ $robotsDirectives }}"/>
    <meta name="googlebot" content="{{ $robotsDirectives }}"/>

    @php
        $seo = \Modules\Base\Models\Seo::pluck('value', 'key');
        $settings = \Modules\Base\Models\Settings::pluck('value', 'key');
    @endphp
    <meta name="author" content="{{ $seo->get('website_name') }}">
    <meta name="theme-color" content="#2189ca"/>

    {{-- Early connections for third-party fonts (before any CSS/JS) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">

    @php
        $defaultMetaDescription = 'Empowering businesses with modern web, mobile, AI, and cloud solutions.';
        $pageMetaDescription = trim((string) ($page['props']['meta']['description'] ?? ''));
        $seoMetaDescription = trim((string) ($seo->get('website_desc') ?? ''));
        $resolvedMetaDescription = $pageMetaDescription !== ''
            ? $pageMetaDescription
            : ($seoMetaDescription !== '' ? $seoMetaDescription : $defaultMetaDescription);
        $resolvedMetaKeywords = $page['props']['meta']['keywords']
            ?? $seo->get('website_keywords')
            ?? 'IT solutions, web development, mobile apps, AI automation, cloud services';
    @endphp

    <title inertia>{{ $page['props']['meta']['title'] ?? $seo->get('website_name') }}</title>
    <meta name="description" content="{{ $resolvedMetaDescription }}">
    <meta name="keywords" content="{{ $resolvedMetaKeywords }}">

    @php
        $metaProps = $page['props']['meta'] ?? [];
        // Image values may be either a storage-relative path (e.g. "default.jpg")
        // or an already-absolute URL (e.g. a model's image_link accessor).
        $resolveImage = function ($value) {
            if (empty($value)) {
                return asset('images/blank.png');
            }
            if (\Illuminate\Support\Str::startsWith($value, ['http://', 'https://', '//'])) {
                return $value;
            }
            return asset('storage/' . ltrim($value, '/'));
        };
        $ogImagePath = $metaProps['og']['image'] ?? $settings->get('meta_img');
        $twitterImagePath = $metaProps['twitter']['image'] ?? $settings->get('meta_img');
        $ogImageUrl = $resolveImage($ogImagePath);
        $twitterImageUrl = $resolveImage($twitterImagePath);
        $ogTitle = $metaProps['og']['title'] ?? $seo->get('website_name');
        $ogDescriptionRaw = trim((string) ($metaProps['og']['description'] ?? $seo->get('website_desc') ?? ''));
        $ogDescription = $ogDescriptionRaw !== '' ? $ogDescriptionRaw : $resolvedMetaDescription;
        $canonicalUrl = $metaProps['canonical'] ?? url()->current();
        $ogType = $metaProps['og']['type'] ?? 'website';
        $twitterHandle = $settings->get('twitter')
            ? '@'.ltrim(\Illuminate\Support\Str::of($settings->get('twitter'))->afterLast('/')->trim(), '@')
            : null;
        $twitterDescriptionRaw = trim((string) ($metaProps['twitter']['description'] ?? $seo->get('website_desc') ?? ''));
        $twitterDescription = $twitterDescriptionRaw !== '' ? $twitterDescriptionRaw : $resolvedMetaDescription;
    @endphp

    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="alternate" type="application/rss+xml" title="RSS" href="{{ url('/rss.xml') }}">
    <link rel="alternate" type="text/plain" title="LLM context" href="{{ url('/llms.txt') }}">
    <link rel="author" type="text/plain" href="{{ url('/humans.txt') }}">
    <link rel="manifest" href="{{ asset('images/favicon/site.webmanifest') }}">

    @php
        use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
        $supportedLocalesMeta = LaravelLocalization::getSupportedLocales();
        $currentLocaleCode = app()->getLocale();
        $currentOgLocale = str_replace('-', '_', $supportedLocalesMeta[$currentLocaleCode]['regional'] ?? $currentLocaleCode);
        $currentUrl = url()->current();
        $supportedLocales = array_keys($supportedLocalesMeta);
        $defaultUrl = LaravelLocalization::getLocalizedURL(LaravelLocalization::getDefaultLocale(), $currentUrl);
    @endphp

    {{-- Open Graph --}}
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ $seo->get('website_name') }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:locale" content="{{ $currentOgLocale }}">
    @foreach($supportedLocalesMeta as $localeCode => $localeMeta)
        @if($localeCode !== $currentLocaleCode)
            <meta property="og:locale:alternate" content="{{ str_replace('-', '_', $localeMeta['regional'] ?? $localeCode) }}">
        @endif
    @endforeach
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImageUrl }}">
    <meta property="og:image:secure_url" content="{{ $ogImageUrl }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $ogTitle }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    @if($twitterHandle)
        <meta name="twitter:site" content="{{ $twitterHandle }}">
    @endif
    <meta name="twitter:url" content="{{ $canonicalUrl }}">
    <meta name="twitter:title" content="{{ $metaProps['twitter']['title'] ?? $seo->get('website_name') }}">
    <meta name="twitter:description" content="{{ $twitterDescription }}">
    <meta name="twitter:image" content="{{ $twitterImageUrl }}">
    <meta name="twitter:image:alt" content="{{ $metaProps['twitter']['title'] ?? $seo->get('website_name') }}">

    @foreach($supportedLocales as $hreflang)
        <link rel="alternate" hreflang="{{ $hreflang }}" href="{{ LaravelLocalization::getLocalizedURL($hreflang, $currentUrl) }}"/>
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $defaultUrl }}"/>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon/favicon-96x96.png') }}" sizes="96x96"/>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon/favicon.svg') }}"/>
    <link rel="shortcut icon" href="{{ asset('images/favicon/favicon.ico') }}"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}"/>

    @if(($page['component'] ?? null) === 'Base::Index')
        <link rel="preload" as="image" href="{{ asset('images/home/banner-bg.webp') }}" type="image/webp" fetchpriority="high">
    @endif

    {{-- Ziggy routes are shared via Inertia props and installed in app.js — do not dump them here. --}}

    {{-- jQuery before Vite so header menu bindings always have $ available --}}
    <script src="{{ asset('site/js/jquery-3.6.0.min.js') }}"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Slim font request: common weights only (full variable italic axis is huge) --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
          rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    </noscript>

    @inertiaHead

    {{-- FAQ Schema (server-side for SEO) --}}
    @if(isset($page['props']['faqSchema']) && !empty($page['props']['faqSchema']))
        <script type="application/ld+json">
            {!! json_encode($page['props']['faqSchema'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    @endif

    {{-- Page structured data: Article / Service / Product / BreadcrumbList (server-side for SEO) --}}
    @if(!empty($page['props']['structuredData']))
        @foreach($page['props']['structuredData'] as $schema)
            @if(!empty($schema))
                <script type="application/ld+json">
                    {!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
                </script>
            @endif
        @endforeach
    @endif

    {{-- Critical above-the-fold CSS (sync) --}}
    <link rel="stylesheet" href="{{ asset('site/css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/font-awesome-all.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/banner.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/footer.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/responsive.css') }}"/>

    {{-- Non-critical CSS: deferred until after first paint --}}
    <link rel="stylesheet" href="{{ asset('site/css/animate.min.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/custom-animate.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/owl.carousel.min.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/owl.theme.default.min.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/slider.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/services.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/sliding-text.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/about.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/process.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/contact.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/testimonial.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/newsletter.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/team.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/blog.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/why-choose.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/feature.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/cta.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/page-header.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/clients.css') }}" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('site/css/animate.min.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/custom-animate.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/owl.carousel.min.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/owl.theme.default.min.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/module-css/slider.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/module-css/services.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/module-css/sliding-text.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/module-css/about.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/module-css/process.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/module-css/contact.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/module-css/testimonial.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/module-css/newsletter.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/module-css/team.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/module-css/blog.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/module-css/why-choose.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/module-css/feature.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/module-css/cta.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/module-css/page-header.css') }}"/>
        <link rel="stylesheet" href="{{ asset('site/css/module-css/clients.css') }}"/>
    </noscript>
    <style>
        #symfonixbot-launcher-wrap {
            position: fixed;
            bottom: 60px;
            right: 25px;
            z-index: 98;
            display: flex;
            align-items: center;
            gap: 10px;
            direction: ltr;
        }

        #symfonixbot-launcher {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background-color: #7fc457;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            transition: transform 180ms ease, box-shadow 180ms ease, filter 180ms ease;
            animation: symfonixbot-launcher-pop 650ms cubic-bezier(.2, .9, .2, 1) 200ms both;
            order: 1;
        }

        #symfonixbot-launcher img {
            width: 32px;
            height: 32px;
        }

        #symfonixbot-launcher-hint {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: #e9f7dd;
            color: #7fc457;
            border: 1px solid rgba(127, 196, 87, 0.28);
            font-weight: 700;
            font-size: 12px;
            line-height: 1;
            text-transform: uppercase;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.16);
            user-select: none;
            white-space: nowrap;
            transition: transform 320ms ease, box-shadow 320ms ease, opacity 320ms ease;
            opacity: 0;
            transform: translateX(6px) scale(0.98);
            pointer-events: none;
            order: 0;
        }

        html[dir="rtl"] #symfonixbot-launcher-hint {
            direction: rtl;
        }

        #symfonixbot-launcher-hint::after {
            content: "";
            position: absolute;
            right: -6px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
            border-left: 6px solid #e9f7dd;
        }

        #symfonixbot-launcher-hint.symfonixbot-hidden {
            opacity: 0;
            transform: translateX(6px) scale(0.98);
            pointer-events: none;
        }

        #symfonixbot-launcher:hover {
            transform: translateY(-1px) scale(1.02);
            box-shadow: 0 10px 26px rgba(0, 0, 0, 0.28);
        }

        #symfonixbot-launcher:hover + #symfonixbot-launcher-hint:not(.symfonixbot-hidden) {
            opacity: 1;
            transform: translateX(0) scale(1);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.18);
            animation: symfonixbot-hint-pop 1000ms cubic-bezier(.2, .9, .2, 1) both,
            symfonixbot-hint-float 3.2s ease-in-out 1200ms infinite;
        }

        @keyframes symfonixbot-hint-pop {
            0% {
                opacity: 0;
                transform: translateX(10px) scale(0.92);
            }
            60% {
                opacity: 1;
                transform: translateX(0) scale(1.04);
            }
            100% {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes symfonixbot-launcher-pop {
            0% {
                transform: scale(0.92);
                filter: saturate(0.9);
            }
            60% {
                transform: scale(1.06);
                filter: saturate(1.05);
            }
            100% {
                transform: scale(1);
                filter: saturate(1);
            }
        }

        @keyframes symfonixbot-hint-float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-2px);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            #symfonixbot-launcher,
            #symfonixbot-launcher-hint {
                animation: none !important;
                transition: none !important;
            }
        }

        @media (max-width: 575.98px) {
            #symfonixbot-launcher-wrap {
                bottom: 14px;
                right: 14px;
                gap: 8px;
            }

            #symfonixbot-launcher {
                width: 52px;
                height: 52px;
            }

            #symfonixbot-launcher img {
                width: 30px;
                height: 30px;
            }

            #symfonixbot-launcher-hint {
                padding: 7px 10px;
                font-size: 11px;
            }
        }

        #symfonixbot-container {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 360px;
            max-width: 90vw;
            height: 450px;
            max-height: 80vh;
            background: #f9f9f9;
            border-radius: 20px;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 9999;
            font-family: 'Roboto', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 14px;
            transition: all .25s ease;
        }

        /* Make it breathe better on small screens */
        @media (max-width: 600px) {
            #symfonixbot-container {
                bottom: 20px;
                right: 10px;
                width: 95%;
                height: 70vh;
                max-width: none;
                border-radius: 14px;
            }
        }

        #symfonixbot-container.symfonixbot-hidden {
            display: none;
        }

        .symfonixbot-header {
            background: #7fc457;
            color: #ffffff;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;

        }

        .symfonixbot-header-title {
            font-weight: 600;
        }

        .symfonixbot-header-close {
            cursor: pointer;
              font-size: 24px;
        }

        .symfonixbot-bubble-bot a {
            color: #7fc457;
        }

        .symfonixbot-messages {
            padding: 10px 12px;
            overflow-y: auto;
            flex: 1;
            background: #f5f7fa;
        }

        .symfonixbot-message {
            margin-bottom: 8px;
            display: flex;
        }

        .symfonixbot-message-user {
            justify-content: flex-end;
        }

        .symfonixbot-bubble {
            max-width: 80%;
            padding: 8px 10px;
            border-radius: 12px;
            line-height: 1.4;
        }

        .symfonixbot-bubble-user {
            background: #7fc457;
            color: #ffffff;
            border-bottom-right-radius: 2px;
            margin: 10px 2px;
        }

        .symfonixbot-bubble-bot {
            background: #ffffff;
            color: #111827;
            border-bottom-left-radius: 2px;
        }

        .symfonixbot-actions {
            margin-top: 8px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .symfonixbot-action-btn {
            display: block;
            width: 100%;
            text-align: center;
            border-radius: 999px;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            background: #7fc457;
            color: #ffffff;
        }

        .symfonixbot-input-row {
            display: flex;
            align-items: center;
            padding: 8px 10px;
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
        }

        #symfonixbot-input {
            flex: 1;
            border: none;
            outline: none;
            padding: 6px 8px;
            background: transparent;
        }

        #symfonixbot-send {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: #7fc457;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1rem;
        }

        .content > *, .blog-details__text > *, .services-details__text-1 > * {
            line-height: 2.5rem !important;
        }

        /* Improve tap targets flagged by Lighthouse */
        .site-footer-two__social-box-inner a,
        .mobile-nav__social a,
        .scroll-to-top {
            min-width: 44px;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>


    @if(app()->getLocale() === "ar")
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap"
              rel="stylesheet" media="print" onload="this.media='all'">
        <noscript>
            <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
        </noscript>
        <link rel="stylesheet" href="{{ asset('site/css/rtl.css') }}" media="print" onload="this.media='all'">
        <style>
            #symfonixbot-container {
                font-family: 'Cairo', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
            }
        </style>
    @endif
    {!! $settings->get('header_scripts') !!}
</head>
<body class="custom-cursor">
<style>
    /* Hide crawl fallback when Inertia SSR already filled #app */
    #main-content:has(#app:not(:empty)) ~ #geo-crawl-fallback {
        display: none !important;
    }
    #geo-crawl-fallback {
        max-width: 960px;
        margin: 0 auto;
        padding: 1.5rem;
        font-family: system-ui, sans-serif;
        line-height: 1.6;
    }
    #geo-crawl-fallback.is-hydrated {
        display: none !important;
    }
    #geo-crawl-fallback nav ul { list-style: none; padding: 0; display: flex; flex-wrap: wrap; gap: 0.75rem 1.25rem; }
    #geo-crawl-fallback a { color: #2189ca; }

    /* Show desktop nav earlier — theme default only kicks in at 1200px */
    @media (min-width: 992px) {
        .main-menu .main-menu__list,
        .stricky-header .main-menu__list {
            display: flex !important;
        }
        .main-menu .mobile-nav__toggler {
            display: none !important;
        }
    }
</style>

<div class="custom-cursor__cursor"></div>
<div class="custom-cursor__cursor-two"></div>


<main id="main-content">
    @inertia
</main>

{{-- Static crawlable content for bots/auditors that do not execute JS (Inertia is client-rendered). --}}
@if(($page['component'] ?? null) === 'Base::Index')
<div id="geo-crawl-fallback">
    <nav aria-label="Primary">
        <ul>
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li><a href="{{ route('about-us') }}">{{ __('About Us') }}</a></li>
            <li><a href="{{ route('services.index') }}">{{ __('Our Services') }}</a></li>
            <li><a href="{{ route('product.index') }}">{{ __('Products') }}</a></li>
            <li><a href="{{ route('use-cases.index') }}">{{ __('Case Studies') }}</a></li>
            <li><a href="{{ route('blogs.index') }}">{{ __('Blogs') }}</a></li>
            <li><a href="{{ route('contact-us') }}">{{ __('Contact Us') }}</a></li>
        </ul>
    </nav>

    <h1>{{ __('Transform complex technical ideas into intelligent systems') }}</h1>
    <p>{{ __('Help companies build practical technology solutions in Web, AI, automation, and cloud computing — designed for growth and sustainability') }}</p>

    <h2>{{ __('What We Do') }} — {{ __('Core Services') }}</h2>
    <p>{{ __("Transform your business with our innovative IT solutions, tailored to address your unique challenges and drive growth in today's digital landscape.") }}</p>
    <ul>
        <li><a href="{{ route('services.index') }}">{{ __('Our Services') }}</a></li>
        <li><a href="{{ route('product.index') }}">{{ __('Products') }}</a></li>
        <li><a href="{{ route('about-us') }}">{{ __('About Us') }}</a></li>
    </ul>

    <h2>{{ __('How We\'ve Empowered Businesses with Innovative Tech Solutions') }}</h2>
    <p>{{ __('Explore our success stories and real-world solutions we\'ve delivered for businesses.') }}</p>
    <p><a href="{{ route('use-cases.index') }}">{{ __('Case Studies') }}</a>
        · <a href="{{ route('blogs.index') }}">{{ __('Blogs') }}</a>
        · <a href="{{ route('contact-us') }}">{{ __('Book your free consultation') }}</a></p>

    <h2>{{ __('Why Choose Symfonix for Web, AI, and Cloud') }}</h2>
    <p>{{ $seo->get('website_desc') ?: __('Empowering businesses with modern web, mobile, AI, and cloud solutions.') }}</p>
</div>
@endif

<noscript>
    <p>{{ __('Enable JavaScript for the full interactive experience. Key pages are linked above.') }}</p>
</noscript>

<div id="symfonixbot-launcher-wrap" aria-label="Symfonix Bot launcher">
    <div id="symfonixbot-launcher" aria-label="{{ __('chat.launcher.open') }}">
        <img src="{{ asset('images/robot.png') }}" alt="{{ __('chat.launcher.title') }}" width="40" height="40" loading="lazy" decoding="async">
    </div>
    <div id="symfonixbot-launcher-hint" class="fade-in">{{ __('chat.launcher.ask_me') }}</div>
</div>
<div id="symfonixbot-container" class="symfonixbot-hidden" aria-label="{{ __('chat.launcher.title') }}">
    <div class="symfonixbot-header">
        <div class="symfonixbot-header-title">{{ __('chat.launcher.title') }}</div>
        <div class="symfonixbot-header-close" id="symfonixbot-close">×</div>
    </div>
    <div id="symfonixbot-messages" class="symfonixbot-messages"></div>
    <div class="symfonixbot-input-row">
        <input id="symfonixbot-input" type="text" placeholder="{{ __('chat.launcher.placeholder') }}">
        <button id="symfonixbot-send">➤</button>
    </div>
</div>

<a href="#main-content" data-target="html" class="scroll-to-target scroll-to-top d-none d-lg-flex" aria-label="{{ __('Scroll back to top of page') }}">
    <span class="scroll-to-top__wrapper"><span class="scroll-to-top__inner"></span></span>
    <span class="scroll-to-top__text"> {{__('Go Back Top')}}</span>
</a>

<script src="{{ asset('site/js/bootstrap.bundle.min.js') }}" defer></script>
<script src="{{ asset('site/js/jquery.appear.min.js') }}" defer></script>
<script src="{{ asset('site/js/wow.js') }}" defer></script>
<script src="{{ asset('site/js/owl.carousel.min.js') }}" defer></script>
<script src="{{ asset('site/js/marquee.min.js') }}" defer></script>
<script src="{{ asset('site/js/gsap/gsap.js') }}" defer></script>
<script src="{{ asset('site/js/gsap/ScrollTrigger.js') }}" defer></script>
<script src="{{ asset('site/js/gsap/SplitText.js') }}" defer></script>
<script>window.__symfonixMobileNavBound = true;</script>
<script src="{{ asset('site/js/script.js') }}" defer></script>
<script>
    (function () {
        const endpoint = '{{ route('botman.handle') }}?locale={{ app()->getLocale() }}';
        const symfonixbotSessionId = 'user-' + Math.random().toString(36).substring(2) + Date.now().toString(36);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const launcher = document.getElementById('symfonixbot-launcher');
        const container = document.getElementById('symfonixbot-container');
        const closeBtn = document.getElementById('symfonixbot-close');
        const messagesEl = document.getElementById('symfonixbot-messages');
        const inputEl = document.getElementById('symfonixbot-input');
        const sendBtn = document.getElementById('symfonixbot-send');
        const hintEl = document.getElementById('symfonixbot-launcher-hint');

        if (!launcher || !container) {
            return;
        }

        function toggleChat(open) {
            const shouldOpen = open !== undefined ? open : container.classList.contains('symfonixbot-hidden');
            container.classList.toggle('symfonixbot-hidden', !shouldOpen);
            if (shouldOpen && hintEl) {
                hintEl.classList.add('symfonixbot-hidden');
            }
            if (shouldOpen) {
                inputEl.focus();
            }
        }

        function appendMessage(from, html) {
            const wrapMsg = document.createElement('div');
            wrapMsg.className = 'symfonixbot-message ' + (from === 'user' ? 'symfonixbot-message-user' : '');
            const bubble = document.createElement('div');
            bubble.className = 'symfonixbot-bubble ' + (from === 'user' ? 'symfonixbot-bubble-user' : 'symfonixbot-bubble-bot');
            bubble.innerHTML = html;
            wrapMsg.appendChild(bubble);
            messagesEl.appendChild(wrapMsg);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function appendActions(actions) {
            if (!actions || !actions.length) return;
            const row = document.createElement('div');
            row.className = 'symfonixbot-actions';
            actions.forEach(action => {
                const btn = document.createElement('button');
                btn.className = 'symfonixbot-action-btn';
                btn.textContent = action.text || action.name || '';
                btn.addEventListener('click', function () {
                    sendMessage(action.text || '', {interactive: true, value: action.value});
                });
                row.appendChild(btn);
            });
            messagesEl.appendChild(row);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        async function sendMessage(text, options = {}) {
            const trimmed = text.trim();
            if (!trimmed) return;

            appendMessage('user', trimmed);
            inputEl.value = '';

            const payload = {
                driver: 'web',
                userId: symfonixbotSessionId,
                message: trimmed,
                _token: csrfToken,
            };

            if (options.interactive) {
                payload.interactive = true;
                if (options.value) {
                    payload.value = options.value;
                }
            }

            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                    body: new URLSearchParams(payload),
                });

                const raw = await res.text();

                if (!res.ok) {
                    console.error('Symfonix Bot request failed', res.status, raw);
                    appendMessage('bot', '{{ __('chat.lead.error') }}');
                    return;
                }

                let data;
                try {
                    data = JSON.parse(raw);
                } catch (e) {
                    const linkIdx = raw.indexOf('<link');
                    const scriptIdx = raw.indexOf('<script');
                    let cutIdx = -1;
                    if (linkIdx > 0 && scriptIdx > 0) {
                        cutIdx = Math.min(linkIdx, scriptIdx);
                    } else if (linkIdx > 0) {
                        cutIdx = linkIdx;
                    } else if (scriptIdx > 0) {
                        cutIdx = scriptIdx;
                    }

                    if (cutIdx > 0) {
                        const jsonPart = raw.substring(0, cutIdx);
                        data = JSON.parse(jsonPart);
                    } else {
                        console.error('Symfonix Bot raw response (no JSON)', raw);
                        return;
                    }
                }

                const replies = (data && data.messages) || [];

                replies.forEach(msg => {
                    if (!msg || typeof msg !== 'object') return;
                    if (msg.type === 'typing_indicator') {
                        return;
                    }
                    if (msg.type === 'actions') {
                        if (msg.text) {
                            appendMessage('bot', msg.text);
                        }
                        appendActions(msg.actions || []);
                    } else {
                        if (msg.text) {
                            appendMessage('bot', msg.text);
                        }
                    }
                });
            } catch (e) {
                console.error('Symfonix Bot error', e);
            }
        }

        launcher.addEventListener('click', function () {
            toggleChat(true);
        });
        closeBtn.addEventListener('click', function () {
            toggleChat(false);
        });
        sendBtn.addEventListener('click', function () {
            sendMessage(inputEl.value);
        });
        inputEl.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage(inputEl.value);
            }
        });
    })();
</script>

@php
    $orgName = $seo->get('website_name') ?: config('app.name');
    $siteUrl = rtrim(config('app.url') ?: url('/'), '/');
    $logoPath = $settings->get('site_logo');
    $logoUrl = $logoPath ? asset('storage/' . $logoPath) : null;

    $sameAs = array_values(array_filter([
        $settings->get('facebook'),
        $settings->get('instagram'),
        $settings->get('twitter'),
        $settings->get('linkedin'),
        $settings->get('github'),
    ], fn ($v) => is_string($v) && str_starts_with($v, 'http')));

    $knowsAbout = [
        'Web Development',
        'Mobile Applications',
        'Artificial Intelligence',
        'Business Automation',
        'Cloud Computing',
        'IT Consulting',
        'Software Engineering',
        'Digital Transformation',
    ];

    $organization = array_filter([
        '@type' => 'Organization',
        '@id' => $siteUrl . '/#organization',
        'name' => $orgName,
        'url' => $siteUrl,
        'logo' => $logoUrl ? [
            '@type' => 'ImageObject',
            'url' => $logoUrl,
        ] : null,
        'image' => $logoUrl,
        'description' => $seo->get('website_desc'),
        'email' => $settings->get('email') ?: null,
        'telephone' => $settings->get('phone') ?: null,
        'knowsAbout' => $knowsAbout,
        'address' => $settings->get('address') ? [
            '@type' => 'PostalAddress',
            'streetAddress' => $settings->get('address'),
        ] : null,
        'contactPoint' => ($settings->get('phone') || $settings->get('email')) ? array_filter([
            '@type' => 'ContactPoint',
            'contactType' => 'customer support',
            'telephone' => $settings->get('phone') ?: null,
            'email' => $settings->get('email') ?: null,
            'availableLanguage' => array_keys(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales()),
        ]) : null,
        'sameAs' => ! empty($sameAs) ? $sameAs : null,
        'makesOffer' => [
            [
                '@type' => 'Offer',
                'itemOffered' => [
                    '@type' => 'Service',
                    'name' => 'Web Development',
                    'url' => \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(app()->getLocale(), url('/services')),
                ],
            ],
            [
                '@type' => 'Offer',
                'itemOffered' => [
                    '@type' => 'Service',
                    'name' => 'AI & Automation',
                    'url' => \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(app()->getLocale(), url('/services')),
                ],
            ],
            [
                '@type' => 'Offer',
                'itemOffered' => [
                    '@type' => 'Service',
                    'name' => 'Cloud Solutions',
                    'url' => \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(app()->getLocale(), url('/services')),
                ],
            ],
        ],
    ], fn ($v) => ! is_null($v) && $v !== '' && $v !== []);

    $professionalService = array_filter([
        '@type' => 'ProfessionalService',
        '@id' => $siteUrl . '/#professionalservice',
        'name' => $orgName,
        'url' => $siteUrl,
        'image' => $logoUrl,
        'description' => $seo->get('website_desc'),
        'telephone' => $settings->get('phone') ?: null,
        'email' => $settings->get('email') ?: null,
        'priceRange' => $settings->get('price_range') ?: null,
        'address' => $settings->get('address') ? [
            '@type' => 'PostalAddress',
            'streetAddress' => $settings->get('address'),
        ] : null,
        'areaServed' => 'Worldwide',
        'knowsAbout' => $knowsAbout,
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name' => 'IT Services',
            'itemListElement' => [
                [
                    '@type' => 'OfferCatalog',
                    'name' => 'Web Development',
                ],
                [
                    '@type' => 'OfferCatalog',
                    'name' => 'Mobile Applications',
                ],
                [
                    '@type' => 'OfferCatalog',
                    'name' => 'AI Automation',
                ],
                [
                    '@type' => 'OfferCatalog',
                    'name' => 'Cloud Computing',
                ],
            ],
        ],
        'parentOrganization' => ['@id' => $siteUrl . '/#organization'],
        'sameAs' => ! empty($sameAs) ? $sameAs : null,
    ], fn ($v) => ! is_null($v) && $v !== '' && $v !== []);

    $website = array_filter([
        '@type' => 'WebSite',
        '@id' => $siteUrl . '/#website',
        'name' => $orgName,
        'url' => $siteUrl,
        'description' => $seo->get('website_desc'),
        'inLanguage' => array_keys(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales()),
        'publisher' => ['@id' => $siteUrl . '/#organization'],
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => rtrim(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL(app()->getLocale(), url('/blogs')), '/').'?search={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ], fn ($v) => ! is_null($v) && $v !== '');

    $schemaGraph = [
        '@context' => 'https://schema.org',
        '@graph' => [$organization, $professionalService, $website],
    ];
@endphp
<script type="application/ld+json">
    {!! json_encode($schemaGraph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
<script>
    @if (session('success'))
    toastr.success('{{ session('success') }}');
    @elseif (session('error'))
    toastr.error('{{ session('error') }}');
    @elseif(session('status'))
    toastr.info('{{ session('status') }}');
    @endif
    @if (isset($errors) && $errors->any())
    @foreach ($errors->all() as $error)
    toastr.error('{{ $error }}');
    @endforeach
    @endif
</script>

{!! $settings->get('body_scripts') !!}
</body>
</html>
