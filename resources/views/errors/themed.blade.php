@php
    $locale = app()->getLocale() ?: 'en';
    $homeUrl = url('/'.$locale);
    $isRtl = $locale === 'ar';
    $status = $status ?? 500;
    $title = $title ?? 'Error';
    $heading = $heading ?? 'Something went wrong';
    $message = $message ?? 'Please try again later.';
    $showImage = $showImage ?? false;
    $loginUrl = null;
    try {
        $loginUrl = route('login');
    } catch (\Throwable $e) {
        $loginUrl = url('/'.$locale.'/login');
    }
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#2189ca">
    <title>{{ $title }} | {{ config('app.name', 'Symfonix') }}</title>
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
                    <h1>{{ $title }}</h1>
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
                            <li>{{ $title }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="error-page">
            <div class="container">
                <div class="error-page__inner text-center">
                    @if ($showImage)
                        <div class="error-page__img float-bob-y">
                            <img
                                src="{{ asset('site/images/resources/error-page-img1.png') }}"
                                alt="{{ $title }}"
                                width="620"
                                height="420"
                                decoding="async"
                            >
                        </div>
                    @else
                        <div class="error-page__code float-bob-y" aria-hidden="true">{{ $status }}</div>
                    @endif
                    <div class="error-page__content">
                        <h2>{{ $heading }}</h2>
                        <p>{{ $message }}</p>
                        <div class="btn-box">
                            @if (! empty($showLogin))
                                <a class="thm-btn error-page__btn-secondary" href="{{ $loginUrl }}">
                                    Login
                                    <span class="icon-{{ $isRtl ? 'left' : 'right' }}-arrow"></span>
                                </a>
                            @endif
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
