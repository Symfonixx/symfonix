<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'"/>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/portfolio.css'"/>
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
        <section class="page-header">
            <div class="page-header__bg"
                 :style="{ backgroundImage: `url(${asset_path}images/backgrounds/page-header-bg.jpg)` }">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans('Use Cases') }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li>
                                <Link :href="route('home')">
                                    <i class="fas fa-home"></i>{{ trans('Home') }}
                                </Link>
                            </li>
                            <li>
                                <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span>
                            </li>
                            <li>{{ trans('Use Cases') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="portfolio-page mt-25 pb-90">
            <h2 class="portfolio-one__big-text">use cases</h2>
            <div class="container">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <div class="section-title__tagline-shape-1"></div>
                        <span class="section-title__tagline">{{ trans('Use Cases') }}</span>
                        <div class="section-title__tagline-shape-2"></div>
                    </div>
                    <h2 class="section-title__title title-animation">
                        {{ trans('How We\'ve Empowered Businesses with Innovative Tech Solutions') }}
                    </h2>
                </div>

                <div class="portfolio-one__bottom">
                    <div class="row">
                        <div
                            v-for="(item, index) in useCases.data"
                            :key="item.id"
                            class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp"
                            :data-wow-delay="`${(index % 3 + 1) * 100}ms`"
                        >
                            <div class="portfolio-one__single">
                                <div class="portfolio-one__img-box">
                                    <div class="portfolio-one__img">
                                        <Link :href="route('use-cases.show', item.slug)">
                                            <img :src="item.image_link" :alt="item.title">
                                        </Link>
                                        <div v-if="item.category_tag || (item.technologies && item.technologies.length)" class="portfolio-one__tag">
                                            <span v-if="item.category_tag">{{ item.category_tag }}</span>
                                            <span v-for="tech in (item.technologies || []).slice(0, 2)" :key="tech">{{ tech }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="portfolio-one__content">
                                    <div class="portfolio-one__title-box">
                                        <h3 class="portfolio-one__title">
                                            <Link :href="route('use-cases.show', item.slug)">{{ item.title }}</Link>
                                        </h3>
                                        <p class="portfolio-one__text">{{ item.summary }}</p>
                                    </div>
                                    <div class="portfolio-one__arrow">
                                        <Link :href="route('use-cases.show', item.slug)">
                                            <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                                        </Link>
                                    </div>
                                    <div v-if="item.completed_year" class="portfolio-one__year">
                                        <span>{{ item.completed_year }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="!useCases.data.length" class="text-center py-5">
                        <h3 class="text-muted">{{ trans('No records found') }}</h3>
                    </div>

                    <div v-if="useCases.last_page > 1" class="blog-page__pagination mt-5">
                        <ul class="pg-pagination list-unstyled">
                            <li v-if="useCases.prev_page_url" class="prev">
                                <Link :href="useCases.prev_page_url" aria-label="Previous">
                                    <span class="icon-left-arrow-1"></span>
                                </Link>
                            </li>
                            <template v-for="(link, index) in useCases.links" :key="index">
                                <li v-if="link.url && index > 0 && index < useCases.links.length - 1"
                                    :class="['count', link.active ? 'active' : '']">
                                    <Link :href="link.url">{{ link.label }}</Link>
                                </li>
                            </template>
                            <li v-if="useCases.next_page_url" class="next">
                                <Link :href="useCases.next_page_url" aria-label="Next">
                                    <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <CtaTwo />
    </app-layout>
</template>

<script setup>
import { computed, onMounted, nextTick } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/App.vue'
import CtaTwo from '@/Components/CtaTwo.vue'

const page = usePage()
const trans = (key) => page.props.translations[key] || key
const seo = computed(() => page.props.seo)
const settings = computed(() => page.props.settings || {})
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale || 'en')
const useCases = computed(() => page.props.useCases || { data: [] })
const meta = computed(() => page.props.meta || {})

const metaTitle = computed(() => meta.value.title || `${trans('Use Cases')} | ${seo.value.website_name || ''}`.trim())
const metaDescription = computed(() => meta.value.description || trans('Explore our project use cases and see how we help businesses with innovative technology solutions.') || seo.value.website_desc || '')
const metaKeywords = computed(() => meta.value.keywords || trans('use cases, case studies, IT solutions, web development') || seo.value.website_keywords || '')
const metaImage = computed(() => meta.value?.og?.image || meta.value?.twitter?.image || settings.value?.meta_img || '')
const metaCanonical = computed(() => meta.value.canonical || '')
const metaRobots = computed(() => meta.value.robots || 'index, follow')

onMounted(() => {
    nextTick(() => {
        if (typeof WOW !== 'undefined') {
            new WOW().init()
        }
    })
})
</script>

<script>
export default {
    components: {
        AppLayout,
        CtaTwo,
    },
}
</script>
