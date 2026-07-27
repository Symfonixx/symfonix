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
        <meta property="og:type" content="article">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" :content="metaTitle">
        <meta name="twitter:description" :content="metaDescription">
        <meta v-if="metaImage" name="twitter:image" :content="metaImage">
    </Head>

    <app-layout>
        <section class="page-header">
            <div class="page-header__bg"
                 :style="{ backgroundImage: `url(${asset_path}images/backgrounds/our-team-bg.jpg)` }">
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
                                <Link :href="route('use-cases.index')">{{ trans('Case Studies') }}</Link>
                            </li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li>{{ useCase.title }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="blog-details use-cases-page">
            <div class="use-cases-page__bg" aria-hidden="true">
                <div class="use-cases-page__orb use-cases-page__orb--one"></div>
                <div class="use-cases-page__orb use-cases-page__orb--two"></div>
                <div class="use-cases-page__orb use-cases-page__orb--three"></div>
            </div>

            <div class="container position-relative">
                <div class="row">
                    <div class="col-xl-12">
                        <article class="use-case-detail">
                    <div class="use-case-detail__glow" aria-hidden="true"></div>

                    <header class="use-case-detail__header">
                        <div class="use-case-detail__identity">
                            <Link :href="route('use-cases.index')" class="use-case-detail__avatar">
                                <img :src="useCase.image_link" :alt="useCase.title" loading="lazy" decoding="async">
                            </Link>
                            <div class="use-case-detail__intro">
                                <h1 class="use-case-detail__title">{{ useCase.title }}</h1>
                                <p v-if="useCase.client_name" class="use-case-detail__subtitle">{{ useCase.client_name }}</p>
                                <p v-else-if="useCase.category_tag" class="use-case-detail__subtitle">{{ useCase.category_tag }}</p>
                            </div>
                        </div>

                        <div class="use-case-detail__actions">
                            <div class="use-case-detail__share">
                                <span class="use-case-detail__share-label">{{ trans('Share now') }}</span>
                                <div class="use-case-detail__share-links">
                                    <a :href="getShareUrl('facebook')" target="_blank" rel="noopener" class="use-case-detail__share-btn" aria-label="Facebook">
                                        <span class="icon-facebook"></span>
                                    </a>
                                    <a :href="getShareUrl('twitter')" target="_blank" rel="noopener" class="use-case-detail__share-btn" aria-label="Twitter">
                                        <span class="fab fa-twitter"></span>
                                    </a>
                                    <a :href="getShareUrl('linkedin')" target="_blank" rel="noopener" class="use-case-detail__share-btn" aria-label="LinkedIn">
                                        <span class="icon-linkedin"></span>
                                    </a>
                                </div>
                            </div>
                            <Link :href="route('contact-us')" class="use-case-detail__pill use-case-detail__pill--cta">
                                {{ trans('Get in Touch') }}
                                <span class="icon-right-up"></span>
                            </Link>
                        </div>
                    </header>

                    <div
                        v-if="useCase.client_name || useCase.completed_year || useCase.category_tag"
                        class="use-case-detail__meta"
                    >
                        <div v-if="useCase.client_name" class="use-case-detail__pill">
                            <i class="fas fa-user"></i>
                            <span>{{ trans('Client') }}: {{ useCase.client_name }}</span>
                        </div>
                        <div v-if="useCase.completed_year" class="use-case-detail__pill">
                            <i class="far fa-calendar-alt"></i>
                            <span>{{ useCase.completed_year }}</span>
                        </div>
                        <div v-if="useCase.category_tag" class="use-case-detail__pill">
                            <i class="fas fa-tag"></i>
                            <span>{{ useCase.category_tag }}</span>
                        </div>
                    </div>

                    <div class="use-case-detail__hero">
                        <img :src="useCase.image_link" :alt="useCase.title" loading="lazy" decoding="async">
                    </div>

                    <div v-if="useCase.summary" class="use-case-detail__section">
                        <div class="use-case-detail__section-head">
                            <span class="use-case-detail__section-icon"><i class="fas fa-lightbulb"></i></span>
                            <span class="use-case-detail__section-label">{{ trans('Project Overview') }}</span>
                        </div>
                        <p class="use-case-detail__text">{{ useCase.summary }}</p>
                    </div>

                    <div v-if="useCase.challenge" class="use-case-detail__section">
                        <div class="use-case-detail__section-head">
                            <span class="use-case-detail__section-icon"><i class="fas fa-exclamation-triangle"></i></span>
                            <span class="use-case-detail__section-label">{{ trans('The Challenge') }}</span>
                        </div>
                        <p class="use-case-detail__text">{{ useCase.challenge }}</p>
                    </div>

                    <div v-if="useCase.solution" class="use-case-detail__section">
                        <div class="use-case-detail__section-head">
                            <span class="use-case-detail__section-icon"><i class="fas fa-cogs"></i></span>
                            <span class="use-case-detail__section-label">{{ trans('Our Solution') }}</span>
                        </div>
                        <p class="use-case-detail__text">{{ useCase.solution }}</p>
                    </div>

                    <div v-if="useCase.results" class="use-case-detail__section">
                        <div class="use-case-detail__section-head">
                            <span class="use-case-detail__section-icon"><i class="fas fa-chart-line"></i></span>
                            <span class="use-case-detail__section-label">{{ trans('The Results') }}</span>
                        </div>
                        <p class="use-case-detail__text">{{ useCase.results }}</p>
                    </div>

                    <div v-if="useCase.content" class="use-case-detail__section">
                        <div class="use-case-detail__section-head">
                            <span class="use-case-detail__section-icon"><i class="fas fa-align-left"></i></span>
                            <span class="use-case-detail__section-label">{{ trans('Details') }}</span>
                        </div>
                        <div class="use-case-detail__content" v-html="useCase.content"></div>
                    </div>

                    <div v-if="useCase.technologies && useCase.technologies.length" class="use-case-detail__section">
                        <div class="use-case-detail__section-head">
                            <span class="use-case-detail__section-icon"><i class="fas fa-code"></i></span>
                            <span class="use-case-detail__section-label">{{ trans('Technologies Used') }}</span>
                        </div>
                        <div class="use-case-detail__tags">
                            <span v-for="tech in useCase.technologies" :key="tech" class="use-case-detail__tag">
                                {{ tech }}
                            </span>
                        </div>
                    </div>

                    <footer v-if="useCase.project_url" class="use-case-detail__footer">
                        <a
                            :href="useCase.project_url"
                            target="_blank"
                            rel="noopener"
                            class="use-case-detail__pill use-case-detail__pill--cta"
                        >
                            {{ trans('Visit Live Project') }}
                            <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                        </a>
                    </footer>
                </article>

                <div v-if="relatedUseCases.length" class="use-cases-related">
                    <div class="use-cases-related__head">
                        <div class="section-title__tagline-box justify-content-center">
                            <div class="section-title__tagline-shape-1"></div>
                            <span class="section-title__tagline">{{ trans('More Case Studies') }}</span>
                            <div class="section-title__tagline-shape-2"></div>
                        </div>
                        <h3 class="use-cases-related__title">{{ trans('Explore More Success Stories') }}</h3>
                    </div>
                    <div class="row">
                        <div
                            v-for="item in relatedUseCases"
                            :key="item.id"
                            class="col-xl-4 col-lg-6 col-md-6"
                        >
                            <UseCaseCard :item="item" :locale="locale" variant="compact" />
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

const metaTitle = computed(() => meta.value.title || `${useCase.value.title || trans('Case Studies')} | ${seo.value.website_name || ''}`.trim())
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

.use-case-detail {
    --uc-blue: #2189ca;
    --uc-accent: #7fc457;
    position: relative;
    padding: 24px;
    border-radius: 40px;
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.14);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    box-shadow:
        0 8px 32px rgba(11, 25, 44, 0.35),
        inset 0 1px 0 rgba(255, 255, 255, 0.12);
    overflow: hidden;
}

