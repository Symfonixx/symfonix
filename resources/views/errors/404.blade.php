@php
    $locale = app()->getLocale() ?: 'en';
    $homeUrl = url($locale === 'en' ? '/' : '/'.$locale);
    $isRtl = $locale === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#2189ca">
    <title>404 Error | {{ config('app.name', 'Symfonix') }}</title>
    <link rel="stylesheet" href="{{ asset('site/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/font-awesome-all.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/custom-animate.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/page-header.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/module-css/error.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/responsive.css') }}">
    @if ($isRtl)
        <link rel="stylesheet" href="{{ asset('site/css/rtl.css') }}">
    @endif
</head>
<body>
    <div class="page-wrapper" style="padding-top: 0;">
        <section class="page-header" style="padding-top: 140px;">
            <div
                class="page-header__bg"
                style="background-image: url({{ asset('images/contact-header-bg.jpg') }});"
            ></div>
            <div class="container">
                <div class="page-header__inner">
                    <h1>404 Error</h1>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li>
                                <a href="{{ $homeUrl }}">
                                    <i class="fas fa-home"></i>Home
                                </a>
                            </li>
                            <li>
                                <span class="icon-{{ $isRtl ? 'left' : 'right' }}-arrow-1"></span>
                            </li>
                            <li>404 Error</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="error-page">
            <div class="container">
                <div class="error-page__inner text-center">
                    <div class="error-page__img float-bob-y">
                        <img
                            src="{{ asset('site/images/resources/error-page-img1.png') }}"
                            alt="404 Error"
                            width="620"
                            height="420"
                            decoding="async"
                        >
                    </div>
                    <div class="error-page__content">
                        <h2>Oops! Page Not Found!</h2>
                        <p>The page you are looking for does not exist. It might have been moved or deleted.</p>
                        <div class="btn-box">
                            <a class="thm-btn" href="{{ $homeUrl }}">
                                Back To Home
                                <span class="icon-{{ $isRtl ? 'left' : 'right' }}-arrow"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
