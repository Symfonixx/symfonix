<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'"/>

        <title>{{ metaTitle }}</title>
        <meta name="description" :content="metaDescription">
        <meta name="keywords" :content="metaKeywords">
        <meta name="robots" :content="metaRobots">
        <link v-if="metaCanonical" rel="canonical" :href="metaCanonical">
        <meta property="og:title" :content="metaTitle">
        <meta property="og:description" :content="metaDescription">
        <meta v-if="metaImage" property="og:image" :content="metaImage">
        <meta v-if="metaCanonical" property="og:url" :content="metaCanonical">
        <meta property="og:type" content="website">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" :content="metaTitle">
        <meta name="twitter:description" :content="metaDescription">
        <meta v-if="metaImage" name="twitter:image" :content="metaImage">
    </Head>
    <app-layout>

        <!--Page Header Start-->
        <section class="page-header">
            <div class="page-header__bg"
                 :style="{ backgroundImage: `url(${asset_path}images/backgrounds/about-us-bg.jpg)` }">
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
                            <li><span :class="`icon-${isRtl ? 'left' : 'right'}-arrow-1`"></span></li>
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

            <div class="container">
                <div class="row">
                    <div class="col-xl-6">
                        <div :class="`about-four__left wow slideIn${isRtl ? 'Right' : 'Left'}`"
                             data-wow-delay="100ms" data-wow-duration="2500ms">
                            <div class="about-four__img-box">
                                <div class="about-four__img">
                                    <img :src="asset_path +  'images/about/about-four-img-1.jpg'"
                                         :alt="trans('About our company')">
                                </div>

                                <div class="about-four__img-2">
                                    <img :src="asset_path +  'images/about/about-four-img-12.jpg'"   :alt="trans('About our company')">
                                </div>
                                <div class="about-four__experience">
                                    <div class="about-four__experience-inner">

                                        <p class="about-four__experience-count-text">10 {{ trans('Years of') }}
                                            <br> {{ trans('Experience') }}</p>
                                    </div>
                                </div>
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
                                <h2 class="section-title__title title-animation" v-if="!isRtl">
                                    {{ trans('Supercharge') }} <span>{{ trans('Your Business') }}</span><br>
                                    <span>{{ trans('Growth with Our') }}</span>
                                    {{ trans('Cutting-Edge IT') }}<br> {{ trans('Solutions') }}
                                </h2>
                            </div>
                            <p class="about-four__text">
                                {{
                                    trans('Symfonix is a technology company that designs, builds, and scales digital systems where web, mobile, AI, and cloud work together instead of fighting each other. The name says it all: a symphony of technologies, orchestrated with intention.')
                                }}
                            </p>

                            <h4 class="about-four__text mt-4">
                                {{ trans('Core Values (This is non-negotiable stuff)') }}
                            </h4>
                            <ul class="about-four__points-2 list-unstyled about-four__points-box">
                                <li>
                                    <div class="icon">
                                        <span class="icon-technical-support"></span>
                                    </div>
                                    <div class="content">
                                        <h5>{{ trans('Harmony over chaos') }}</h5>
                                        <p>{{
                                                trans('Every solution must be coherent. No messy stacks, no duct-tape architectures.')
                                            }}</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-quality"></span>
                                    </div>
                                    <div class="content">
                                        <h5>{{ trans('Engineering first') }}</h5>
                                        <p>{{
                                                trans('Pretty UI is great, but solid architecture, performance, and maintainability come first.')
                                            }}</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-customer-centricity"></span>
                                    </div>
                                    <div class="content">
                                        <h5>{{ trans('Truth & clarity') }}</h5>
                                        <p>{{
                                                trans("We say what's possible, what's risky, and what's unnecessary. No tech theater.")
                                            }}</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-support"></span>
                                    </div>
                                    <div class="content">
                                        <h5>{{ trans('Continuous learning') }}</h5>
                                        <p>{{
                                                trans('AI, cloud, and software evolve fast. We evolve faster.')
                                            }}</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-real-estate-agency"></span>
                                    </div>
                                    <div class="content">
                                        <h5>{{ trans('Global mindset, local roots') }}</h5>
                                        <p>{{
                                                trans('Built in Syria. Designed for the world.')
                                            }}</p>
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
            <div class="container">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <div class="section-title__tagline-shape-1"></div>
                        <span class="section-title__tagline">{{ trans('Why Choose Us') }}</span>
                        <div class="section-title__tagline-shape-2"></div>
                    </div>
                    <h2 class="section-title__title title-animation" v-if="!isRtl">
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
                                    {{
                                        trans('We deliver exceptional products and services that exceed expectations every time.')
                                    }}
                                </p>
                            </div>
                            <div class="why-choose-three__single">
                                <div class="why-choose-three__icon">
                                    <span class="icon-team"></span>
                                </div>
                                <h3 class="why-choose-three__title">{{ trans('Trusted Expertise') }}</h3>
                                <div class="why-choose-three__bdr"></div>
                                <p class="why-choose-three__text">
                                    {{
                                        trans('Backed by years of experience and a proven track record, we are your reliable partner for success.')
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 wow fadeInUp" data-wow-delay="200ms">
                        <div class="why-choose-three__img">
                            <img :src="asset_path +  'images/about/tech-concept.jpg'"
                                 :alt="trans('Technology concept')">
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
                                    {{
                                        trans('Your satisfaction is our priority, and we tailor solutions to meet your unique needs. Your happiness comes first.')
                                    }}
                                </p>
                            </div>
                            <div class="why-choose-three__single">
                                <div class="why-choose-three__icon">
                                    <span class="icon-support"></span>
                                </div>
                                <h3 class="why-choose-three__title">{{ trans('Trusted by Many') }}</h3>
                                <div class="why-choose-three__bdr"></div>
                                <p class="why-choose-three__text">
                                    {{
                                        trans('We have built a strong reputation over the years by consistently delivering excellent results.')
                                    }}
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
                                {{
                                    trans('Our dedicated team combines expertise, creativity, and passion to deliver exceptional results and ensure your satisfaction every step of the way.')
                                }}
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
                <img :src="asset_path + 'images/shapes/process-one-shape-1.png'" :alt="trans('Process illustration')">
            </div>

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
                                {{
                                    trans('From personalized solutions to expert execution, we prioritize quality, reliability, and customer satisfaction')
                                }}
                            </p>
                            <div class="process-one__btn-box">
                                <Link :href="route('contact-us')" class="thm-btn" v-if="typeof route !== 'undefined'">
                                    {{ trans('Get in Touch') }}
                                    <span :class="`icon-${isRtl ? 'left' : 'right'}-arrow`"></span>
                                </Link>
                                <a :href="`/${locale === 'ar' ? 'ar' : ''}/contact-us`" class="thm-btn" v-else>
                                    {{ trans('Get in Touch') }}
                                    <span :class="`icon-${isRtl ? 'left' : 'right'}-arrow`"></span>
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
                                            {{
                                                trans('We analyze your business needs, identify challenges, and craft a strategic roadmap for the best IT solutions.')
                                            }}
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <div class="process-one__process-content">
                                        <h3 class="process-one__process-title">{{ trans('Development') }}</h3>
                                        <p class="process-one__process-text">
                                            {{
                                                trans('Our expert team designs, develops, and integrates cutting-edge technology tailored to your goals.')
                                            }}
                                        </p>
                                    </div>
                                    <div class="process-one__process-count"></div>
                                </li>
                                <li>
                                    <div class="process-one__process-count"></div>
                                    <div class="process-one__process-content">
                                        <h3 class="process-one__process-title">{{
                                                trans('Optimization & Support')
                                            }}</h3>
                                        <p class="process-one__process-text">
                                            {{
                                                trans('We ensure seamless performance with continuous improvements, maintenance, and dedicated support.')
                                            }}
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
     <!--CTA One Start -->
        <section class="cta-one">
            <div class="cta-one__shape-bg float-bob-y"
                 :style="{ backgroundImage: `url(${asset_path}site/images/shapes/cta-one-shape-bg.png)` }">
            </div>
            <div class="container">
                <div class="cta-one__inner">
                    <h3 class="cta-one__title">
                        {{ trans('To make requests for further information, contact us') }}
                    </h3>
                    <div class="cta-one__contact-info">
                        <div class="cta-one__contact-icon">
                            <span class="icon-customer-service-headset"></span>
                        </div>
                        <div class="cta-one__contact-details">
                            <p>{{ trans('Call Us For Any inquiry') }}</p>
                            <h4><a dir="ltr" href="tel:{{settings.phone}}">{{settings.phone}}</a></h4>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--CTA One End -->
        <!-- Testimonial One Start -->
        <section class="testimonial-one pb-3" v-if="testimonials && testimonials.length > 0">

            <div class="testimonial-one__shape-2 float-bob-y">
                <img :src="asset_path + 'images/shapes/testimonial-one-shape-2.png'" :alt="trans('Decorative shape')">
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-3"></div>
                    <div class="col-xl-9">
                        <div class="testimonial-one__content-box">
                            <div class="section-title text-left sec-title-animation animation-style2">
                                <div class="section-title__tagline-box">
                                    <div class="section-title__tagline-shape-1"></div>
                                    <span class="section-title__tagline">{{ trans('Testimonials') }}</span>
                                    <div class="section-title__tagline-shape-2"></div>
                                </div>
                                <h2 class="section-title__title title-animation">
                                    {{ trans('What Our Clients Say') }}
                                </h2>
                            </div>
                            <div class="testimonial-one__carousel owl-theme owl-carousel">
                                <!-- Testimonial One Single Start -->
                                <div class="item" v-for="testimonial in testimonials" :key="testimonial.id">
                                    <div class="testimonial-one__single">
                                        <div class="testimonial-one__img-box">
                                            <div class="testimonial-one__img">
                                                <img :src="testimonial.avatar_link" :alt="translateField(testimonial.name)">
                                            </div>
                                        </div>
                                        <div class="testimonial-one__content">

                                            <p class="testimonial-one__text">
                                                “{{ translateField(testimonial.quote) }}”
                                            </p>
                                            <div class="testimonial-one__bottom">
                                                <div class="testimonial-one__quote-and-client-info">
                                                    <div class="testimonial-one__quote">
                                                        <span class="icon-left"></span>
                                                    </div>
                                                    <div class="testimonial-one__client-info">
                                                        <p class="testimonial-one__client-sub-title">
                                                            {{ translateField(testimonial.position) }}
                                                        </p>
                                                        <h3 class="testimonial-one__client-name">
                                                            <a href="#">{{ translateField(testimonial.name) }}</a>
                                                        </h3>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Testimonial One Single End -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Testimonial One End -->
    </app-layout>
</template>

<script setup>
import {computed, onMounted, nextTick} from 'vue'
import {Link, usePage, Head} from '@inertiajs/vue3'

const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const seo = computed(() => page.props.seo)
const settings = computed(() => page.props.settings || {})
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale || 'en')
const isRtl = computed(() => locale.value === 'ar')
const teams = computed(() => page.props.teams || [])
const testimonials = computed(() => page.props.testimonials || [])
const meta = computed(() => page.props.meta || {})

const metaTitle = computed(() => {
    return meta.value.title || `${trans('About Us')} | ${seo.value.website_name || ''}`.trim()
})
const metaDescription = computed(() => {
    return meta.value.description
        || trans('Learn about our team, mission, and the technology expertise behind our solutions.')
        || seo.value.website_desc
        || ''
})
const metaKeywords = computed(() => {
    return meta.value.keywords
        || trans('about us, IT consulting, technology experts, digital transformation')
        || seo.value.website_keywords
        || ''
})
const metaImage = computed(() => {
    return meta.value?.og?.image || meta.value?.twitter?.image || settings.value?.meta_img || ''
})
const metaCanonical = computed(() => meta.value.canonical || '')
const metaRobots = computed(() => meta.value.robots || 'index, follow')

const translateField = (value) => {
    if (!value) {
        return ''
    }
    if (typeof value === 'string') {
        return value
    }
    if (typeof value === 'object' && value !== null) {
        return value[locale.value] || value.en || value[Object.keys(value)[0]] || ''
    }
    return ''
}

onMounted(() => {
    nextTick(() => {
        const applyOwlDotAriaLabels = ($carousel) => {
            if (!$carousel || !$carousel.length) {
                return;
            }

            $carousel.find('.owl-dot').each(function(index) {
                $(this).attr('aria-label', `${trans('Go to slide')} ${index + 1}`);
            });
        };

        // Initialize Team Owl Carousel
        if (typeof $ !== 'undefined' && $('.team-two__carousel').length && teams.value.length > 0) {
            const $teamCarousel = $('.team-two__carousel');
            $teamCarousel.owlCarousel({
                loop: teams.value.length > 3,
                margin: 30,
                nav: false,
                dots: true,
                smartSpeed: 500,
                autoplay: true,
                autoplayTimeout: 7000,
                rtl: isRtl.value,
                responsive: {
                    0: {items: 1},
                    768: {items: 2},
                    992: {items: 2},
                    1200: {items: 3}
                }
            }).on('initialized.owl.carousel refreshed.owl.carousel', function() {
                applyOwlDotAriaLabels($(this));
            });
            applyOwlDotAriaLabels($teamCarousel);
        }

        // Initialize Testimonials Owl Carousel
        if (typeof $ !== 'undefined' && $('.testimonial-one__carousel').length && testimonials.value.length > 0) {
            const $testimonialCarousel = $('.testimonial-one__carousel');
            $testimonialCarousel.owlCarousel({
                loop: testimonials.value.length > 1,
                margin: 30,
                nav: false,
                dots: true,
                smartSpeed: 500,
                autoplay: true,
                autoplayTimeout: 7000,
                rtl: isRtl.value,
                responsive: {
                    0: {items: 1},
                    768: {items: 1},
                    992: {items: 1},
                    1200: {items: 1}
                }
            }).on('initialized.owl.carousel refreshed.owl.carousel', function() {
                applyOwlDotAriaLabels($(this));
            });
            applyOwlDotAriaLabels($testimonialCarousel);
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
                    let split = new SplitText(quote, {type: "lines"});
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

