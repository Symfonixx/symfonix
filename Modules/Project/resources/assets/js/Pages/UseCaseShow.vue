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
        <meta property="og:type" content="article">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" :content="metaTitle">
        <meta name="twitter:description" :content="metaDescription">
        <meta v-if="metaImage" name="twitter:image" :content="metaImage">
    </Head>

    <app-layout>
        <section class="page-header">
            <div class="page-header__bg"
                 :style="{ backgroundImage: `url(${useCase.image_link})` }">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ useCase.title }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li>
                                <Link :href="route('home')">
                                    <i class="fas fa-home"></i>{{ trans('Home') }}
                                </Link>
                            </li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li>
                                <Link :href="route('use-cases.index')">{{ trans('Use Cases') }}</Link>
                            </li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li>{{ useCase.title }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="portfolio-details pb-90">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="portfolio-details__top">
                            <div class="portfolio-details__title-and-social">
                                <h3 class="portfolio-details__top-title">{{ useCase.title }}</h3>
                                <div class="portfolio-details__social-box">
                                    <p>{{ trans('Share now') }}</p>
                                    <div class="portfolio-details__social">
                                        <a :href="getShareUrl('facebook')" target="_blank" rel="noopener">
                                            <span class="icon-facebook"></span>
                                        </a>
                                        <a :href="getShareUrl('twitter')" target="_blank" rel="noopener">
                                            <span class="fab fa-twitter"></span>
                                        </a>
                                        <a :href="getShareUrl('linkedin')" target="_blank" rel="noopener">
                                            <span class="icon-linkedin"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="portfolio-details__get-touch">
                                <Link :href="route('contact-us')">
                                    {{ trans('Get in Touch') }}<span class="icon-right-up"></span>
                                </Link>
                                <div class="portfolio-details__get-touch-shape"></div>
                            </div>
                        </div>

                        <div v-if="useCase.client_name || useCase.completed_year || useCase.category_tag"
                             class="portfolio-details__portfolio-list-inner">
                            <ul class="portfolio-details__portfolio-list list-unstyled">
                                <li v-if="useCase.client_name">
                                    <div class="icon">
                                        <span class="icon-add-friend"></span>
                                    </div>
                                    <div class="content">
                                        <span>{{ trans('Client') }}</span>
                                        <p>{{ useCase.client_name }}</p>
                                    </div>
                                </li>
                                <li v-if="useCase.completed_year">
                                    <div class="icon">
                                        <span class="icon-calendar"></span>
                                    </div>
                                    <div class="content">
                                        <span>{{ trans('Year') }}</span>
                                        <p>{{ useCase.completed_year }}</p>
                                    </div>
                                </li>
                                <li v-if="useCase.category_tag">
                                    <div class="icon">
                                        <span class="icon-real-estate-agency"></span>
                                    </div>
                                    <div class="content">
                                        <span>{{ trans('Category') }}</span>
                                        <p>{{ useCase.category_tag }}</p>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="portfolio-details__img-1">
                            <img :src="useCase.image_link" :alt="useCase.title">
                        </div>

                        <ul class="portfolio-details__portfolio-page list-unstyled">
                            <li v-if="useCase.summary">
                                <div class="portfolio-details__count"></div>
                                <div class="portfolio-details__portfolio-content">
                                    <h4 class="portfolio-details__title-1">{{ trans('Project Overview') }}</h4>
                                    <p class="portfolio-details__text-1">{{ useCase.summary }}</p>
                                </div>
                            </li>

                            <li v-if="useCase.challenge">
                                <div class="portfolio-details__count"></div>
                                <div class="portfolio-details__portfolio-content">
                                    <h4 class="portfolio-details__title-1">{{ trans('The Challenge') }}</h4>
                                    <p class="portfolio-details__text-1">{{ useCase.challenge }}</p>
                                </div>
                            </li>

                            <li v-if="useCase.solution">
                                <div class="portfolio-details__count"></div>
                                <div class="portfolio-details__portfolio-content">
                                    <h4 class="portfolio-details__title-2">{{ trans('Our Solution') }}</h4>
                                    <p class="portfolio-details__text-2">{{ useCase.solution }}</p>
                                </div>
                            </li>

                            <li v-if="useCase.results">
                                <div class="portfolio-details__count"></div>
                                <div class="portfolio-details__portfolio-content">
                                    <h4 class="portfolio-details__title-2">{{ trans('The Results') }}</h4>
                                    <p class="portfolio-details__text-3">{{ useCase.results }}</p>
                                </div>
                            </li>

                            <li v-if="useCase.content">
                                <div class="portfolio-details__count"></div>
                                <div class="portfolio-details__portfolio-content">
                                    <div class="content" v-html="useCase.content"></div>
                                </div>
                            </li>

                            <li v-if="useCase.technologies && useCase.technologies.length">
                                <div class="portfolio-details__count"></div>
                                <div class="portfolio-details__portfolio-content">
                                    <h4 class="portfolio-details__title-3">{{ trans('Technologies Used') }}</h4>
                                    <div class="portfolio-one__tag portfolio-details__tech-tags">
                                        <span v-for="tech in useCase.technologies" :key="tech">{{ tech }}</span>
                                    </div>
                                </div>
                            </li>

                            <li v-if="useCase.project_url">
                                <div class="portfolio-details__count"></div>
                                <div class="portfolio-details__portfolio-content">
                                    <a :href="useCase.project_url" target="_blank" rel="noopener" class="thm-btn">
                                        {{ trans('Visit Live Project') }}
                                        <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                                    </a>
                                </div>
                            </li>
                        </ul>

                        <div v-if="relatedUseCases.length" class="mt-5 pt-4">
                            <h4 class="portfolio-details__title-3 mb-4">{{ trans('More Case Studies') }}</h4>
                            <div class="row">
                                <div v-for="item in relatedUseCases" :key="item.id" class="col-xl-4 col-lg-6 col-md-6 mb-4">
                                    <div class="portfolio-one__single">
                                        <div class="portfolio-one__img-box">
                                            <div class="portfolio-one__img">
                                                <Link :href="route('use-cases.show', item.slug)">
                                                    <img :src="item.image_link" :alt="item.title">
                                                </Link>
                                                <div v-if="item.category_tag" class="portfolio-one__tag">
                                                    <span>{{ item.category_tag }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="portfolio-one__content">
                                            <div class="portfolio-one__title-box">
                                                <h3 class="portfolio-one__title">
                                                    <Link :href="route('use-cases.show', item.slug)">{{ item.title }}</Link>
                                                </h3>
                                                <p v-if="item.summary" class="portfolio-one__text">{{ item.summary }}</p>
                                            </div>
                                            <div class="portfolio-one__arrow">
                                                <Link :href="route('use-cases.show', item.slug)">
                                                    <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                                                </Link>
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

        <CtaTwo />
    </app-layout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/App.vue'
import CtaTwo from '@/Components/CtaTwo.vue'

const page = usePage()
const trans = (key) => page.props.translations[key] || key
const seo = computed(() => page.props.seo)
const settings = computed(() => page.props.settings || {})
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale || 'en')
const useCase = computed(() => page.props.useCase || {})
const relatedUseCases = computed(() => page.props.relatedUseCases || [])
const meta = computed(() => page.props.meta || {})

const metaTitle = computed(() => meta.value.title || `${useCase.value.title || trans('Use Cases')} | ${seo.value.website_name || ''}`.trim())
const metaDescription = computed(() => meta.value.description || useCase.value.summary || seo.value.website_desc || '')
const metaKeywords = computed(() => meta.value.keywords || (useCase.value.technologies || []).join(', ') || seo.value.website_keywords || '')
const metaImage = computed(() => meta.value?.og?.image || meta.value?.twitter?.image || useCase.value.image_link || settings.value?.meta_img || '')
const metaCanonical = computed(() => meta.value.canonical || '')
const metaRobots = computed(() => meta.value.robots || 'index, follow')

const getShareUrl = (platform) => {
    if (typeof window === 'undefined') {
        return '#'
    }

    const url = encodeURIComponent(window.location.href)
    const title = encodeURIComponent(useCase.value.title || '')

    switch (platform) {
        case 'twitter':
            return `https://twitter.com/intent/tweet?url=${url}&text=${title}`
        case 'facebook':
            return `https://www.facebook.com/sharer/sharer.php?u=${url}`
        case 'linkedin':
            return `https://www.linkedin.com/shareArticle?mini=true&url=${url}&title=${title}`
        default:
            return '#'
    }
}
</script>

<script>
export default {
    components: {
        AppLayout,
        CtaTwo,
    },
}
</script>

<style scoped>
.portfolio-details__tech-tags {
    position: static;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
}

.portfolio-details__tech-tags span {
    position: static;
}
</style>
