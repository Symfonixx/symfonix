<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'" />
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
                :style="{ backgroundImage: `url(${asset_path}images/contact-header-bg.jpg)` }">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans('Testimonials') }}</h2>
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
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li>{{ trans('Testimonials') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!--Page Header End-->

        <!--Testimonials Page Start-->
        <section class="testimonials-page">
            <div class="container">
                <div class="row" v-if="testimonials && testimonials.length > 0">
                    <!-- Testimonial Two Single Start -->
                    <div class="col-xl-4 col-lg-6 col-md-6" v-for="testimonial in testimonials"
                        :key="testimonial.id">
                        <div class="testimonial-two__single">
                            <div class="testimonial-two__single-inner">
                                <div class="testimonial-two__star">
                                    <span class="icon-pointed-star"></span>
                                    <span class="icon-pointed-star"></span>
                                    <span class="icon-pointed-star"></span>
                                    <span class="icon-pointed-star"></span>
                                    <span class="icon-pointed-star"></span>
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
                <div v-else class="text-center py-5">
                    <p>{{ trans('No testimonials found.') }}</p>
                </div>
            </div>
        </section>
        <!--Testimonials Page End-->
    </app-layout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage, Head } from '@inertiajs/vue3'


const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const seo = computed(() => page.props.seo)
const settings = computed(() => page.props.settings || {})
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale)
const testimonials = computed(() => page.props.testimonials || [])
const meta = computed(() => page.props.meta || {})

const metaTitle = computed(() => {
    return  `${trans('Testimonials')} | ${seo.value.website_name || ''}`.trim()
})
const metaDescription = computed(() => {
    return meta.value.description
        || trans('Read what our clients say about working with our team.')
        || seo.value.website_desc
        || ''
})
const metaKeywords = computed(() => {
    return meta.value.keywords
        || trans('testimonials, reviews, client feedback, success stories')
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
</script>

<script>
import AppLayout from '@/Layouts/App.vue';

export default {
    components: {
        AppLayout
    }
};
</script>
