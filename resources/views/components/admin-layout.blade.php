@php
    use Modules\Base\Support\AdminBranding;

    $adminLogoUrl = AdminBranding::logoUrl();
    $adminMinLogoUrl = AdminBranding::minLogoUrl();
@endphp
<!DOCTYPE html>
<!--
Author: Hadi Hilal
-->
<html dir="{{ app()->getLocale() === "ar" ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}"
      style="{{app()->getLocale() === "ar" ? 'direction: rtl' : 'direction: ltr'}}">
<!--begin::Head-->
<head>
    <base href="">
    <title>@yield('title') - {{__('Admin Panel')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta charset="utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="robots" content="noindex">
<meta name="referrer" content="same-origin">

    <link rel="icon" type="image/png" href="{{asset('images/favicon/favicon-96x96.png')}}" sizes="96x96"/>
    <link rel="icon" type="image/svg+xml" href="{{asset('images/favicon/favicon.svg')}}"/>
    <link rel="shortcut icon" href="{{asset('images/favicon/favicon.ico')}}"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('images/favicon/apple-touch-icon.png')}}"/>
    <link rel="manifest" href="{{asset('images/favicon/site.webmanifest')}}"/>

    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700"/>
    <!--end::Fonts-->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.0/font/bootstrap-icons.min.css"
          integrity="sha512-vDQXRXKXpBHSJMOkJqqzWhgcAcaG3Bab87wklsuBuYOanSnZ3y76EJj1CpMxPW8o5A5PW7ScywzKC2G4BJN53A=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
          integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <!--end::Global Stylesheets Bundle-->
    @if(app()->getLocale() === "ar")
        <link href="{{asset('admin/plugins/global/plugins.bundle.rtl.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{asset('admin/css/style.bundle.rtl.css') }}" rel="stylesheet" type="text/css"/>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap"
              rel="stylesheet">
        <style>
            html, body, .modal .modal-header h1, .modal .modal-header .h1, .modal .modal-header h2,
            .modal .modal-header .h2, .modal .modal-header h3, .modal .modal-header .h3, .modal .modal-header h4,
            .modal .modal-header .h4, .modal .modal-header h5, .modal .modal-header .h5, .modal .modal-header h6, .modal .modal-header .h6 {
                font-family: 'Tajawal', sans-serif !important;
                line-height: 1.8;
            }
        </style>
    @else
        <link href="{{asset('admin/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{asset('admin/css/style.bundle.css') }}" rel="stylesheet" type="text/css"/>
    @endif

    <link href="{{ asset('admin/css/custom-admin.css') }}" rel="stylesheet" type="text/css"/>

    @yield('css')
    @livewireStyles
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true"
      data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
      data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
      data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
<script>
    var defaultThemeMode = "light";
    var themeMode;
    if (document.documentElement) {
        if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
            themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
        } else {
            if (localStorage.getItem("data-bs-theme") !== null) {
                themeMode = localStorage.getItem("data-bs-theme");
            } else {
                themeMode = defaultThemeMode;
            }
        }
        if (themeMode === "system") {
            themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        }
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    }</script>

