<template>

    <!-- Start sidebar widget content -->
    <div class="xs-sidebar-group info-group info-sidebar">
        <div class="xs-overlay xs-bg-black"></div>
        <div class="xs-sidebar-widget">
            <div class="sidebar-widget-container">
                <div class="widget-heading">
                    <a href="#" class="close-side-widget">X</a>
                </div>
                <div class="sidebar-textwidget">
                    <div class="sidebar-info-contents">
                        <div class="content-inner">
                            <div class="logo">
                                <Link :href="route('home')"><img :src="storage_path + settings.site_logo" alt="logo"/>
                                </Link>
                            </div>
                            <div class="content-box">
                                <h4>{{ trans('About Us') }}</h4>
                                <p>{{ seo.about_us }}</p>
                            </div>
                            <div class="form-inner">
                                <h4>{{ trans('Get a price quote') }}</h4>
                                <form
                                    class="contact-form-validated"
                                    novalidate="novalidate"
                                    @submit.prevent="handleContactSubmit"
                                >
                                    <div class="form-group">
                                        <input
                                            type="text"
                                            name="name"
                                            v-model="contactForm.name"
                                            :placeholder="trans('Name')"
                                            :disabled="contactForm.processing"
                                            required=""
                                        >
                                        <div v-if="contactForm.errors.name" class="text-danger mt-1 small">
                                            {{ contactForm.errors.name }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input
                                            type="email"
                                            name="email"
                                            v-model="contactForm.email"
                                            :placeholder="trans('Email')"
                                            :disabled="contactForm.processing"
                                            required=""
                                        >
                                        <div v-if="contactForm.errors.email" class="text-danger mt-1 small">
                                            {{ contactForm.errors.email }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input
                                            type="text"
                                            name="mobile"
                                            v-model="contactForm.mobile"
                                            :placeholder="trans('Phone')"
                                            :disabled="contactForm.processing"
                                            required=""
                                        >
                                        <div v-if="contactForm.errors.mobile" class="text-danger mt-1 small">
                                            {{ contactForm.errors.mobile }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input
                                            type="text"
                                            name="subject"
                                            v-model="contactForm.subject"
                                            :placeholder="trans('Subject')"
                                            :disabled="contactForm.processing"
                                            required=""
                                        >
                                        <div v-if="contactForm.errors.subject" class="text-danger mt-1 small">
                                            {{ contactForm.errors.subject }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <textarea
                                            name="message"
                                            v-model="contactForm.message"
                                            :placeholder="trans('Message')"
                                            :disabled="contactForm.processing"
                                            required=""
                                        ></textarea>
                                        <div v-if="contactForm.errors.message" class="text-danger mt-1 small">
                                            {{ contactForm.errors.message }}
                                        </div>
                                    </div>
                                    <div class="form-group message-btn">
                                        <button
                                            type="submit"
                                            class="thm-btn form-inner__btn"
                                            :disabled="contactForm.processing"
                                        >
                                            <span v-if="contactForm.processing">
                                                {{ trans('Sending...') }}
                                            </span>
                                            <span v-else>
                                                {{ trans('Submit') }}
                                            </span>

                                            <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow `"></span>
                                        </button>
                                    </div>
                                    <div
                                        v-if="contactSubmitSuccess"
                                        class="alert alert-success mt-2"
                                    >
                                        {{ trans("Thank you for contacting us! We will get back to you soon.") }}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End sidebar widget content -->


    <div class="page-wrapper">
        <header class="main-header-two">
            <div class="main-menu-two__top">
                <div class="main-menu-two__top-inner">
                    <p class="main-menu-two__top-text">{{ trans('We Build Technology In Perfect Harmony') }}</p>
                    <ul class="list-unstyled main-menu-two__contact-list">
                        <li>
                            <div class="icon">
                                <i class="icon-pin"></i>
                            </div>
                            <div class="text">
                                <p>{{ settings.address }}</p>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <i class="icon-search-mail"></i>
                            </div>
                            <div class="text">
                                <p><a dir="ltr" :href="`mailto::${settings.email}`">{{ settings.email }}</a>
                                </p>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <i class="icon-phone-call"></i>
                            </div>
                            <div class="text">
                                <p><a dir="ltr" :href="`tel::${settings.phone}`">{{ settings.phone }}</a></p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <MainMenuNav/>
        </header>

        <div class="stricky-header stricked-menu main-menu main-menu-two">
            <div class="sticky-header__content">
                <MainMenuNav/>
            </div><!-- /.sticky-header__content -->
        </div><!-- /.stricky-header -->


            <slot/>

        <!-- Newsletter Two Start -->
        <section class="newsletter-two">
            <div class="newsletter-two__shape-1">
                <img :src="asset_path + 'site/images/shapes/newsletter-two-shape-1.png'"
                     :alt="trans('Newsletter decoration')">
            </div>
            <div class="newsletter-two__shape-2 float-bob-x">
                <img :src="asset_path + 'site/images/shapes/newsletter-two-shape-2.png'"
                     :alt="trans('Newsletter decoration')">
            </div>
            <div class="container">
                <div class="newsletter-two__inner">
                    <div class="newsletter-two__left">
                        <h2 class="newsletter-two__title">{{ trans('Subscribe to Our Newsletter') }}</h2>
                        <p class="newsletter-two__text">
                            {{
                                trans('Engineering insights, product updates, and practical tech lessons—delivered occasionally, not daily')
                            }}</p>
                    </div>
                    <div class="newsletter-two__right">
                        <form class="newsletter-two__form" @submit.prevent="handleSubscribeSubmit">
                            <div class="newsletter-two__input">
                                <input
                                    type="email"
                                    name="email"
                                    v-model="subscribeForm.email"
                                    :placeholder="trans('Enter your email address')"
                                    :disabled="subscribeForm.processing"
                                    required=""
                                >
                                <div v-if="subscribeForm.errors.email" class="text-danger mt-1 small">
                                    {{ subscribeForm.errors.email }}
                                </div>
                            </div>
                            <button
                                type="submit"
                                class="thm-btn"
                                :disabled="subscribeForm.processing"
                            >
                                <span v-if="subscribeForm.processing">
                                    {{ trans('Subscribing...') }}
                                </span>
                                <span v-else>
                                    {{ trans('Subscribe Now') }}
                                </span>
                                <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow `"></span>
                            </button>
                            <div class="checked-box">
                                <input type="checkbox" name="skipper1" id="skipper" checked="">
                                <label for="skipper">
                                    <span></span>
                                    <Link :href="route('privacy-policy')">
                                        {{ trans('By subscribing, you accept our privacy policy') }}
                                    </Link>
                                </label>
                            </div>
                            <div
                                v-if="subscribeSuccess"
                                class="alert alert-success mt-2"
                            >
                                {{ trans('Thank you for subscribing to our newsletter!') }}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- Newsletter Two End -->


        <!--Site Footer Two Start-->
        <footer class="site-footer-two">
            <div class="site-footer-two__top">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms">
                            <div class="site-footer-two__about">
                                <div class="site-footer-two__logo">
                                    <Link :href="route('home')"><img :src="storage_path + settings.site_logo"
                                                                     alt="logo"></Link>
                                </div>
                                <ul class="list-unstyled site-footer-two__contact-list">
                                    <li>
                                        <div class="site-footer-two__contact-icon">
                                            <span class="icon-contact"></span>
                                        </div>
                                        <div class="site-footer-two__contact-content">
                                            <p class="site-footer-two__contact-info">
                                                <a
                                                    :href="`mailto:${settings.email}`"
                                                    class="site-footer-two__contact-mail"
                                                >
                                                    {{ settings.email }}
                                                </a>
                                                <a
                                                    :href="`tel:${settings.phone}`"
                                                    class="site-footer-two__contact-phone"
                                                >
                                                    <span dir="ltr">{{ settings.phone }} </span>
                                                </a>
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="site-footer-two__contact-icon">
                                            <span class="icon-pin"></span>
                                        </div>
                                        <div class="site-footer-two__contact-content">
                                            <p class="site-footer-two__contact-info">
                                                {{ settings.address }}
                                            </p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                            <div class="footer-widget-two__quick-links">
                                <h4 class="footer-widget-two__title">{{ trans('Pages') }}</h4>
                                <ul class="footer-widget-two__quick-links-list list-unstyled">

<!--                                    <li>-->
<!--                                        <Link :href="route('team')">-->
<!--                                            <span-->
<!--                                                :class="locale === 'ar' ? 'icon-left-arrow-2' : 'icon-right-arrow-2'"></span>{{-->
<!--                                                trans('Our Members')-->
<!--                                            }}-->
<!--                                        </Link>-->
<!--                                    </li>-->

                                    <li>
                                        <Link :href="route('testimonials')">
                                            <span
                                                :class="locale === 'ar' ? 'icon-left-arrow-2' : 'icon-right-arrow-2'"></span>{{
                                                trans('Testimonials')
                                            }}
                                        </Link>
                                    </li>

                                    <li v-for="pageItem in footerPages" :key="pageItem.id">
                                        <Link :href="getPageUrl(pageItem)">
                                            <span
                                                :class="locale === 'ar' ? 'icon-left-arrow-2' : 'icon-right-arrow-2'"></span>{{
                                                getTranslatableTitle(pageItem)
                                            }}
                                        </Link>
                                    </li>


                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="300ms">
                            <div class="footer-widget-two__support">
                                <h4 class="footer-widget-two__title">{{ trans('Quick Links') }}</h4>
                                <ul class="footer-widget-two__quick-links-list list-unstyled">
                                    <li>
                                        <Link :href="route('home')">
                                            <span
                                                :class="locale === 'ar' ? 'icon-left-arrow-2' : 'icon-right-arrow-2'"></span>{{
                                                trans('Home')
                                            }}
                                        </Link>
                                    </li>
                                    <li>
                                        <Link :href="route('about-us')">
                                            <span
                                                :class="locale === 'ar' ? 'icon-left-arrow-2' : 'icon-right-arrow-2'"></span>{{
                                                trans('About Us')
                                            }}
                                        </Link>
                                    </li>
                                    <li>
                                        <Link :href="route('services.index')">
                                            <span
                                                :class="locale === 'ar' ? 'icon-left-arrow-2' : 'icon-right-arrow-2'"></span>{{
                                                trans('Our Services')
                                            }}
                                        </Link>
                                    </li>
                                    <li>
                                        <Link :href="route('blogs.index')">
                                            <span
                                                :class="locale === 'ar' ? 'icon-left-arrow-2' : 'icon-right-arrow-2'"></span>{{
                                                trans('Blogs')
                                            }}
                                        </Link>
                                    </li>
                                    <li>
                                        <Link :href="route('contact-us')">
                                            <span
                                                :class="locale === 'ar' ? 'icon-left-arrow-2' : 'icon-right-arrow-2'"></span>{{
                                                trans('Contact Us')
                                            }}
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="400ms">
                            <div class="footer-widget-two__services">
                                <h4 class="footer-widget-two__title">{{ trans('Our Services') }}</h4>
                                <ul class="footer-widget-two__quick-links-list list-unstyled">
                                    <li v-for="service in (servicesList || []).slice(0, 3)" :key="service.id">
                                        <Link :href="route('services.index' , { category: service.slug })">
                                            <span
                                                :class="locale === 'ar' ? 'icon-left-arrow-2' : 'icon-right-arrow-2'"></span>{{
                                                getTranslatableTitle(service)
                                            }}
                                        </Link>
                                    </li>
                                    <li v-if="!servicesList || servicesList.length === 0">
                                        <Link :href="route('services.index' )">
                                            <span
                                                :class="locale === 'ar' ? 'icon-left-arrow-2' : 'icon-right-arrow-2'"></span>{{
                                                trans('View All Services')
                                            }}
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="site-footer-two__bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="site-footer-two__bottom-inner">
                                <div class="site-footer-two__copyright">
                                    <p class="site-footer-two__copyright-text">
                                        {{ trans('All rights are reserved') }}
                                        {{ new Date().getFullYear() }} ©
                                        <Link :href="route('home')">{{ seo.website_name }}</Link>

                                    </p>
                                </div>
                                <div class="site-footer-two__social-box">
                                    <h3 class="h4 site-footer-two__social-title">{{ trans('Follow Us') }}:</h3>
                                    <div class="site-footer-two__social-box-inner">
                                        <a
                                            v-if="settings.whatsapp"
                                            :href="settings.whatsapp"
                                            target="_blank"
                                            rel="noopener"
                                            aria-label="Whatsapp"
                                        >
                                            <span class="icon-whatsapp"></span>
                                        </a>

                                        <a
                                            v-if="settings.facebook"
                                            :href="settings.facebook"
                                            target="_blank"
                                            rel="noopener"
                                            aria-label="Facebook"
                                        >
                                            <span class="icon-facebook"></span>
                                        </a>
                                        <a
                                            v-if="settings.instagram"
                                            :href="settings.instagram"
                                            target="_blank"
                                            rel="noopener"
                                            aria-label="Instagram"
                                        >
                                            <span class="fab fa-instagram"></span>
                                        </a>
                                        <a
                                            v-if="settings.linkedin"
                                            :href="settings.linkedin"
                                            target="_blank"
                                            rel="noopener"
                                            aria-label="LinkedIn"
                                        >
                                            <span class="icon-linkedin"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!--Site Footer Two End-->


    </div><!-- /.page-wrapper -->


    <div class="mobile-nav__wrapper">
        <div class="mobile-nav__overlay mobile-nav__toggler"></div>
        <!-- /.mobile-nav__overlay -->
        <div class="mobile-nav__content">
            <span class="mobile-nav__close mobile-nav__toggler"><i class="fa fa-times"></i></span>

            <div class="logo-box">
                <Link :href="route('home')" aria-label="logo image">
                    <img :src="storage_path + settings.site_logo" alt="logo">
                </Link>
            </div>
            <!-- /.logo-box -->
            <div class="mobile-nav__container">
                <MainMenuList/>
            </div>
            <!-- /.mobile-nav__container -->

            <ul class="mobile-nav__contact list-unstyled">
                <li>
                    <i class="fa fa-envelope"></i>
                    <a :href="`mailto:${settings.email}`">{{ settings.email }}</a>
                </li>
                <li>
                    <i class="fas fa-phone"></i>
                    <a :href="`tel:${settings.phone}`">{{ settings.phone }}</a>
                </li>
            </ul><!-- /.mobile-nav__contact -->
            <div class="mobile-nav__top">
                <div class="mobile-nav__social">
                    <a
                        v-if="settings.twitter"
                        :href="settings.twitter"
                        class="fab fa-twitter me-2"
                        target="_blank"
                        rel="noopener"
                        aria-label="Twitter"
                    ></a>

                    <a
                        v-if="settings.whatsapp"
                        :href="settings.whatsapp"
                        class="fab fa-whatsapp me-2"
                        target="_blank"
                        rel="noopener"
                        aria-label="Whatsapp"
                    ></a>

                    <a
                        v-if="settings.facebook"
                        :href="settings.facebook"
                        class="fab fa-facebook me-2"
                        target="_blank"
                        rel="noopener"
                        aria-label="Facebook"
                    ></a>
                    <a
                        v-if="settings.instagram"
                        :href="settings.instagram"
                        class="fab fa-instagram me-2"
                        target="_blank"
                        rel="noopener"
                        aria-label="Instagram"
                    ></a>
                    <a
                        v-if="settings.linkedin"
                        :href="settings.linkedin"
                        class="fab fa-linkedin me-2"
                        target="_blank"
                        rel="noopener"
                        aria-label="Linkedin"
                    ></a>

                </div><!-- /.mobile-nav__social -->
            </div><!-- /.mobile-nav__top -->


        </div>
        <!-- /.mobile-nav__content -->
    </div>
    <!-- /.mobile-nav__wrapper -->

    <!-- Search Popup -->
    <div class="search-popup">
        <div class="color-layer"></div>
        <button class="close-search"><span class="far fa-times fa-fw"></span></button>
        <form @submit.prevent="handleSearchSubmit">
            <div class="form-group">
                <input
                    type="search"
                    name="search"
                    v-model="searchForm.search"
                    :placeholder="trans('Search Here')"
                    required=""
                >
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>


</template>

<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue'
import {Link, router, useForm, usePage} from '@inertiajs/vue3'
import MainMenuList from '@/Components/MainMenuList.vue'
import MainMenuNav from '@/Components/MainMenuNav.vue'

const page = usePage()

const trans = (key) => page.props.translations[key] || key;
const settings = computed(() => page.props.settings)
const storage_path = computed(() => page.props.storage_path)
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale)
const seo = computed(() => page.props.seo)
const servicesList = computed(() => page.props.servicesList)
const footerPages = computed(() => page.props.footerPages || [])


const subscribeSuccess = ref(false)

const subscribeForm = useForm({
    email: '',
})

const searchForm = useForm({
    search: '',
})

// Contact form (shared with Support::ContactUsController)
const contactSubmitSuccess = ref(false)

const contactForm = useForm({
    name: '',
    email: '',
    mobile: '',
    subject: '',
    message: '',
})

const handleContactSubmit = () => {
    if (contactForm.processing) {
        return false;
    }

    if (!contactForm.name || !contactForm.name.trim()) return false;
    if (!contactForm.email || !contactForm.email.trim()) return false;
    if (!contactForm.mobile || !contactForm.mobile.trim()) return false;
    if (!contactForm.subject || !contactForm.subject.trim()) return false;
    if (!contactForm.message || !contactForm.message.trim()) return false;

    let contactUrl = route('contact-us.store');
    contactForm.post(contactUrl, {
        preserveScroll: true,
        preserveState: true,
        onBefore: () => {
            contactSubmitSuccess.value = false;
        },
        onSuccess: () => {
            contactSubmitSuccess.value = true;
            contactForm.reset();
            contactForm.clearErrors();
            setTimeout(() => {
                contactSubmitSuccess.value = false;
            }, 5000);
        },
        onError: () => {
            contactSubmitSuccess.value = false;
        },
    });

    return false;
};

const handleSearchSubmit = () => {
    if (searchForm.processing) {
        return false;
    }

    const keyword = (searchForm.search || '').trim();
    if (!keyword) {
        return false;
    }

    let blogsUrl = route('blogs.index');
    searchForm.get(blogsUrl, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
        onFinish: () => {
            // Close search popup after navigating
            document.body.classList.remove('search-active');
        },
    });

    return false;
};


const handleSubscribeSubmit = () => {
    if (subscribeForm.processing) {
        return false;
    }

    if (!subscribeForm.email || !subscribeForm.email.trim()) {
        return false;
    }

    let subscribeUrl = route('subscribe');
    subscribeForm.post(subscribeUrl, {
        preserveScroll: true,
        preserveState: true,
        onBefore: () => {
            subscribeSuccess.value = false;
        },
        onSuccess: () => {
            subscribeSuccess.value = true;
            subscribeForm.reset();
            subscribeForm.clearErrors();
            setTimeout(() => {
                subscribeSuccess.value = false;
            }, 5000);
        },
        onError: () => {
            subscribeSuccess.value = false;
        },
    });

    return false;
};

onMounted(() => {
    const unregisterNavigate = router.on('navigate', (event) => {
        $(".mobile-nav__wrapper").removeClass("expanded");
        $("body").removeClass("locked");
        $("body").removeClass("search-active");
        $(".info-group").removeClass("isActive");
    });

    onUnmounted(() => {
        unregisterNavigate();
    });

    // Mobile Nav Toggler
    if ($(".mobile-nav__toggler").length) {
        $(".mobile-nav__toggler").off("click").on("click", function (e) {
            e.preventDefault();
            $(".mobile-nav__wrapper").toggleClass("expanded");
            $("body").toggleClass("locked");
        });
    }

    // Sidebar Toggler
    if ($(".navSidebar-button").length) {
        $(".navSidebar-button").off("click").on("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(".info-group").addClass("isActive");
        });
    }

    if ($(".close-side-widget").length) {
        $(".close-side-widget").off("click").on("click", function (e) {
            e.preventDefault();
            $(".info-group").removeClass("isActive");
        });
    }

    $("body").off("click.infoGroup").on("click.infoGroup", function (e) {
        $(".info-group").removeClass("isActive");
    });

    $(".xs-sidebar-widget").off("click").on("click", function (e) {
        e.stopPropagation();
    });

    // Header Search
    if ($('.searcher-toggler-box').length) {
        $('.searcher-toggler-box').off('click').on('click', function () {
            $('body').addClass('search-active');
        });
        $('.close-search').off('click').on('click', function () {
            $('body').removeClass('search-active');
        });
        $('.search-popup .color-layer').off('click').on('click', function () {
            $('body').removeClass('search-active');
        });
    }

    if ($(".mobile-nav__container .main-menu__list").length) {
        let dropdownAnchor = $(".mobile-nav__container .main-menu__list .dropdown > a");
        dropdownAnchor.each(function () {
            let self = $(this);
            if (self.find('button[aria-label="dropdown toggler"]').length === 0) {
                let toggleBtn = document.createElement("BUTTON");
                toggleBtn.setAttribute("aria-label", "dropdown toggler");
                toggleBtn.innerHTML = "<i class='fa fa-angle-down'></i>";
                self.append(function () {
                    return toggleBtn;
                });
            }
            self.find("button").off("click").on("click", function (e) {
                e.preventDefault();
                let self = $(this);
                self.toggleClass("expanded");
                self.parent().toggleClass("expanded");
                self.parent().parent().children("ul").slideToggle();
            });
        });
    }

    // Sticky Menu and Scroll to Top
    $(window).off("scroll.appLayout").on("scroll.appLayout", function () {
        if ($(".stricked-menu").length) {
            var headerScrollPos = 300;
            var stricky = $(".stricked-menu");
            if ($(window).scrollTop() > headerScrollPos) {
                stricky.addClass("stricky-fixed");
            } else if ($(this).scrollTop() <= headerScrollPos) {
                stricky.removeClass("stricky-fixed");
            }
        }

        var scrollToTopBtn = ".scroll-to-top";
        if ($(scrollToTopBtn).length) {
            if ($(window).scrollTop() > 500) {
                $(scrollToTopBtn).addClass("show");
            } else {
                $(scrollToTopBtn).removeClass("show");
            }
        }
    });

    // Theme Toggle
    const toggle = document.getElementById('themeToggle');
    const root = document.documentElement;
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) root.setAttribute('data-theme', savedTheme);

    toggle?.addEventListener('click', () => {
        const current = root.getAttribute('data-theme');
        const next = current === 'light' ? 'dark' : 'light';
        root.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);
    });
});

// Helper function to get translatable title
const getTranslatableTitle = (item) => {
    if (!item || !item.title) {
        return '';
    }
    if (typeof item.title === 'string') {
        return item.title;
    }
    if (typeof item.title === 'object') {
        return item.title[locale.value] || item.title['en'] || item.title[Object.keys(item.title)[0]] || '';
    }
    return '';
};

// Helper function to get page URL
const getPageUrl = (pageItem) => {
    if (!pageItem || !pageItem.slug) {
        return '#';
    }
    try {
        return route('page.view', pageItem.slug);
    } catch (e) {
        return `#`;
    }
};

// Helper function to get service URL


</script>