.use-case-detail__glow {
    position: absolute;
    inset: -30% -10% auto;
    height: 240px;
    background: radial-gradient(circle, rgba(33, 137, 202, 0.35) 0%, rgba(127, 196, 87, 0.08) 45%, transparent 70%);
    pointer-events: none;
}

.use-case-detail__header {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    padding-bottom: 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.use-case-detail__identity {
    display: flex;
    align-items: center;
    gap: 18px;
    min-width: 0;
}

.use-case-detail__avatar {
    flex-shrink: 0;
    width: 88px;
    height: 88px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.25);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
    background: linear-gradient(135deg, var(--uc-blue), var(--uc-accent));
}

.use-case-detail__avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.use-case-detail__title {
    margin: 0 0 8px;
    font-size: clamp(1.5rem, 3vw, 2rem);
    font-weight: 700;
    color: #fff;
    line-height: 1.3;
}

.use-case-detail__subtitle {
    margin: 0;
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.72);
}

.use-case-detail__actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 16px;
}

.use-case-detail__share {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.use-case-detail__share-label {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: rgba(255, 255, 255, 0.7);
}

.use-case-detail__share-links {
    display: flex;
    gap: 8px;
}

.use-case-detail__share-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    color: #fff;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.14);
    text-decoration: none;
    transition: background 0.25s ease, border-color 0.25s ease;
}

