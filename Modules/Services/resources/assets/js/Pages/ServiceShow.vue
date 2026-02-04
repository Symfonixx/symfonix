<template>
    <Head>
        <title>{{service.title}}</title>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'"/>
    </Head>
    <app-layout>
        <div class="page-header">
            <div class="page-header__bg"
                :style="{ backgroundImage: `url(${asset_path}images/backgrounds/services-bg.jpg)`}">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ getServiceTitle(service) }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li>
                                <Link :href="route('home')">
                                    <i class="fas fa-home"></i>{{ trans("Home") }}
                                </Link>
                            </li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li>
                                <Link :href="route('services.index')">{{ trans("Our Services") }}</Link>
                            </li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li>{{ getServiceTitle(service) }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Details Start -->
        <section class="services-details">

            <div class="container">
                <div class="row">
                    <div class="col-xl-4 col-lg-5">
                        <div class="services-details__left">
                            <div class="services-details__services-list-box">
                                <h3 class="services-details__services-list-title">{{ trans("Service Categories") }}</h3>
                                <ul class="services-details__services-list list-unstyled">
                                    <li v-for="recentService in recentServices.slice(0, 6)" :key="recentService.id">
                                        <Link
                                            :href="getServiceUrl(recentService)"
                                            :class="{ 'active': recentService.id === service.id }"
                                        >
                                            {{ getServiceTitle(recentService) }}
                                            <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-2`"></span>
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                            <div class="services-details__need-help">
                                <div class="services-details__need-help-img">
                                    <img :src="asset_path + 'images/need_help.jpg'"
                                         :alt="trans('Need help')">
                                    <div class="services-details__need-help-content">
                                        <div class="services-details__need-help-bdr"></div>
                                        <h3 class="services-details__need-help-title">{{ trans("Need Help?") }}</h3>
                                        <p class="services-details__need-help-number">
                                            <a dir="ltr" :href="`tel:${settings.website_phone || settings.phone}`">
                                                {{ settings.website_phone || settings.phone || '+12 (00) 345 789034' }}
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-7">
                        <div class="services-details__right">

                                <h3 class="services-details__title-1">{{ getServiceTitle(service) }}</h3>
                            <div class="services-details__bdr"></div>
                                  <ul class="blog-details__meta list-unstyled" v-if="service.reading_time">
                                    <li>
                                        <Link>
                                            <span class="far fa-clock"></span>{{ service.reading_time }}
                                            {{ trans('min read') }}
                                        </Link>
                                    </li>
                                </ul>
                            <div class="services-details__img-1 my-3" v-if="service.image_link">
                                <img :src="service.image_link" :alt="getServiceTitle(service)">
                            </div>

                            <div class="services-details__text-1" style="    line-height: 27px !important;">
                                <div v-html="service.content"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Service Details End -->

        <section v-if="relatedServices.length" class="services-carousel-page services-related mb-5">
            <div class="container">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <div class="section-title__tagline-shape-1"></div>
                        <span class="section-title__tagline">{{ trans("Related Services") }}</span>
                        <div class="section-title__tagline-shape-2"></div>
                    </div>
                    <h2 class="section-title__title title-animation">
                        {{ trans("Explore More") }} <span>{{ trans("Services") }}</span>
                    </h2>
                </div>
                <div class="row">
                    <div v-for="relatedService in relatedServices" :key="relatedService.id"
                         class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms">
                        <ServiceCardThree
                            :title="getServiceTitle(relatedService)"
                            :description="getServiceDescription(relatedService)"
                            :highlights="getServiceHighlights(relatedService)"
                            :link="getServiceUrl(relatedService)"
                            :image="relatedService.image_link"
                            :button-label="trans('Read More')"
                            :is-rtl="locale === 'ar'"
                            :reading-time="relatedService.reading_time"
                            :reading-time-label="trans('min read')"
                        />
                    </div>
                </div>
            </div>
        </section>


        <!-- Testimonial One Start -->
        <section class="testimonial-one" v-if="testimonials && testimonials.length">

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
import {usePage, Link, Head} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/App.vue'
import ServiceCardThree from '@/Components/Services/ServiceCardThree.vue'

const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const seo = computed(() => page.props.seo)
const settings = computed(() => page.props.settings || {})
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale || 'en')
const service = computed(() => page.props.service)
const relatedServices = computed(() => page.props.relatedServices || [])
const recentServices = computed(() => page.props.recentServices || [])
const testimonials = computed(() => page.props.testimonials || [])
const meta = computed(() => page.props.meta || {})

const metaTitle = computed(() => {
    return meta.value.title || `${getServiceTitle(service.value)} | ${seo.value.website_name || ''}`.trim()
})
const metaDescription = computed(() => {
    return meta.value.description || getServiceDescription(service.value) || seo.value.website_desc || ''
})
const metaKeywords = computed(() => {
    return meta.value.keywords || service.value?.keywords || seo.value.website_keywords || ''
})
const metaImage = computed(() => {
    return meta.value?.og?.image || meta.value?.twitter?.image || service.value?.image_link || settings.value?.meta_img || ''
})
const metaCanonical = computed(() => meta.value.canonical || '')
const metaRobots = computed(() => meta.value.robots || 'index, follow')

const translateField = (value) => {
    if (!value) return ''
    if (typeof value === 'string') return value
    if (typeof value === 'object' && value !== null) {
        return value[locale.value] || value['en'] || value[Object.keys(value)[0]] || ''
    }
    return ''
}

// Helper function to safely get service URL
const getServiceUrl = (serviceItem) => {
    if (!serviceItem || !serviceItem.slug) {
        return '#'
    }
    try {
        return route('services.show', serviceItem.slug)
    } catch (e) {
        return '#'
    }
}

// Helpers to safely handle translatable title/description fields
const getServiceTitle = (serviceItem) => {
    return translateField(serviceItem?.title)
}

const getServiceDescription = (serviceItem) => {
    return translateField(serviceItem?.description)
}

const normalizeKeywords = (rawKeywords) => {
    if (!rawKeywords) {
        return []
    }

    let parsed = rawKeywords
    if (typeof rawKeywords === 'string') {
        const trimmed = rawKeywords.trim()
        if (trimmed.startsWith('{') || trimmed.startsWith('[')) {
            try {
                parsed = JSON.parse(trimmed)
            } catch (e) {
                try {
                    parsed = JSON.parse(trimmed.replace(/'/g, '"'))
                } catch (err) {
                    parsed = rawKeywords
                }
            }
        }
    }

    if (Array.isArray(parsed)) {
        return parsed
            .map((item) => {
                if (typeof item === 'string') {
                    return item
                }
                if (item && typeof item === 'object') {
                    if (item.value) {
                        return translateField(item.value)
                    }
                    return translateField(item)
                }
                return ''
            })
            .map((item) => String(item).trim())
            .filter(Boolean)
    }

    if (typeof parsed === 'object') {
        const value = translateField(parsed)
        return value ? [value] : []
    }

    return parsed
        .toString()
        .split(/[,;\n]+/)
        .map(item => item.trim())
        .filter(Boolean)
}

const getServiceHighlights = (serviceItem) => {
    const keywords = normalizeKeywords(translateField(serviceItem?.keywords))
    if (keywords.length) {
        return keywords
    }

    return [
        trans("Web Development"),
        trans("App Development"),
        trans("Graphics Design"),
        trans("Performance Audits"),
        trans("Customer Insights"),
        trans("Continuous Improvement"),
    ]
}

onMounted(() => {
    nextTick(() => {
        // Initialize Testimonials Owl Carousel
        if (typeof $ !== 'undefined' && $('.testimonial-one__carousel').length && testimonials.value.length) {
            $('.testimonial-one__carousel').owlCarousel({
                loop: testimonials.value.length > 1,
                margin: 30,
                nav: false,
                dots: true,
                smartSpeed: 500,
                autoplay: true,
                autoplayTimeout: 7000,
                rtl: locale.value === 'ar',
                responsive: {
                    0: {items: 1},
                    768: {items: 1},
                    992: {items: 1},
                    1200: {items: 1}
                }
            })
        }

        // Initialize WOW animations
        if (typeof WOW !== 'undefined') {
            new WOW().init()
        }

        // Initialize GSAP title animations
        if (typeof gsap !== 'undefined' && typeof SplitText !== 'undefined') {
            const titleAnimations = document.querySelectorAll(".sec-title-animation .title-animation")
            if (titleAnimations.length) {
                titleAnimations.forEach(quote => {
                    let splitParent = new SplitText(quote, {type: "lines"})
                    let split = new SplitText(quote, {type: "lines"})
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
                    })
                })
            }
        }
    })
})
</script>

<script>
export default {
    components: {
        AppLayout
    }
};
</script>

