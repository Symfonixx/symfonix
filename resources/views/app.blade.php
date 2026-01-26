<!---
Site: Sham Vision
Created At: 01-01-2026
Developed By: Hadi Hilal
--->
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
        $appLogoUrl = asset('images/logo.png');
    @endphp
    <meta name="author" content="{{ $seo->get('website_name') }}">
    <meta name="theme-color" content="#cc3333"/>

    <title inertia>{{ $page['props']['meta']['title'] ?? $seo->get('website_name') }}</title>
    <meta name="description" content="{{ $page['props']['meta']['description'] ?? $seo->get('website_desc') }}">
    <meta name="keywords" content="{{ $page['props']['meta']['keywords'] ?? $seo->get('website_keywords') }}">

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
    <link rel="stylesheet" href="{{ asset('site/css/font-awesome-all.css') }}"/>
{{--    <link rel="stylesheet" href="{{ asset('site/css/odometer.min.css') }}"/>--}}
    <link rel="stylesheet" href="{{ asset('site/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/owl.carousel.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/owl.theme.default.min.css') }}"/>


    <link rel="stylesheet" href="{{ asset('site/css/module-css/banner.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/slider.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/footer.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/services.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/sliding-text.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/about.css') }}"/>
{{--    <link rel="stylesheet" href="{{ asset('site/css/module-css/counter.css') }}"/>--}}
    <link rel="stylesheet" href="{{ asset('site/css/module-css/portfolio.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/process.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/contact.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/testimonial.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/brand.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/newsletter.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/team.css') }}"/>
{{--    <link rel="stylesheet" href="{{ asset('site/css/module-css/pricing.css') }}"/>--}}
    <link rel="stylesheet" href="{{ asset('site/css/module-css/event.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/blog.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/why-choose.css') }}"/>
    <link rel="stylesheet" href="{{ asset('site/css/module-css/feature.css') }}"/>
{{--    <link rel="stylesheet" href="{{ asset('site/css/module-css/faq.css') }}"/>--}}
    <link rel="stylesheet" href="{{ asset('site/css/module-css/cta.css') }}"/>


    <!-- template styles -->
    <link rel="stylesheet" href="{{ asset('site/css/style.css') }}">


    <style>
        /* ShamBot custom widget */
        #symfonixbot-launcher-wrap {
            position: fixed;
            bottom: 20px;
            right: 20px;
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
            animation: symfonixbot-launcher-pop 650ms cubic-bezier(.2,.9,.2,1) 200ms both;
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
            transition: transform 180ms ease, box-shadow 180ms ease, opacity 180ms ease;
            animation: symfonixbot-hint-pop 650ms cubic-bezier(.2,.9,.2,1) both,
                       symfonixbot-hint-float 2.8s ease-in-out 900ms infinite;
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

        #symfonixbot-launcher-wrap:hover #symfonixbot-launcher-hint:not(.symfonixbot-hidden) {
            transform: translateY(-1px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.18);
        }

        @keyframes symfonixbot-hint-pop {
            0% { opacity: 0; transform: translateX(10px) scale(0.92); }
            60% { opacity: 1; transform: translateX(0) scale(1.04); }
            100% { opacity: 1; transform: translateX(0) scale(1); }
        }

        @keyframes symfonixbot-launcher-pop {
            0% { transform: scale(0.92); filter: saturate(0.9); }
            60% { transform: scale(1.06); filter: saturate(1.05); }
            100% { transform: scale(1); filter: saturate(1); }
        }

        @keyframes symfonixbot-hint-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-2px); }
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
            max-width: 95vw;
            max-height: 480px;
            min-height: 400px;
            background: #f9f9f9;
            border-radius: 16px;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 9999;
            font-family: 'Roboto', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 14px;
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
        .content > *  , .blog-details__text > *  , .services-details__text-1 > *{
            line-height: 2.5rem!important;
        }
    </style>


    @if(app()->getLocale() === "ar")

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('site/css/rtl.css')}}">
    @endif
   <link rel="stylesheet" href="{{ asset('site/css/responsive.css') }}"/>
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
<div id="symfonixbot-launcher-wrap" aria-label="Symfonix Bot launcher">
    <div id="symfonixbot-launcher-hint" class="fade-in">{{ __('chat.launcher.ask_me') }}</div>
    <div id="symfonixbot-launcher" aria-label="{{ __('chat.launcher.open') }}">
        <img src="{{ asset('images/robot.png') }}" alt="{{ __('chat.launcher.title') }}">
    </div>
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
<a href="#" data-target="html" class="scroll-to-target scroll-to-top d-none d-lg-flex">
    <span class="scroll-to-top__wrapper"><span class="scroll-to-top__inner"></span></span>
    <span class="scroll-to-top__text"> {{__('Go Back Top')}}</span>
</a>


<script src="{{ asset('site/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('site/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('site/js/jquery.appear.min.js') }}"></script>
<script src="{{ asset('site/js/wow.js') }}"></script>
<script src="{{ asset('site/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('site/js/marquee.min.js') }}"></script>

<script src="{{ asset('site/js/gsap/gsap.js') }}"></script>
<script src="{{ asset('site/js/gsap/ScrollTrigger.js') }}"></script>
<script src="{{ asset('site/js/gsap/SplitText.js') }}"></script>


<!-- template js -->
<script src="{{ asset('site/js/script.js') }}"></script>
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
            const wrap = document.createElement('div');
            wrap.className = 'symfonixbot-message ' + (from === 'user' ? 'symfonixbot-message-user' : '');
            const bubble = document.createElement('div');
            bubble.className = 'symfonixbot-bubble ' + (from === 'user' ? 'symfonixbot-bubble-user' : 'symfonixbot-bubble-bot');
            bubble.innerHTML = html;
            wrap.appendChild(bubble);
            messagesEl.appendChild(wrap);
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
                    sendMessage(action.text || '', { interactive: true, value: action.value });
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
<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "{{env('APP_NAME')}}",
      "url": "{{env('APP_URL')}}",
      "logo": "{{ storage_path('storage' . $settings->get('site_logo')) }}"
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
