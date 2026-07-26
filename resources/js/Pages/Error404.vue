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
        <!--Page Header Start-->
        <section class="page-header">
            <div
                class="page-header__bg"
                :style="{ backgroundImage: `url(${asset_path}images/contact-header-bg.jpg)` }"
            ></div>
            <div class="container">
                <div class="page-header__inner">
                    <h1>{{ trans('404 Error') }}</h1>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li>
                                <Link :href="getHomeUrl()">
                                    <i class="fas fa-home"></i>{{ trans('Home') }}
                                </Link>
                            </li>
                            <li>
                                <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span>
                            </li>
                            <li>{{ trans('404 Error') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!--Page Header End-->

        <!--Start Error Page-->
        <section class="error-page">
            <div class="container">
                <div class="error-page__inner text-center">
                    <div class="error-page__img float-bob-y">
                        <img
                            :src="asset_path + 'site/images/resources/error-page-img1.png'"
                            :alt="trans('404 Error')"
                            width="620"
                            height="420"
                            decoding="async"
                        >
                    </div>

                    <div class="error-page__content">
                        <h2>{{ trans('Oops! Page Not Found!') }}</h2>
                        <p>
                            {{ trans('The page you are looking for does not exist. It might have been moved or deleted.') }}
                        </p>
                        <div class="btn-box">
                            <Link class="thm-btn" :href="getHomeUrl()">
                                {{ trans('Back To Home') }}
                                <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--End Error Page-->
    </app-layout>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, Link, Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/App.vue'

const page = usePage()
const trans = (key) => {
    try {
        return page.props.translations?.[key] || key
    } catch (e) {
        return key
    }
}

const seo = computed(() => page.props.seo || {})
const asset_path = computed(() => page.props.asset_path || '/')
const locale = computed(() => page.props.locale || 'en')
const meta = computed(() => page.props.meta || {})
const siteName = computed(() => seo.value.website_name || page.props.appName || 'Symfonix')

const metaTitle = computed(() => `${trans('404 Error')} | ${siteName.value}`)
const metaDescription = computed(() => {
    return meta.value.description || trans('The page you are looking for could not be found.')
})
const metaKeywords = computed(() => {
    return meta.value.keywords || trans('404 error, page not found, missing page')
})
const metaImage = computed(() => {
    return meta.value?.og?.image || meta.value?.twitter?.image || ''
})
const metaCanonical = computed(() => meta.value.canonical || '')
const metaRobots = computed(() => meta.value.robots || 'noindex, nofollow')

const getHomeUrl = () => {
    try {
        return route('home')
    } catch (e) {
        return locale.value === 'en' ? '/' : `/${locale.value}`
    }
}
</script>

<script>
export default {
    components: {
        AppLayout,
    },
}
</script>
