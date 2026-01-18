<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'" />
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/about.css'" />
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/why-choose.css'" />
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/team.css'" />
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/process.css'" />
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/testimonial.css'" />
        <link rel="stylesheet" :href="asset_path + 'site/css/odometer.min.css'" />
        <title>{{trans('About Us')}} | {{seo.website_name}}</title>
    </Head>
    <app-layout>

        <!--Page Header Start-->
        <section class="page-header">
            <div class="page-header__bg" :style="{ backgroundImage: `url(${asset_path}images/contact-header-bg.jpg)` }">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans('About Us') }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li>
                                <Link :href="route('home')" v-if="typeof route !== 'undefined'">
                                    <i class="fas fa-home"></i>{{ trans('Home') }}
                                </Link>
                                <a :href="`/${locale === 'ar' ? 'ar' : ''}`" v-else>
                                    <i class="fas fa-home"></i>{{ trans('Home') }}
                                </a>
                            </li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`""></span></li>
                            <li>{{ trans('About Us') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!--Page Header End-->

        <!--About Four Start-->
        <section class="about-four">
            <div class="about-four__bg-shape"
                :style="{ backgroundImage: `url(${asset_path}images/shapes/about-four-bg-shape.png)` }"></div>
            <div class="about-four__bg-shape-2"
                :style="{ backgroundImage: `url(${asset_path}images/shapes/about-four-bg-shape-2.png)` }"></div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="about-four__left wow slideInLeft" data-wow-delay="100ms" data-wow-duration="2500ms">
                            <div class="about-four__img-box">
                                <div class="about-four__img">
                                    <img :src="asset_path +  'images/about/about-four-img-1.jpg'" alt="">
                                </div>
                                <div class="about-four__img-2">
                                    <img :src="asset_path +  'images/resources/about-four-img-2.jpg'" alt="">
                                </div>
                                <div class="about-four__experience">
                                    <div class="about-four__experience-inner">
                                        <div class="about-four__experience-count-box">
                                            <h3 class="odometer" data-count="25">00</h3>
                                        </div>
                                        <p class="about-four__experience-count-text">{{ trans('Years of') }}
                                            <br> {{ trans('Experience') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="about-four__client-box" v-if="teams && teams.length > 0">
                                <ul class="about-four__client-img-list list-unstyled">
                                    <li v-for="(team, index) in teams.slice(0, 3)" :key="team.id">
                                        <div class="about-four__client-img">
                                            <img :src="team.avatar_link" :alt="translateField(team.name)">
                                        </div>
                                    </li>
                                    <li v-if="teams.length > 3">
                                        <a href="#"><span class="icon-plus"></span></a>
                                    </li>
                                </ul>
                                <p class="about-four__client-text">
                                    <span class="odometer" data-count="120">00</span><span>K</span> {{ trans('Satisfied Client') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="about-four__right">
                            <div class="section-title text-left sec-title-animation animation-style2">
                                <div class="section-title__tagline-box">
                                    <div class="section-title__tagline-shape-1"></div>
                                    <span class="section-title__tagline">{{ trans('About Us') }}</span>
                                    <div class="section-title__tagline-shape-2"></div>
                                </div>
                                <h2 class="section-title__title title-animation">
                                    {{ trans('Supercharge') }} <span>{{ trans('Your Business') }}</span><br>
                                    <span>{{ trans('Growth with Our') }}</span>
                                    {{ trans('Cutting-Edge IT') }}<br> {{ trans('Solutions') }}
                                </h2>
                            </div>
                            <p class="about-four__text">
                                {{ trans('Transform your business with our innovative IT solutions, tailored to address your unique challenges and drive growth in today\'s digital landscape.') }}
                            </p>
                            <div class="about-four__points-box">
                                <ul class="about-four__points-list list-unstyled">
                                    <li>
                                        <div class="icon">
                                            <span class="icon-tick-inside-circle"></span>
                                        </div>
                                        <p>{{ trans('Innovative IT Solutions Expert') }}<br> {{ trans('Support & Consulting') }}</p>
                                    </li>
                                    <li>
                                        <div class="icon">
                                            <span class="icon-tick-inside-circle"></span>
                                        </div>
                                        <p>{{ trans('Cloud Solutions for Modern') }}<br> {{ trans('Enterprises') }}</p>
                                    </li>
                                </ul>
                                <ul class="about-four__points-list about-four__points-list-2 list-unstyled">
                                    <li>
                                        <div class="icon">
                                            <span class="icon-tick-inside-circle"></span>
                                        </div>
                                        <p>{{ trans('Seamless Digital') }}<br> {{ trans('Transformation AI-Driven') }} <br>{{ trans('Business Automation') }}</p>
                                    </li>
                                    <li v-if="teams && teams.length > 0">
                                        <div class="about-four__points-list-img">
                                            <img :src="teams[0].avatar_link" :alt="translateField(teams[0].name)">
                                        </div>
                                        <div class="about-four__points-list-content">
                                            <h5>{{ translateField(teams[0].name) }}</h5>
                                            <p>{{ translateField(teams[0].position) }}</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <ul class="about-four__points-2 list-unstyled">
                                <li>
                                    <div class="icon">
                                        <span class="icon-technical-support"></span>
                                    </div>
                                    <div class="content">
                                        <h5>{{ trans('Innovative IT Solutions') }}</h5>
                                        <p>{{ trans('Stay ahead with cutting-edge technology tailored to your business needs.') }}</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-real-estate-agency"></span>
                                    </div>
                                    <div class="content">
                                        <h5>{{ trans('Cloud Solutions') }}</h5>
                                        <p>{{ trans('Secure, scalable, and efficient cloud services to power your growth.') }}</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--About Four End-->

        <!--Why Choose Three Start-->
        <section class="why-choose-three">
            <div class="why-choose-three__bg-shape float-bob-x"
                :style="{ backgroundImage: `url(${asset_path}images/shapes/why-choose-three-bg-shape.png)` }"></div>
            <div class="why-choose-three__shape-1"></div>
            <div class="why-choose-three__shape-2"></div>
            <div class="container">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <div class="section-title__tagline-shape-1"></div>
                        <span class="section-title__tagline">{{ trans('Why Choose Us') }}</span>
                        <div class="section-title__tagline-shape-2"></div>
                    </div>
                    <h2 class="section-title__title title-animation">
                        {{ trans('Your Business with') }} <span>{{ trans('Reliable &') }}</span><br>
                        <span>{{ trans('Future-Ready') }}</span>
                        {{ trans('IT Solutions') }}
                    </h2>
                </div>
                <div class="row">
                    <div class="col-xl-3 wow fadeInLeft" data-wow-delay="100ms">
                        <div class="why-choose-three__single-left">
                            <div class="why-choose-three__single">
                                <div class="why-choose-three__icon">
                                    <span class="icon-quality"></span>
                                </div>
                                <h3 class="why-choose-three__title">{{ trans('Unmatched Quality') }}</h3>
                                <div class="why-choose-three__bdr"></div>
                                <p class="why-choose-three__text">
                                    {{ trans('We deliver exceptional products and services that exceed expectations every time.') }}
                                </p>
                            </div>
                            <div class="why-choose-three__single">
                                <div class="why-choose-three__icon">
                                    <span class="icon-team"></span>
                                </div>
                                <h3 class="why-choose-three__title">{{ trans('Trusted Expertise') }}</h3>
                                <div class="why-choose-three__bdr"></div>
                                <p class="why-choose-three__text">
                                    {{ trans('Backed by years of experience and a proven track record, we are your reliable partner for success.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 wow fadeInUp" data-wow-delay="200ms">
                        <div class="why-choose-three__img">
                            <img :src="asset_path + 'images/resources/why-choose-three-img.jpg'" alt="">
                        </div>
                    </div>
                    <div class="col-xl-3 wow fadeInRight" data-wow-delay="100ms">
                        <div class="why-choose-three__single-left">
                            <div class="why-choose-three__single">
                                <div class="why-choose-three__icon">
                                    <span class="icon-customer-centricity"></span>
                                </div>
                                <h3 class="why-choose-three__title">{{ trans('User-Centric Approach') }}</h3>
                                <div class="why-choose-three__bdr"></div>
                                <p class="why-choose-three__text">
                                    {{ trans('Your satisfaction is our priority, and we tailor solutions to meet your unique needs. Your happiness comes first.') }}
                                </p>
                            </div>
                            <div class="why-choose-three__single">
                                <div class="why-choose-three__icon">
                                    <span class="icon-support"></span>
                                </div>
                                <h3 class="why-choose-three__title">{{ trans('Trusted by Many') }}</h3>
                                <div class="why-choose-three__bdr"></div>
                                <p class="why-choose-three__text">
                                    {{ trans('We have built a strong reputation over the years by consistently delivering excellent results.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Why Choose Three End-->

        <!-- Team Two Start -->
        <section class="team-two" v-if="teams && teams.length > 0">
            <div class="team-two__bg-shape float-bob-y"
                :style="{ backgroundImage: `url(${asset_path}images/shapes/team-two-bg-shape.png)` }">
            </div>
            <div class="team-two__shape-1"></div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-5">
                        <div class="team-two__left">
                            <div class="section-title text-left sec-title-animation animation-style2">
                                <div class="section-title__tagline-box">
                                    <div class="section-title__tagline-shape-1"></div>
                                    <span class="section-title__tagline">{{ trans('Our Members') }}</span>
                                    <div class="section-title__tagline-shape-2"></div>
                                </div>
                                <h2 class="section-title__title title-animation">
                                    {{ trans('Meet Our Team.') }} <span>{{ trans('Get to') }}</span><br>
                                    <span>{{ trans('Know the Talented') }}</span>
                                    {{ trans('Minds Behind Our Team') }}
                                </h2>
                            </div>
                            <p class="team-two__text">
                                {{ trans('Our dedicated team combines expertise, creativity, and passion to deliver exceptional results and ensure your satisfaction every step of the way.') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-xl-7">
                        <div class="team-two__right">
                            <div class="team-two__carousel owl-theme owl-carousel">
                                <!--Team Two Single Start-->
                                <div class="item" v-for="team in teams" :key="team.id">
                                    <div class="team-two__single">
                                        <div class="team-two__img-box">
                                            <div class="team-two__img">
                                                <img :src="team.avatar_link" :alt="translateField(team.name)">
                                            </div>
                                            <div class="team-two__social">
                                                <a v-if="team.facebook" :href="team.facebook" target="_blank">
                                                    <span class="icon-facebook"></span>
                                                </a>
                                                <a v-if="team.behance" :href="team.behance" target="_blank">
                                                    <span class="icon-dribble"></span>
                                                </a>
                                                <a v-if="team.linked_in" :href="team.linked_in" target="_blank">
                                                    <span class="icon-linkedin"></span>
                                                </a>
                                                <a v-if="team.github" :href="team.github" target="_blank">
                                                    <span class="icon-github"></span>
                                                </a>
                                            </div>
                                            <div class="team-two__title-box">
                                                <h3><a href="#">{{ translateField(team.name) }}</a></h3>
                                                <p>{{ translateField(team.position) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Team Two Single End-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Team Two End -->

        <!-- Process One Start -->
        <section class="process-one">
            <div class="process-one__shape-1">
                <img :src="asset_path + 'images/shapes/process-one-shape-1.png'" alt="">
            </div>
            <div class="process-one__shape-2"></div>
            <div class="process-one__shape-3"></div>
            <div class="process-one__bg-shape"
                :style="{ backgroundImage: `url(${asset_path}images/shapes/process-one-bg-shape.png)` }"></div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-5">
                        <div class="process-one__left">
                            <div class="section-title text-left sec-title-animation animation-style2">
                                <div class="section-title__tagline-box">
                                    <div class="section-title__tagline-shape-1"></div>
                                    <span class="section-title__tagline">{{ trans('Working Process') }}</span>
                                    <div class="section-title__tagline-shape-2"></div>
                                </div>
                                <h2 class="section-title__title title-animation">
                                    {{ trans('How We\'ve') }}
                                    <span>{{ trans('Empowered') }}</span><br>
                                    <span>{{ trans('Businesses with Innovative') }}</span><br>
                                    {{ trans('Tech Solutions') }}
                                </h2>
                            </div>
                            <p class="process-one__text">
                                {{ trans('From personalized solutions to expert execution, we prioritize quality, reliability, and customer satisfaction') }}
                            </p>
                            <div class="process-one__btn-box">
                                <Link :href="route('contact-us')" class="thm-btn" v-if="typeof route !== 'undefined'">
                                    {{ trans('Get in Touch') }}
                                    <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                                </Link>
                                <a :href="`/${locale === 'ar' ? 'ar' : ''}/contact-us`" class="thm-btn" v-else>
                                    {{ trans('Get in Touch') }}
                                    <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7">
                        <div class="process-one__right">
                            <ul class="process-one__process-list list-unstyled">
                                <li>
                                    <div class="process-one__process-count"></div>
                                    <div class="process-one__process-content">
                                        <h3 class="process-one__process-title">{{ trans('Discovery & Strategy') }}</h3>
                                        <p class="process-one__process-text">
                                            {{ trans('We analyze your business needs, identify challenges, and craft a strategic roadmap for the best IT solutions.') }}
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <div class="process-one__process-content">
                                        <h3 class="process-one__process-title">{{ trans('Development') }}</h3>
                                        <p class="process-one__process-text">
                                            {{ trans('Our expert team designs, develops, and integrates cutting-edge technology tailored to your goals.') }}
                                        </p>
                                    </div>
                                    <div class="process-one__process-count"></div>
                                </li>
                                <li>
                                    <div class="process-one__process-count"></div>
                                    <div class="process-one__process-content">
                                        <h3 class="process-one__process-title">{{ trans('Optimization & Support') }}</h3>
                                        <p class="process-one__process-text">
                                            {{ trans('We ensure seamless performance with continuous improvements, maintenance, and dedicated support.') }}
                                        </p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Process One End -->

        <!-- Testimonial Two Start -->
        <section class="testimonial-two" v-if="testimonials && testimonials.length > 0">
            <div class="testimonial-two__shape-1"></div>
            <div class="testimonial-two__shape-2"></div>
            <div class="container">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <div class="section-title__tagline-shape-1"></div>
                        <span class="section-title__tagline">{{ trans('Testimonials') }}</span>
                        <div class="section-title__tagline-shape-2"></div>
                    </div>
                    <h2 class="section-title__title title-animation">
                        {{ trans('Customer Experiences') }} <br> {{ trans('That') }} <span>{{ trans('Speak Volumes') }}</span>
                    </h2>
                </div>
                <div class="testimonial-two__carousel owl-theme owl-carousel">
                    <!-- Testimonial Two Single Start -->
                    <div class="item" v-for="testimonial in testimonials" :key="testimonial.id">
                        <div class="testimonial-two__single">
                            <div class="testimonial-two__single-inner">
                                <div class="testimonial-two__star">
                                    <span class="icon-pointed-star"></span>
                                    <span class="icon-pointed-star"></span>
                                    <span class="icon-pointed-star"></span>
                                    <span class="icon-star"></span>
                                    <span class="icon-star"></span>
                                </div>
                                <p class="testimonial-two__text">
                                    {{ translateField(testimonial.quote) }}
                                </p>
                            </div>
                            <div class="testimonial-two__client-info">
                                <div class="testimonial-two__client-img">
                                    <img :src="testimonial.avatar_link" :alt="translateField(testimonial.name)">
                                </div>
                                <div class="testimonial-two__client-content">
                                    <h4 class="testimonial-two__client-name">
                                        <a href="#">{{ translateField(testimonial.name) }}</a>
                                    </h4>
                                    <p class="testimonial-two__sub-title">
                                        {{ translateField(testimonial.position) }}
                                    </p>
                                </div>
                            </div>
                            <div class="testimonial-two__quote">
                                <span class="icon-right-quote"></span>
                            </div>
                        </div>
                    </div>
                    <!-- Testimonial Two Single End -->
                </div>
            </div>
        </section>
        <!-- Testimonial Two End -->
    </app-layout>
</template>

<script setup>
import {computed, onMounted, nextTick, ref} from 'vue'
import {Link, usePage, Head, useForm} from '@inertiajs/vue3'

const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const seo = computed(() => page.props.seo)
const asset_path = computed(() => page.props.asset_path || '')
const settings = computed(() => page.props.settings)
const storage_path = computed(() => page.props.storage_path)
const locale = computed(() => page.props.locale)
const teams = computed(() => page.props.teams || [])
const testimonials = computed(() => page.props.testimonials || [])

// Newsletter form
const newsletterForm = useForm({
    email: '',
    acceptPrivacy: false
})

const handleNewsletterSubmit = () => {
    if (!newsletterForm.email || !newsletterForm.acceptPrivacy) {
        return false;
    }

    // You can add newsletter subscription logic here
    // For now, just reset the form
    newsletterForm.reset();
    alert(trans('Thank you for subscribing!'));
    return false;
}

const translateField = (value) => {
    if (!value) {
        return '';
    }
    if (typeof value === 'string') {
        return value;
    }
    const loc = locale.value;
    if (typeof value === 'object' && value !== null) {
        if (value[loc]) {
            return value[loc];
        }
    }
    return '';
};

onMounted(() => {
    nextTick(() => {
        // Initialize Team Owl Carousel
        if (typeof $ !== 'undefined' && $('.team-two__carousel').length && teams.value.length > 0) {
            $('.team-two__carousel').owlCarousel({
                loop: teams.value.length > 3,
                margin: 30,
                nav: false,
                dots: true,
                smartSpeed: 500,
                autoplay: true,
                autoplayTimeout: 7000,
                responsive: {
                    0: { items: 1 },
                    768: { items: 2 },
                    992: { items: 2 },
                    1200: { items: 3 }
                }
            });
        }

        // Initialize Testimonials Owl Carousel
        if (typeof $ !== 'undefined' && $('.testimonial-two__carousel').length && testimonials.value.length > 0) {
            $('.testimonial-two__carousel').owlCarousel({
                loop: testimonials.value.length > 1,
                margin: 30,
                nav: false,
                dots: true,
                smartSpeed: 500,
                autoplay: true,
                autoplayTimeout: 7000,
                responsive: {
                    0: { items: 1 },
                    768: { items: 1 },
                    992: { items: 1 },
                    1200: { items: 1 }
                }
            });
        }

        // Initialize WOW animations
        if (typeof WOW !== 'undefined') {
            new WOW().init();
        }

        // Initialize GSAP title animations
        if (typeof gsap !== 'undefined' && typeof SplitText !== 'undefined') {
            const titleAnimations = document.querySelectorAll(".sec-title-animation .title-animation");
            if (titleAnimations.length) {
                titleAnimations.forEach(quote => {
                    let splitParent = new SplitText(quote, { type: "lines" });
                    let split = new SplitText(quote, { type: "lines" });
                    gsap.from(split.lines, {
                        duration: 1,
                        y: 100,
                        opacity: 0,
                        stagger: 0.1,
                        scrollTrigger: {
                            trigger: quote,
                            start: "top 90%",
                            toggleActions: "play none none none"
                        }
                    });
                });
            }
        }

        // Initialize Odometer counters using jQuery approach
        if (typeof $ !== 'undefined' && $.fn.odometer) {
            $('.odometer').each(function() {
                const $this = $(this);
                if (!$this.data('initialized')) {
                    $this.odometer({
                        value: parseInt($this.attr('data-count')) || 0,
                        format: '(,ddd)',
                        theme: 'minimal'
                    });
                    $this.data('initialized', true);
                }
            });
        } else if (typeof Odometer !== 'undefined') {
            // Fallback to vanilla JS Odometer
            const odometerElements = document.querySelectorAll('.odometer');
            odometerElements.forEach((el) => {
                if (!el.dataset.initialized) {
                    const count = parseFloat(el.getAttribute('data-count')) || 0;
                    el.dataset.initialized = '1';
                    const od = new Odometer({
                        el: el,
                        value: 0,
                        format: '(,ddd)',
                        theme: 'minimal'
                    });
                    setTimeout(() => {
                        od.update(count);
                    }, 100);
                }
            });
        }
    });
});

</script>
<script>


import AppLayout from '@/Layouts/App.vue';

export default {
    components: {
        AppLayout
    }

};
</script>

