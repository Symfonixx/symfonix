<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() == 'ar' ? 'rtl' :'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="robots" content="index, follow"/>

    <?php
        $seo = \Modules\Base\Models\Seo::pluck('value', 'key');
        $settings = \Modules\Base\Models\Settings::pluck('value', 'key');
    ?>
    <meta name="author" content="<?php echo e($seo->get('website_name')); ?>">
    <meta name="theme-color" content="#cc3333"/>

    <title inertia><?php echo e($page['props']['meta']['title'] ?? $seo->get('website_name')); ?></title>
    <meta name="description" content="<?php echo e($page['props']['meta']['description'] ?? $seo->get('website_desc')); ?>">

    <link rel="canonical" href="<?php echo e($page['props']['meta']['canonical'] ?? url()->current()); ?>">

    <meta property="og:title" content="<?php echo e($page['props']['meta']['og']['title'] ?? $seo->get('website_name')); ?>">
    <meta property="og:description"
          content="<?php echo e($page['props']['meta']['og']['description'] ?? $seo->get('website_desc')); ?>">
    <meta property="og:image"
          content="<?php echo e(asset('storage/' . ($page['props']['meta']['og']['image'] ?? $settings->get('meta_img')))); ?>">

    <meta name="twitter:title" content="<?php echo e($page['props']['meta']['twitter']['title'] ?? $seo->get('website_name')); ?>">
    <meta name="twitter:description"
          content="<?php echo e($page['props']['meta']['twitter']['description'] ?? $seo->get('website_desc')); ?>">
    <meta name="twitter:image"
          content="<?php echo e(asset('storage/' . ($page['props']['meta']['twitter']['image'] ?? $settings->get('meta_img')))); ?>">

    <link rel="alternate" hreflang="en" href="<?php echo e(url('en')); ?>"/>
    <link rel="alternate" hreflang="ar" href="<?php echo e(url('ar')); ?>"/>
    <link rel="alternate" hreflang="x-default" href="<?php echo e(url('/')); ?>"/>

    <link rel="icon" type="image/png" href="<?php echo e(asset('images/favicon/favicon-96x96.png')); ?>" sizes="96x96"/>
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('images/favicon/favicon.svg')); ?>"/>
    <link rel="shortcut icon" href="<?php echo e(asset('images/favicon/favicon.ico')); ?>"/>
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('images/favicon/apple-touch-icon.png')); ?>"/>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
          rel="stylesheet">


    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          rel="stylesheet">

    <?php echo app('Tighten\Ziggy\BladeRouteGenerator')->generate(); ?>
    <?php if (!isset($__inertiaSsrDispatched)) { $__inertiaSsrDispatched = true; $__inertiaSsrResponse = app(\Inertia\Ssr\Gateway::class)->dispatch($page); }  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->head; } ?>

    <link rel="stylesheet" href="<?php echo e(asset('site/css/bootstrap.min.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/animate.min.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/custom-animate.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/swiper.min.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/font-awesome-all.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/jarallax.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/jquery.magnific-popup.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/odometer.min.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/flaticon.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('site/css/owl.carousel.min.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/owl.theme.default.min.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/nice-select.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/jquery-ui.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/aos.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/twentytwenty.css')); ?>"/>


    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/banner.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/slider.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/footer.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/services.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/sliding-text.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/about.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/counter.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/portfolio.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/process.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/contact.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/testimonial.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/brand.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/newsletter.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/team.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/pricing.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/event.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/blog.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/why-choose.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/feature.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/faq.css')); ?>"/>
    <link rel="stylesheet" href="<?php echo e(asset('site/css/module-css/cta.css')); ?>"/>


    <!-- template styles -->
    <link rel="stylesheet" href="<?php echo e(asset('site/css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('site/css/responsive.css')); ?>"/>


    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->getLocale() === "ar"): ?>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo e(asset('site/css/rtl.css')); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo $settings->get('header_scripts'); ?>

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


<?php if (!isset($__inertiaSsrDispatched)) { $__inertiaSsrDispatched = true; $__inertiaSsrResponse = app(\Inertia\Ssr\Gateway::class)->dispatch($page); }  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->body; } else { ?><div id="app" data-page="<?php echo e(json_encode($page)); ?>"></div><?php } ?>
<a href="#" data-target="html" class="scroll-to-target scroll-to-top">
    <span class="scroll-to-top__wrapper"><span class="scroll-to-top__inner"></span></span>
    <span class="scroll-to-top__text"> Go Back Top</span>
</a>


<script src="<?php echo e(asset('site/js/jquery-3.6.0.min.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/bootstrap.bundle.min.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/jarallax.min.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/jquery.ajaxchimp.min.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/jquery.appear.min.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/swiper.min.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/jquery.magnific-popup.min.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/jquery.validate.min.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/odometer.min.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/wNumb.min.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/wow.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/isotope.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/owl.carousel.min.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/jquery-ui.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/jquery.nice-select.min.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/twentytwenty.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/jquery.event.move.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/marquee.min.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/jquery.circleType.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/jquery.fittext.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/jquery.lettering.min.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/typed-2.0.11.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/jquery-sidebar-content.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/aos.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/countdown.min.js')); ?>"></script>


<script src="<?php echo e(asset('site/js/gsap/gsap.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/gsap/ScrollTrigger.js')); ?>"></script>
<script src="<?php echo e(asset('site/js/gsap/SplitText.js')); ?>"></script>


<!-- template js -->
<script src="<?php echo e(asset('site/js/script.js')); ?>"></script>
<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "<?php echo e(env('APP_NAME')); ?>",
      "url": "<?php echo e(env('APP_URL')); ?>",
      "logo": ""
    }
</script>
<script>
    <?php if(session('success')): ?>
    toastr.success('<?php echo e(session('success')); ?>');
    <?php elseif(session('error')): ?>
    toastr.error('<?php echo e(session('error')); ?>');
    <?php elseif(session('status')): ?>
    toastr.info('<?php echo e(session('status')); ?>');
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if($errors->any()): ?>
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    toastr.error('<?php echo e($error); ?>');
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</script>

<?php echo $settings->get('body_scripts'); ?>

</body>
</html>
<?php /**PATH D:\websites\symfonix\resources\views/app.blade.php ENDPATH**/ ?>