.use-case-detail__share-btn:hover {
    background: rgba(33, 137, 202, 0.4);
    border-color: rgba(33, 137, 202, 0.5);
    color: #fff;
}

.use-case-detail__meta {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 20px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.use-case-detail__pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9);
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.12);
    text-decoration: none;
    transition: background 0.25s ease, border-color 0.25s ease;
}

.use-case-detail__pill--cta {
    background: rgba(33, 137, 202, 0.35);
    border-color: rgba(33, 137, 202, 0.5);
    color: #fff;
}

.use-case-detail__pill--cta:hover {
    background: var(--uc-blue);
    border-color: var(--uc-blue);
    color: #fff;
}

.use-case-detail__hero {
    position: relative;
    margin-top: 24px;
    margin-bottom: 0;
    border-radius: 40px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.use-case-detail__hero img {
    width: 100%;
    max-height: 480px;
    object-fit: cover;
    display: block;
}

.use-case-detail__section {
    position: relative;
    padding: 20px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.use-case-detail__section-head {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
}

.use-case-detail__section-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(33, 137, 202, 0.25);
    color: #fff;
    font-size: 0.75rem;
}

.use-case-detail__section-label {
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.85);
}

.use-case-detail__text {
    margin: 0;
    font-size: 0.975rem;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.78);
}

.use-case-detail__content {
    font-size: 0.975rem;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.78);
}

.use-case-detail__content :deep(p) {
    margin-bottom: 1rem;
    color: rgba(255, 255, 255, 0.78);
}

.use-case-detail__content :deep(h1),
.use-case-detail__content :deep(h2),
.use-case-detail__content :deep(h3),
.use-case-detail__content :deep(h4) {
    color: #fff;
    margin-bottom: 0.75rem;
}

.use-case-detail__content :deep(a) {
    color: var(--uc-accent);
}

.use-case-detail__tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.use-case-detail__tag {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 500;
    color: #fff;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.14);
}

.use-case-detail__footer {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding-top: 24px;
}

.use-cases-related {
    position: relative;
    z-index: 1;
    margin-top: 50px;
    padding: 32px;
    border-radius: 40px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
}

.blog-details.use-cases-page :deep(.use-case-card) {
    margin-bottom: 30px;
}

.use-cases-related__head {
    text-align: center;
    margin-bottom: 58px;
    margin-top: -12px;
}

.use-cases-related__title {
    margin: 12px 0 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff;
}

@media (max-width: 767px) {
    .use-case-detail {
        padding: 24px;
    }

    .use-case-detail__header {
        flex-direction: column;
    }

    .use-case-detail__actions {
        width: 100%;
        justify-content: space-between;
    }
}
</style>