<!--begin::Main-->
<!--begin::Root-->
<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
    <!--begin::Page-->
    <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
        <div id="kt_app_header" class="app-header" data-kt-sticky="true"
             data-kt-sticky-activate="{default: true, lg: true}" data-kt-sticky-name="app-header-minimize"
             data-kt-sticky-offset="{default: '200px', lg: '0'}" data-kt-sticky-animation="false">
            <!--begin::Header container-->
            <div class="app-container container-fluid d-flex align-items-stretch justify-content-between"
                 id="kt_app_header_container">
                <!--begin::Sidebar mobile toggle-->
                <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
                    <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                        <i class="ki-duotone ki-abstract-14 fs-2 fs-md-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <!--end::Sidebar mobile toggle-->
                <!--begin::Mobile logo-->
                <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                    <a href="{{ route('admin.dashboard.index') }}" class="d-lg-none">
                        <img alt="Logo" src="{{ $adminLogoUrl }}" class="h-30px"/>
                    </a>
                </div>
                <!--end::Mobile logo-->
                <!--begin::Header wrapper-->
                <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1"
                     id="kt_app_header_wrapper">
                    <!--begin::Menu wrapper-->
                    <div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true"
                         data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}"
                         data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end"
                         data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true"
                         data-kt-swapper-mode="{default: 'append', lg: 'prepend'}"
                         data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
                        <!--begin::Menu-->
                        <div
                            class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0"
                            id="kt_app_header_menu" data-kt-menu="true">
                            <!--begin:Menu item-->
                            <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                                 {{ app()->getLocale() === "ar" ? 'data-kt-menu-placement="bottom-end"' : 'data-kt-menu-placement="bottom-start"' }}
                                 class="menu-item here show menu-here-bg menu-lg-down-accordion me-0 me-lg-2">
                                <!--begin:Menu link-->
                                <span class="menu-link">
                                    <span class="menu-title">
                                       Symfonix
                                    </span>
                                    <span class="menu-arrow d-lg-none"></span>
                                </span>
                                <!--end:Menu link-->
                                <!--begin:Menu sub-->
                                <!--end:Menu sub-->
                            </div>
                            <!--end:Menu item-->
                        </div>
                        <!--end::Menu-->
                    </div>
                    <!--end::Menu wrapper-->
                    <!--begin::Navbar-->
                    <div class="app-navbar flex-shrink-0">
                        <!--begin::Notifications-->
                        <div class="app-navbar-item ms-1 ms-md-3">
                            <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px position-relative"
                                 data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                                 data-kt-menu-attach="parent"
                                 {{ app()->getLocale() === "ar" ? 'data-kt-menu-placement="bottom-start"' : 'data-kt-menu-placement="bottom-end"' }}>
                                <i class="ki-duotone ki-notification-status fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                @if($unreadNotificationCount > 0)
                                    <span class="bullet bullet-dot bg-success h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink"></span>
                                @endif
                            </div>
                            <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px"
                                 data-kt-menu="true">
                                <div class="d-flex flex-column bgi-no-repeat rounded-top bg-primary">
                                    <h3 class="text-white fw-semibold px-9 py-6 mb-0">
                                        {{ __('Notifications') }}
                                        @if($unreadNotificationCount > 0)
                                            <span class="fs-8 opacity-75 ps-3">{{ $unreadNotificationCount }} {{ __('new') }}</span>
                                        @endif
                                    </h3>
                                </div>
                                @if($recentNotifications->isNotEmpty())
                                    <div class="scroll-y mh-325px my-5 px-8">
                                        @foreach($recentNotifications as $notification)
                                            <form action="{{ route('admin.notifications.read', $notification->id) }}" method="POST" class="d-flex flex-stack py-4 border-bottom border-gray-200">
                                                @csrf
                                                <button type="submit" class="btn btn-link text-start p-0 text-decoration-none w-100">
                                                    <div class="d-flex align-items-start">
                                                        <div class="symbol symbol-35px me-4">
                                                            <span class="symbol-label bg-light-primary text-primary fw-semibold">
                                                                <i class="bi bi-ticket-detailed"></i>
                                                            </span>
                                                        </div>
                                                        <div class="mb-0 me-2 flex-grow-1">
                                                            <div class="fs-6 text-gray-800 fw-bold {{ $notification->read_at ? '' : 'text-primary' }}">
                                                                {{ $notification->data['message'] ?? __('Notification') }}
                                                            </div>
                                                            <div class="text-gray-500 fs-7">{{ $notification->created_at->diffForHumans() }}</div>
                                                        </div>
                                                    </div>
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                    <div class="py-3 text-center border-top">
                                        <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-color-gray-600 btn-active-color-primary">
                                                {{ __('Mark all as read') }}
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="px-5 py-8 text-center text-muted">
                                        <i class="ki-duotone ki-notification-bing fs-3x text-gray-400 mb-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <div class="fw-semibold fs-6">{{ __('No notifications yet') }}</div>
                                        <div class="fs-7">{{ __('You will see updates here when they arrive.') }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!--end::Notifications-->

                        <!--begin::Messages-->
                        @can('CRM Management')
                            <div class="app-navbar-item ms-1 ms-md-3">
                                <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px position-relative"
                                     data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                                     data-kt-menu-attach="parent"
                                     {{ app()->getLocale() === "ar" ? 'data-kt-menu-placement="bottom-start"' : 'data-kt-menu-placement="bottom-end"' }}>
                                    <i class="ki-duotone ki-message-text-2 fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    @if($unreadMessageCount > 0)
                                        <span class="bullet bullet-dot bg-success h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink"></span>
                                    @endif
                                </div>
                                <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px"
                                     data-kt-menu="true">
                                    <div class="d-flex flex-column bgi-no-repeat rounded-top bg-primary">
                                        <h3 class="text-white fw-semibold px-9 py-6 mb-0">
                                            {{ __('Messages') }}
                                            @if($unreadMessageCount > 0)
                                                <span class="fs-8 opacity-75 ps-3">{{ $unreadMessageCount }} {{ __('new') }}</span>
                                            @endif
                                        </h3>
                                    </div>
                                    @if($recentMessages->isNotEmpty())
                                        <div class="scroll-y mh-325px my-5 px-8">
                                            @foreach($recentMessages as $message)
                                                <div class="d-flex flex-stack py-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-35px me-4">
                                                            <span class="symbol-label bg-light-primary text-primary fw-semibold">
                                                                {{ strtoupper(substr($message->name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                        <div class="mb-0 me-2">
                                                            <a href="{{ route('admin.contact_forms.index') }}"
                                                               class="fs-6 text-gray-800 text-hover-primary fw-bold">{{ $message->name }}</a>
                                                            <div class="text-gray-500 fs-7">{{ Str::limit($message->subject, 40) }}</div>
                                                        </div>
                                                    </div>
                                                    <span class="badge badge-light fs-8">{{ $message->created_at->diffForHumans() }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="px-5 py-8 text-center text-muted">
                                            <i class="ki-duotone ki-sms fs-3x text-gray-400 mb-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="fw-semibold fs-6">{{ __('No messages yet') }}</div>
                                        </div>
                                    @endif
                                    <div class="py-3 text-center border-top">
                                        <a href="{{ route('admin.contact_forms.index') }}"
                                           class="btn btn-color-gray-600 btn-active-color-primary">
                                            {{ __('View All Messages') }}
                                            <i class="ki-duotone ki-arrow-right fs-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endcan
                        <!--end::Messages-->

                        <!--begin::User menu-->
                        <div class="app-navbar-item ms-1 ms-md-4" id="kt_header_user_menu_toggle">
                            <!--begin::Menu wrapper-->
                            <div class="cursor-pointer symbol symbol-35px"
                                 data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
                                {{ app()->getLocale() === "ar" ?'data-kt-menu-placement="bottom-end"' : 'data-kt-menu-placement="bottom-start"'   }}
                            >
                                <img src="{{$user->avatar}}" alt="user avatar"/>
                            </div>
                            <!--begin::User account menu-->
                            <div
                                class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                                data-kt-menu="true">
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <div class="menu-content d-flex align-items-center px-3">
                                        <!--begin::Avatar-->
                                        <div class="symbol symbol-50px me-5">
                                            <img src="{{$user->avatar}}" alt="user avatar"/>
                                        </div>
                                        <!--end::Avatar-->
                                        <!--begin::Username-->
                                        <div class="d-flex flex-column">
                                            <div class="fw-bold d-flex align-items-center fs-5">{{$user->name}}
                                                <span class="badge badge-light-success fw-bolder fs-9 px-2 py-1 ms-2">{{__($user->type)}}
                                                </span>
                                            </div>
                                            <a href="#"
                                               class="fw-semibold text-muted text-hover-primary fs-7">{{$user->email}}</a>
                                        </div>
                                        <!--end::Username-->
                                    </div>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu separator-->
                                <div class="separator my-2"></div>
                                <!--end::Menu separator-->

                                <div class="menu-item px-5">
                                    <a href="{{ route('admin.profile.index') }}" class="menu-link px-5">
                                        {{__('My Profile')}}
                                    </a>
                                </div>

                                <!--begin::Menu item-->
                                <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"

                                     {{ app()->getLocale() === "ar" ? 'data-kt-menu-placement="left-start"' : 'data-kt-menu-placement="right-start"' }}
                                     data-kt-menu-offset="-15px, 0">
                                    <a href="#" class="menu-link px-5">
												<span class="menu-title position-relative">{{__('Mode')}}
												<span class="ms-5 position-absolute translate-middle-y top-50 end-0">
													<i class="ki-duotone ki-night-day theme-light-show fs-2">
														<span class="path1"></span>
														<span class="path2"></span>
														<span class="path3"></span>
														<span class="path4"></span>
														<span class="path5"></span>
														<span class="path6"></span>
														<span class="path7"></span>
														<span class="path8"></span>
														<span class="path9"></span>
														<span class="path10"></span>
													</i>
													<i class="ki-duotone ki-moon theme-dark-show fs-2">
														<span class="path1"></span>
														<span class="path2"></span>
													</i>
												</span></span>
                                    </a>
                                    <!--begin::Menu-->
                                    <div
                                        class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                                        data-kt-menu="true" data-kt-element="theme-mode-menu">
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3 my-0">
                                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                                               data-kt-value="light">
														<span class="menu-icon" data-kt-element="icon">
															<i class="ki-duotone ki-night-day fs-2">
																<span class="path1"></span>
																<span class="path2"></span>
																<span class="path3"></span>
																<span class="path4"></span>
																<span class="path5"></span>
																<span class="path6"></span>
																<span class="path7"></span>
																<span class="path8"></span>
																<span class="path9"></span>
																<span class="path10"></span>
															</i>
														</span>
                                                <span class="menu-title">{{__('Light Mode')}}</span>
                                            </a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3 my-0">
                                            <a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                                               data-kt-value="dark">
														<span class="menu-icon" data-kt-element="icon">
															<i class="ki-duotone ki-moon fs-2">
																<span class="path1"></span>
																<span class="path2"></span>
															</i>
														</span>
                                                <span class="menu-title">{{__('Dark Mode')}}</span>
                                            </a>
                                        </div>
                                        <!--end::Menu item-->
                                    </div>
                                    <!--end::Menu-->
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"

                                     {{ app()->getLocale() === "ar" ? 'data-kt-menu-placement="left-start" ' : 'data-kt-menu-placement="right-start"'  }}
                                     data-kt-menu-offset="-15px, 0">
                                    <a href="#" class="menu-link px-5">
												<span class="menu-title position-relative">{{__('Language')}}
												<span
                                                    class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">
												<img class="w-15px h-15px rounded-1 ms-2"
                                                     src="/images/langs/{{ app()->getLocale()}}.png"
                                                     alt=""/></span></span>
                                    </a>
                                    <!--begin::Menu sub-->
                                    <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a class="menu-link d-flex px-5 {{ app()->getLocale()  === 'ar' ? 'active' : ''}}"
                                               rel="alternate" hreflang="ar"
                                               href="{{ LaravelLocalization::getLocalizedURL('ar') }}">
													<span class="symbol symbol-20px me-4">
														<img class="rounded-1"
                                                             src="{{asset('images/langs/ar.png')}}"
                                                             alt="saudi-arabia"/>
													</span>AR</a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a class="menu-link d-flex px-5 {{ app()->getLocale()  === 'en' ? 'active' : ''}}"
                                               rel="alternate" hreflang="en"
                                               href="{{ LaravelLocalization::getLocalizedURL('en') }}">
													<span class="symbol symbol-20px me-4">
														<img class="rounded-1"
                                                             src="{{asset('images/langs/en.png')}}"
                                                             alt="united-states"/>
													</span>EN</a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a class="menu-link d-flex px-5 {{ app()->getLocale()  === 'tr' ? 'active' : ''}}"
                                               rel="alternate" hreflang="tr"
                                                 href="{{ LaravelLocalization::getLocalizedURL('tr') }}">
                                        													<span
                                                                                                class="symbol symbol-20px me-4">
                                        														<img class="rounded-1"
                                                                                                     src="{{asset('images/langs/tr.png')}}"
                                                                                                     alt="turkey "/>
                                        													</span>TR</a>
                                        </div>
                                        <!--end::Menu item-->

                                    </div>
                                    <!--end::Menu sub-->
                                </div>
                                <!--end::Menu item-->

                                <!--begin::Menu item-->
                                <div class="menu-item px-5">
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                        @csrf
                                    </form>
                                    <a href="#"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                       class="menu-link px-5">{{__('Sign Out')}}</a>
                                </div>
                                <!--end::Menu item-->
                            </div>
                            <!--end::User account menu-->
                            <!--end::Menu wrapper-->
                        </div>
                        <!--end::User menu-->
                        <!--begin::Header menu toggle-->
                        <div class="app-navbar-item d-lg-none ms-2 me-n2" title="Show header menu">
                            <div class="btn btn-flex btn-icon btn-active-color-primary w-30px h-30px"
                                 id="kt_app_header_menu_toggle">
                                <i class="ki-duotone ki-element-4 fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        <!--end::Header menu toggle-->
                        <!--begin::Aside toggle-->
                        <!--end::Header menu toggle-->
                    </div>
                    <!--end::Navbar-->
                </div>
                <!--end::Header wrapper-->
            </div>
            <!--end::Header container-->
        </div>

        <!--end::Header-->
        <!--begin::Wrapper-->
        <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">

            <!--begin::Sidebar-->
            <div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true"
                 data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}"
                 data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start"
                 data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
                <!--begin::Logo-->
                <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">

                    <!--begin::Logo image-->
                    <a href="{{ route('admin.dashboard.index') }}" title="{{ __('Dashboard') }}">
                        <img alt="Logo" src="{{ $adminLogoUrl }}"
                             class="h-40px app-sidebar-logo-default"/>
                        <img alt="Logo" src="{{ $adminMinLogoUrl }}"
                             class="h-30px app-sidebar-logo-minimize"/>
                    </a>
                    <!--end::Logo image-->

                    <div id="kt_app_sidebar_toggle"
                         class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
                         data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
                         data-kt-toggle-name="app-sidebar-minimize">
                        <i class="ki-duotone ki-black-left-line fs-3 rotate-180">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <!--end::Sidebar toggle-->
                </div>
                <!--end::Logo-->
                <!--begin::sidebar menu-->
                <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
                    <!--begin::Menu wrapper-->
                    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
                        <!--begin::Scroll wrapper-->
                        <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true"
                             data-kt-scroll-activate="true" data-kt-scroll-height="auto"
                             data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
                             data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px"
                             data-kt-scroll-save-state="true">
                            <input type="text" id="admin-sidebar-search" class="form-control form-control-sm"
                                   placeholder="{{ __('Search menu...') }}" autocomplete="off"/>
                            <!--begin::Menu-->
                            <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6"
                                 id="kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
                                <x-admin.side-nav></x-admin.side-nav>
                            </div>
                            <!--end::Menu-->
                        </div>
                        <!--end::Scroll wrapper-->
                    </div>
                    <!--end::Menu wrapper-->
                </div>
                <!--end::sidebar menu-->
            </div>
            <!--end::Sidebar-->
            <!--begin::Main-->
            <main class="app-main flex-column flex-row-fluid" id="kt_app_main">
                <!--begin::Content wrapper-->
                <div class="d-flex flex-column flex-column-fluid">
                    <!--begin::Toolbar-->
                    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                        <!--begin::Toolbar container-->
                        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
                            @yield('toolbar')
                        </div>
                        <!--end::Toolbar container-->
                    </div>
                    <!--end::Toolbar-->
                    <!--begin::Content-->
                    <div id="kt_app_content" class="app-content flex-column-fluid">
                        <!--begin::Content container-->
                        <div id="kt_app_content_container" class="app-container container-fluid">
                            {{$slot}}
                        </div>
                        <!--end::Content container-->
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Content wrapper-->
                <!--begin::Footer-->
                <div id="kt_app_footer" class="app-footer">
                    <!--begin::Footer container-->
                    <div
                        class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
                        <!--begin::Copyright-->
                        <div class="text-dark order-2 order-md-1">
                            <span class="text-muted fw-bold me-1">{{__('All rights are reserved')}} 2026 ©</span>
                            <a href="https://www.linkedin.com/in/hadi-hilal" target="_blank"
                               class="text-gray-700 text-hover-primary">{{__('Developed By Hadi Hilal')}} </a>
                        </div>
                        <!--end::Copyright-->
                        <!--begin::Menu-->
                        <ul class="menu menu-gray-700 menu-hover-primary fw-bold order-1">
                            <li class="menu-item">
                                <a href="{{route('home')}}" target="_blank">
                                    {{__("Go To Website")}} <i class="bi bi-arrow-up-{{app()->getLocale() == 'ar' ? 'left':'right'}} mx-1 fa-2x"></i>
                                </a>
                            </li>
                        </ul>
                        <!--end::Menu-->
                    </div>
                    <!--end::Footer container-->
                </div>
                <!--end::Footer-->
            </main>
            <!--end:::Main-->
        </div>
        <!--end::Wrapper-->
    </div>

    <!--end::Page-->
    @yield('modal')
</div>
<!--end::Root-->

<!--end::Main-->
<!--begin::Javascript-->
<!--begin::Global JavaScript Bundle(used by all pages)-->
<script src="{{asset('admin/plugins/global/plugins.bundle.js')}}"></script>
<script src="{{asset('admin/js/scripts.bundle.js')}}"></script>
<script src="{{ asset('admin/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<!--end::Global JavaScript Bundle(used by all pages)-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js"
        integrity="sha512-6sSYJqDreZRZGkJ3b+YfdhB3MzmuP9R7X1QZ6g5aIXhRvR1Y/N/P47jmnkENm7YL3oqsmI6AK+V6AD99uWDnIw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: '{{ app()->getLocale() === "ar" ? "toast-top-left" : "toast-top-right" }}',
        timeOut: 4000,
        extendedTimeOut: 2000,
    };

    @if (session('success'))
    toastr.success(@json(session('success')));
    @elseif (session('error'))
    toastr.error(@json(session('error')));
    @endif
    @if ($errors->any())
    @foreach ($errors->all() as $error)
    toastr.error(@json($error));
    @endforeach
    @endif

    // Sidebar menu search
    (function () {
        const input = document.getElementById('admin-sidebar-search');
        if (!input) return;

        input.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const items = document.querySelectorAll('#kt_app_sidebar_menu > .menu-item, #kt_app_sidebar_menu > .menu-section-label');

            items.forEach(function (el) {
                if (el.classList.contains('menu-section-label')) return;

                const title = el.querySelector('.menu-title');
                const text = title ? title.textContent.trim().toLowerCase() : '';
                const subLinks = el.querySelectorAll('.menu-sub .menu-link');
                let subMatch = false;

                subLinks.forEach(function (link) {
                    const subTitle = link.querySelector('.menu-title');
                    const subText = subTitle ? subTitle.textContent.trim().toLowerCase() : '';
                    const match = !query || subText.includes(query);
                    link.closest('.menu-item').classList.toggle('menu-search-hidden', !match && query);
                    if (match) subMatch = true;
                });

                const parentMatch = !query || text.includes(query) || subMatch;
                el.classList.toggle('menu-search-hidden', !parentMatch);

                if (query && subMatch && el.classList.contains('menu-accordion')) {
                    el.classList.add('here', 'show');
                    const sub = el.querySelector('.menu-sub-accordion');
                    if (sub) sub.classList.add('show');
                }
            });
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                this.value = '';
                this.dispatchEvent(new Event('input'));
                this.blur();
            }
        });
    })();

    // Confirm before inline delete forms
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form.matches('form[method="POST"]') || !form.querySelector('input[name="_method"][value="DELETE"]')) return;
        if (form.dataset.confirmed === 'true') return;

        e.preventDefault();
        Swal.fire({
            text: @json(__('Are you sure you want to delete it?')),
            icon: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: @json(__('Yes, Delete!')),
            cancelButtonText: @json(__('No, Cancel')),
            customClass: {
                confirmButton: 'btn fw-bold btn-danger',
                cancelButton: 'btn fw-bold btn-active-light-primary',
            },
        }).then(function (result) {
            if (result.isConfirmed) {
                form.dataset.confirmed = 'true';
                form.submit();
            }
        });
    });

    // Reusable function for making AJAX requests
    function makeAjaxRequest(url, method, data, dataType = "json", onSuccess) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: url,
            method: method,
            dataType: dataType,
            data: data,
            success: onSuccess,
            error: function () {
                toastr.error('{{ __('An Error Occurred!') }}');
            }
        });
    }

    function generateSlug(text) {
        return text
            .toLowerCase()
            .replace(/[0-9]/g, '') // Remove numbers
            .replace(/[^\w\s-]/g, '') // Remove non-word characters
            .replace(/\s+/g, '-') // Replace whitespace with dashes
            .replace(/--+/g, '-') // Replace multiple dashes with a single dash
            .trim(); // Trim leading/trailing whitespace and dashes
    }

    $('#gslug').on('input', function () {
        let val = $(this).val(),
            slug = generateSlug(val);

        if (slug !== '') {
            let viewSlug = "https://domain.com/" + slug
            $('#link').removeClass('text-danger').addClass('text-primary').css('text-decoration', 'underline').text(viewSlug);
            $('#slug').val(slug);
        } else {
            $('#link').addClass('text-danger').css('text-decoration', '').text("{{__('The Slug Should Be English')}}");
        }

    });


</script>
@livewireScripts
@yield('js')
@stack('scripts')
<!--end::Javascript-->
</body>
<!--end::Body-->
</html>
