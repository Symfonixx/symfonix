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
                    <h1>{{ trans('Products') }}</h1>
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
                            <li>{{ trans('Products') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <section class="blog-page products-page">
            <div class="products-page__bg" aria-hidden="true">
                <div class="products-page__orb products-page__orb--one"></div>
                <div class="products-page__orb products-page__orb--two"></div>
                <div class="products-page__orb products-page__orb--three"></div>
            </div>

            <div class="container position-relative">
                <div class="section-title text-center sec-title-animation animation-style1 products-page__head">
                    <div class="section-title__tagline-box">
                        <div class="section-title__tagline-shape-1"></div>
                        <span class="section-title__tagline">{{ trans('Our Catalog') }}</span>
                        <div class="section-title__tagline-shape-2"></div>
                    </div>
                    <h2 class="section-title__title title-animation">
                        {{ trans('B2B Solutions Built for Scale') }}
                    </h2>
                    <p class="products-page__subtitle">
                        {{ trans('Discover enterprise-ready platforms and services designed to grow with your business.') }}
                    </p>
                </div>

                <div v-if="products.data.length" class="products-page__stats">
                    <div class="products-page__stat">
                        <span class="products-page__stat-value">{{ products.total }}</span>
                        <span class="products-page__stat-label">{{ trans('Solutions Available') }}</span>
                    </div>
                    <div v-if="featuredCount" class="products-page__stat">
                        <span class="products-page__stat-value">{{ featuredCount }}</span>
                        <span class="products-page__stat-label">{{ trans('Featured') }}</span>
                    </div>
                </div>

                <div class="row">
                    <div
                        v-for="(product, index) in products.data"
                        :key="product.id"
                        class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp"
                        :data-wow-delay="`${(index % 3 + 1) * 100}ms`"
                    >
                        <ProductCard :item="product" :locale="locale" />
                    </div>

                    <div v-if="!products.data.length" class="col-12">
                        <div class="products-page__empty">
                            <div class="products-page__empty-icon" aria-hidden="true">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <h3>{{ trans('No records found') }}</h3>
                            <p>{{ trans('Check back soon — we are adding new solutions to our catalog.') }}</p>
                        </div>
                    </div>

                    <div v-if="products.last_page > 1" class="blog-page__pagination products-page__pagination">
                        <ul class="pg-pagination list-unstyled">
                            <li v-if="products.prev_page_url" class="prev">
                                <Link :href="products.prev_page_url" aria-label="Previous">
                                    <span class="icon-left-arrow-1"></span>
                                </Link>
                            </li>
                            <template v-for="(link, linkIndex) in products.links" :key="linkIndex">
                                <li v-if="link.url && linkIndex > 0 && linkIndex < products.links.length - 1"
                                    :class="['count', link.active ? 'active' : '']">
                                    <Link :href="link.url">{{ link.label }}</Link>
                                </li>
                            </template>
                            <li v-if="products.next_page_url" class="next">
                                <Link :href="products.next_page_url" aria-label="Next">
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
const products = computed(() => page.props.products || { data: [] })
const meta = computed(() => page.props.meta || {})

const featuredCount = computed(() => products.value.data?.filter((p) => p.is_featured).length || 0)

const metaTitle = computed(() => meta.value.title || `${trans('Products')} | ${seo.value.website_name || ''}`.trim())
const metaDescription = computed(() => meta.value.description || trans('Browse our B2B product catalog.') || seo.value.website_desc || '')
const metaKeywords = computed(() => meta.value.keywords || trans('products, B2B catalog, SaaS') || seo.value.website_keywords || '')
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
import ProductCard from '@/Components/ProductCard.vue'

export default {
    components: {
        AppLayout,
        CtaTwo,
        ProductCard,
    },
}
</script>

<style scoped>
.products-page {
    position: relative;
    overflow: hidden;
}

.products-page__bg {
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

.products-page__orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.45;
}

.products-page__orb--one {
    width: 420px;
    height: 420px;
    top: -120px;
    left: -80px;
    background: #2189ca;
}

.products-page__orb--two {
    width: 360px;
    height: 360px;
    top: 40%;
    right: -100px;
    background: #7fc457;
}

.products-page__orb--three {
    width: 280px;
    height: 280px;
    bottom: -60px;
    left: 35%;
    background: #1a5f8a;
}

.products-page__head :deep(.section-title__title) {
    color: #fff;
}

.products-page__head :deep(.section-title__tagline) {
    color: rgba(255, 255, 255, 0.85);
}

.products-page__subtitle {
    max-width: 640px;
    margin: 16px auto 0;
    font-size: 1.05rem;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.72);
}

.products-page__stats {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 16px;
    margin-bottom: 48px;
}

.products-page__stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    min-width: 140px;
    padding: 16px 28px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(12px);
}

.products-page__stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #fff;
    line-height: 1;
}

.products-page__stat-label {
    font-size: 0.8rem;
    font-weight: 500;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.65);
}

.blog-page.products-page :deep(.product-card) {
    margin-bottom: 30px;
}

.products-page__empty {
    text-align: center;
    padding: 64px 24px;
    border-radius: 32px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.products-page__empty-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 72px;
    height: 72px;
    margin-bottom: 20px;
    border-radius: 50%;
    font-size: 1.75rem;
    color: rgba(255, 255, 255, 0.7);
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.products-page__empty h3 {
    margin: 0 0 10px;
    font-size: 1.35rem;
    color: #fff;
}

.products-page__empty p {
    margin: 0;
    color: rgba(255, 255, 255, 0.65);
}

.products-page__pagination {
    position: relative;
    z-index: 1;
}
</style>
