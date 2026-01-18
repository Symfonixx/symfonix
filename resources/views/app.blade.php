<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{app()->getLocale() == 'ar' ? 'rtl' :'ltr'}}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="index, follow"/>

    @php
        $seo = \Modules\Base\Models\Seo::pluck('value', 'key');
        $settings = \Modules\Base\Models\Settings::pluck('value', 'key');
    @endphp
    <meta name="author" content="{{ $seo->get('website_name') }}">
    <meta name="theme-color" content="#cc3333"/>

    <title inertia>{{ $page['props']['meta']['title'] ?? $seo->get('website_name') }}</title>
    <meta name="description" content="{{ $page['props']['meta']['description'] ?? $seo->get('website_desc') }}">

    <link rel="canonical" href="{{ $page['props']['meta']['canonical'] ?? url()->current() }}">

    <meta property="og:title" content="{{ $page['props']['meta']['og']['title'] ?? $seo->get('website_name') }}">
    <meta property="og:description"
          content="{{ $page['props']['meta']['og']['description'] ?? $seo->get('website_desc') }}">
    <meta property="og:image"
          content="{{ asset('storage/' . ($page['props']['meta']['og']['image'] ?? $settings->get('meta_img'))) }}">

    <meta name="twitter:title" content="{{ $page['props']['meta']['twitter']['title'] ?? $seo->get('website_name') }}">
    <meta name="twitter:description"
          content="{{ $page['props']['meta']['twitter']['description'] ?? $seo->get('website_desc') }}">
    <meta name="twitter:image"
          content="{{ asset('storage/' . ($page['props']['meta']['twitter']['image'] ?? $settings->get('meta_img'))) }}">

    <link rel="alternate" hreflang="en" href="{{ url('en') }}"/>
    <link rel="alternate" hreflang="ar" href="{{ url('ar') }}"/>
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}"/>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon/favicon-96x96.png') }}" sizes="96x96"/>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon/favicon.svg') }}"/>
    <link rel="shortcut icon" href="{{ asset('images/favicon/favicon.ico') }}"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
          rel="stylesheet">


    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          rel="stylesheet">

    @routes
    @inertiaHead

    <link rel="stylesheet" href="{{ asset('site/css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/animate.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/custom-animate.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/swiper.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/font-awesome-all.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/jarallax.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/jquery.magnific-popup.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/odometer.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/owl.carousel.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/owl.theme.default.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/nice-select.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/jquery-ui.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/aos.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/twentytwenty.css') }}"/>


    <link rel="stylesheet" href="{{ asset('site/css/module-css/banner.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/slider.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/footer.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/services.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/sliding-text.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/about.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/counter.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/portfolio.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/process.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/contact.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/testimonial.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/brand.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/newsletter.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/team.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/pricing.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/event.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/blog.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/why-choose.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/feature.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/faq.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/cta.css') }}"/>


    <!-- template styles -->
    <link rel="stylesheet" href="{{ asset('site/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/responsive.css') }}"/>


    @if(app()->getLocale() === "ar")

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('site/css/rtl.css')}}">
    @endif

    {!! $settings->get('header_scripts') !!}
</head>
<body class="custom-cursor">

<div class="custom-cursor__cursor"></div>
<div class="custom-cursor__cursor-two"></div>

<!--Start Preloader-->
<div class="loader js-preloader">
    <div></div>
    <div></div>
    <div></div>
</div>
<!--End Preloader-->


@inertia
<a href="#" data-target="html" class="scroll-to-target scroll-to-top">
    <span class="scroll-to-top__wrapper"><span class="scroll-to-top__inner"></span></span>
    <span class="scroll-to-top__text"> Go Back Top</span>
</a>


<script src="{{ asset('site/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('site/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('site/js/jarallax.min.js') }}"></script>
<script src="{{ asset('site/js/jquery.ajaxchimp.min.js') }}"></script>
<script src="{{ asset('site/js/jquery.appear.min.js') }}"></script>
<script src="{{ asset('site/js/swiper.min.js') }}"></script>
<script src="{{ asset('site/js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('site/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('site/js/odometer.min.js') }}"></script>
<script src="{{ asset('site/js/wNumb.min.js') }}"></script>
<script src="{{ asset('site/js/wow.js') }}"></script>
<script src="{{ asset('site/js/isotope.js') }}"></script>
<script src="{{ asset('site/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('site/js/jquery-ui.js') }}"></script>
<script src="{{ asset('site/js/jquery.nice-select.min.js') }}"></script>
<script src="{{ asset('site/js/twentytwenty.js') }}"></script>
<script src="{{ asset('site/js/jquery.event.move.js') }}"></script>
<script src="{{ asset('site/js/marquee.min.js') }}"></script>
<script src="{{ asset('site/js/jquery.circleType.js') }}"></script>
<script src="{{ asset('site/js/jquery.fittext.js') }}"></script>
<script src="{{ asset('site/js/jquery.lettering.min.js') }}"></script>
<script src="{{ asset('site/js/typed-2.0.11.js') }}"></script>
<script src="{{ asset('site/js/jquery-sidebar-content.js') }}"></script>
<script src="{{ asset('site/js/aos.js') }}"></script>
<script src="{{ asset('site/js/countdown.min.js') }}"></script>


<script src="{{ asset('site/js/gsap/gsap.js') }}"></script>
<script src="{{ asset('site/js/gsap/ScrollTrigger.js') }}"></script>
<script src="{{ asset('site/js/gsap/SplitText.js') }}"></script>


<!-- template js -->
<script src="{{ asset('site/js/script.js') }}"></script>
<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "{{env('APP_NAME')}}",
      "url": "{{env('APP_URL')}}",
      "logo": ""
    }
</script>
<script>
    @if (session('success'))
    toastr.success('{{ session('success') }}');
    @elseif (session('error'))
    toastr.error('{{ session('error') }}');
    @elseif(session('status'))
    toastr.info('{{ session('status') }}');
    @endif
    @if ($errors->any())
    @foreach ($errors->all() as $error)
    toastr.error('{{ $error }}');
    @endforeach
    @endif
</script>

{!! $settings->get('body_scripts') !!}
</body>
</html>
