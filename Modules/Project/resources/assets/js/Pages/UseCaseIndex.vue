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
        <div class="page-header">
            <div class="page-header__bg"
                 :style="{ backgroundImage: `url(${asset_path}images/backgrounds/our-team-bg.jpg)` }">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h1>{{ trans('Case Studies') }}</h1>
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
                            <li>{{ trans('Case Studies') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <section class="blog-page use-cases-page">
            <div class="use-cases-page__bg" aria-hidden="true">
                <div class="use-cases-page__orb use-cases-page__orb--one"></div>
                <div class="use-cases-page__orb use-cases-page__orb--two"></div>
                <div class="use-cases-page__orb use-cases-page__orb--three"></div>
            </div>

            <div class="container position-relative">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <div class="section-title__tagline-shape-1"></div>
                        <span class="section-title__tagline">{{ trans('Case Studies') }}</span>
                        <div class="section-title__tagline-shape-2"></div>
                    </div>
                    <h2 class="section-title__title title-animation">
                        {{ trans('How We\'ve Empowered Businesses with Innovative Tech Solutions') }}
                    </h2>
                </div>

                <div class="row">
                    <div
                        v-for="(item, index) in useCases.data"
                        :key="item.id"
                        class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp"
                        :data-wow-delay="`${(index % 3 + 1) * 100}ms`"
                    >
                        <UseCaseCard :item="item" :locale="locale" />
                    </div>

                    <div v-if="!useCases.data.length" class="col-12">
                        <div class="text-center py-5">
                            <h3 class="use-cases-page__empty">{{ trans('No records found') }}</h3>
                        </div>
                    </div>

                    <div v-if="useCases.last_page > 1" class="blog-page__pagination">
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

const metaTitle = computed(() => meta.value.title || `${trans('Case Studies')} | ${seo.value.website_name || ''}`.trim())
const metaDescription = computed(() => meta.value.description || trans('Explore our case studies and see how we help businesses with innovative technology solutions.') || seo.value.website_desc || '')
const metaKeywords = computed(() => meta.value.keywords || trans('case studies, project solutions, IT solutions, web development') || seo.value.website_keywords || '')
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
import AppLayout from '@/Layouts/App.vue'
import CtaTwo from '@/Components/CtaTwo.vue'
import UseCaseCard from '@/Components/UseCaseCard.vue'

export default {
    components: {
        AppLayout,
        CtaTwo,
        UseCaseCard,
    },
}
</script>

<style scoped>
.use-cases-page {
    position: relative;
    overflow: hidden;
}

.use-cases-page__bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        145deg,
        #0b192c 0%,
        #0f2844 35%,
        #155a8a 65%,
        rgba(33, 137, 202, 0.55) 85%,
        rgba(127, 196, 87, 0.2) 100%
    );
    pointer-events: none;
}

.use-cases-page__orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.45;
}

.use-cases-page__orb--one {
    width: 420px;
    height: 420px;
    top: -120px;
    left: -80px;
    background: #2189ca;
}

.use-cases-page__orb--two {
    width: 360px;
    height: 360px;
    top: 40%;
    right: -100px;
    background: #7fc457;
}

.use-cases-page__orb--three {
    width: 280px;
    height: 280px;
    bottom: -60px;
    left: 35%;
    background: #1a5f8a;
}

.blog-page.use-cases-page :deep(.use-case-card) {
    margin-bottom: 30px;
}

.use-cases-page__empty {
    color: rgba(255, 255, 255, 0.7);
}
</style>
