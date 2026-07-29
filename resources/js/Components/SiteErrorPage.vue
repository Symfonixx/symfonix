<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'" />
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/error.css'" />
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
            <div
                class="page-header__bg"
                :style="{ backgroundImage: `url(${asset_path}images/contact-header-bg.jpg)` }"
            ></div>
            <div class="container">
                <div class="page-header__inner">
                    <h1>{{ title }}</h1>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li>
                                <Link :href="homeUrl">
                                    <i class="fas fa-home"></i>{{ trans('Home') }}
                                </Link>
                            </li>
                            <li>
                                <span :class="`icon-${isRtl ? 'left' : 'right'}-arrow-1`"></span>
                            </li>
                            <li>{{ title }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="error-page">
            <div class="container">
                <div class="error-page__inner text-center">
                    <div v-if="showImage" class="error-page__img float-bob-y">
                        <img
                            :src="asset_path + 'site/images/resources/error-page-img1.png'"
                            :alt="title"
                            width="620"
                            height="420"
                            decoding="async"
                        >
                    </div>
                    <div v-else class="error-page__code float-bob-y" aria-hidden="true">
                        {{ status }}
                    </div>

                    <div class="error-page__content">
                        <h2>{{ heading }}</h2>
                        <p>{{ message }}</p>

                        <div
                            v-if="showDebug && (page?.props?.error || page?.props?.trace)"
                            class="error-page__debug alert alert-danger text-start"
                        >
                            <strong>Debug Error:</strong>
                            <div v-if="page?.props?.error">{{ page.props.error }}</div>
                            <details v-if="page?.props?.trace" class="mt-3">
                                <summary>Stack trace</summary>
                                <pre class="mt-2">{{ page.props.trace }}</pre>
                            </details>
                        </div>

                        <div class="btn-box">
                            <Link
                                v-if="secondaryHref"
                                class="thm-btn error-page__btn-secondary"
                                :href="secondaryHref"
                            >
                                {{ secondaryLabel }}
                                <span :class="`icon-${isRtl ? 'left' : 'right'}-arrow`"></span>
                            </Link>
                            <Link class="thm-btn" :href="homeUrl">
                                {{ trans('Back To Home') }}
                                <span :class="`icon-${isRtl ? 'left' : 'right'}-arrow`"></span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </app-layout>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, Link, Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/App.vue'

const props = defineProps({
    status: { type: [Number, String], required: true },
    title: { type: String, required: true },
    heading: { type: String, required: true },
    message: { type: String, required: true },
    description: { type: String, default: '' },
    keywords: { type: String, default: '' },
    showImage: { type: Boolean, default: false },
    showDebug: { type: Boolean, default: false },
    secondaryHref: { type: String, default: '' },
    secondaryLabel: { type: String, default: '' },
})

const page = usePage()
const trans = (key) => {
    try {
        return page.props.translations?.[key] || key
    } catch (e) {
        return key
    }
}

const asset_path = computed(() => page.props.asset_path || '/')
const locale = computed(() => page.props.locale || 'en')
const isRtl = computed(() => locale.value === 'ar')
const seo = computed(() => page.props.seo || {})
const meta = computed(() => page.props.meta || {})
const siteName = computed(() => seo.value.website_name || page.props.appName || 'Symfonix')

const metaTitle = computed(() => `${props.title} | ${siteName.value}`)
const metaDescription = computed(() => meta.value.description || props.description || props.message)
const metaKeywords = computed(() => meta.value.keywords || props.keywords)
const metaImage = computed(() => meta.value?.og?.image || meta.value?.twitter?.image || '')
const metaCanonical = computed(() => meta.value.canonical || '')
const metaRobots = computed(() => meta.value.robots || 'noindex, nofollow')

const homeUrl = computed(() => {
    try {
        return route('home')
    } catch (e) {
        return `/${locale.value}`
    }
})
</script>

<script>
export default {
    components: {
        AppLayout,
    },
}
</script>
