<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'"/>
        <title>{{ metaTitle }}</title>
        <meta name="description" :content="metaDescription">
        <meta name="keywords" :content="metaKeywords">
        <meta name="robots" :content="metaRobots">
        <meta property="og:title" :content="metaTitle">
        <meta property="og:description" :content="metaDescription">
        <meta v-if="metaImage" property="og:image" :content="metaImage">
        <meta property="og:type" content="product">
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
                    <h2>{{ product.name }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li>
                                <Link :href="route('home')">
                                    <i class="fas fa-home"></i>{{ trans('Home') }}
                                </Link>
                            </li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li>
                                <Link :href="route('product.index')">{{ trans('Products') }}</Link>
                            </li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li>{{ product.name }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="blog-details products-page">
            <div class="products-page__bg" aria-hidden="true">
                <div class="products-page__orb products-page__orb--one"></div>
                <div class="products-page__orb products-page__orb--two"></div>
                <div class="products-page__orb products-page__orb--three"></div>
            </div>

            <div class="container position-relative">
                <div class="row">
                    <div class="col-xl-12">
                        <article class="product-detail">
                            <div class="product-detail__glow" aria-hidden="true"></div>

                            <header class="product-detail__header">
                                <div class="product-detail__identity">
                                    <Link :href="route('product.index')" class="product-detail__thumb">
                                        <img :src="product.main_image_link" :alt="product.name" loading="lazy" decoding="async">
                                    </Link>
                                    <div class="product-detail__intro">
                                        <p v-if="product.category" class="product-detail__category">
                                            {{ product.category.name }}
                                        </p>
                                        <h1 class="product-detail__title">{{ product.name }}</h1>
                                        <p v-if="formattedPrice" class="product-detail__price">
                                            <span class="product-detail__price-amount">{{ formattedPrice }}</span>
                                            <span v-if="billingLabel" class="product-detail__price-billing">{{ billingLabel }}</span>
                                        </p>
                                        <p v-if="product.short_description" class="product-detail__subtitle">
                                            {{ product.short_description }}
                                        </p>
                                    </div>
                                </div>

                                <div class="product-detail__actions">
                                    <div class="product-detail__share">
                                        <span class="product-detail__share-label">{{ trans('Share now') }}</span>
                                        <div class="product-detail__share-links">
                                            <a :href="getShareUrl('facebook')" target="_blank" rel="noopener" class="product-detail__share-btn" aria-label="Facebook">
                                                <span class="icon-facebook"></span>
                                            </a>
                                            <a :href="getShareUrl('twitter')" target="_blank" rel="noopener" class="product-detail__share-btn" aria-label="Twitter">
                                                <span class="fab fa-twitter"></span>
                                            </a>
                                            <a :href="getShareUrl('linkedin')" target="_blank" rel="noopener" class="product-detail__share-btn" aria-label="LinkedIn">
                                                <span class="icon-linkedin"></span>
                                            </a>
                                        </div>
                                    </div>
                                    <button type="button" class="product-detail__pill product-detail__pill--cta" @click="openDemoModal">
                                        {{ trans('Request Live Demo') }}
                                        <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                                    </button>
                                </div>
                            </header>

                            <div
                                v-if="product.category || product.is_featured"
                                class="product-detail__meta"
                            >
                                <div v-if="product.category" class="product-detail__pill">
                                    <i class="fas fa-tag"></i>
                                    <span>{{ product.category.name }}</span>
                                </div>
                                <div v-if="product.is_featured" class="product-detail__pill product-detail__pill--featured">
                                    <i class="fas fa-star"></i>
                                    <span>{{ trans('Featured') }}</span>
                                </div>
                            </div>

                            <div class="product-detail__hero">
                                <img :src="product.main_image_link" :alt="product.name" loading="lazy" decoding="async">
                            </div>

                            <div v-if="product.short_description" class="product-detail__section">
                                <div class="product-detail__section-head">
                                    <span class="product-detail__section-icon"><i class="fas fa-lightbulb"></i></span>
                                    <span class="product-detail__section-label">{{ trans('Overview') }}</span>
                                </div>
                                <p class="product-detail__text">{{ product.short_description }}</p>
                            </div>

                            <div v-if="product.description" class="product-detail__section">
                                <div class="product-detail__section-head">
                                    <span class="product-detail__section-icon"><i class="fas fa-align-left"></i></span>
                                    <span class="product-detail__section-label">{{ trans('Product Details') }}</span>
                                </div>
                                <div class="product-detail__content" v-html="product.description"></div>
                            </div>

                            <footer class="product-detail__footer">
                                <button type="button" class="product-detail__pill product-detail__pill--cta" @click="openDemoModal">
                                    {{ trans('Request Live Demo') }}
                                    <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                                </button>
                                <Link :href="route('contact-us')" class="product-detail__pill">
                                    {{ trans('Get in Touch') }}
                                    <span class="icon-right-up"></span>
                                </Link>
                            </footer>
                        </article>

                        <div v-if="relatedProducts.length" class="products-related">
                            <div class="products-related__head">
                                <div class="section-title__tagline-box justify-content-center">
                                    <div class="section-title__tagline-shape-1"></div>
                                    <span class="section-title__tagline">{{ trans('More Products') }}</span>
                                    <div class="section-title__tagline-shape-2"></div>
                                </div>
                                <h3 class="products-related__title">{{ trans('Explore More Solutions') }}</h3>
                            </div>
                            <div class="row">
                                <div
                                    v-for="item in relatedProducts"
                                    :key="item.id"
                                    class="col-xl-4 col-lg-6 col-md-6"
                                >
                                    <ProductCard :item="item" :locale="locale" variant="compact" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <ContactRequestModal
            ref="contactModal"
            modal-id="productDemoModal"
            :title="trans('Request Live Demo')"
            :description="demoModalDescription"
            :default-subject="demoSubject"
            :default-message="demoMessage"
            :submit-label="trans('Send Request')"
        />

        <CtaTwo />
    </app-layout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/App.vue'
import CtaTwo from '@/Components/CtaTwo.vue'
import ContactRequestModal from '@/Components/ContactRequestModal.vue'

const page = usePage()
const trans = (key) => page.props.translations[key] || key
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale || 'en')
const product = computed(() => page.props.product || {})
const relatedProducts = computed(() => page.props.relatedProducts || [])
const meta = computed(() => page.props.meta || {})

const contactModal = ref(null)

const metaTitle = computed(() => meta.value.title || product.value.seo_title || product.value.name || '')
const metaDescription = computed(() => meta.value.description || product.value.seo_description || product.value.short_description || '')
const metaKeywords = computed(() => meta.value.keywords || '')
const metaImage = computed(() => meta.value?.og?.image || product.value.main_image_link || '')
const metaRobots = computed(() => meta.value.robots || 'index, follow')

const formattedPrice = computed(() => {
    const raw = product.value.price
    if (raw === null || raw === undefined || raw === '') {
        return ''
    }

    const amount = Number(raw).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })

    return `${amount} ${product.value.currency || 'USD'}`
})

const billingLabel = computed(() => {
    const type = product.value.billing_type
    if (!type || type === 'one_time') {
        return ''
    }

    const labels = {
        monthly: trans('/mo'),
        quarterly: trans('/quarter'),
        yearly: trans('/yr'),
    }

    return labels[type] || ''
})

const demoSubject = computed(() => `${trans('Live Demo Request')}: ${product.value.name || ''}`.trim())
const demoMessage = computed(() => {
    const intro = trans('I would like to request a live demo for this product.')
    const name = product.value.name ? `${trans('Product')}: ${product.value.name}` : ''

    return [intro, name].filter(Boolean).join('\n\n')
})
const demoModalDescription = computed(() => trans('Fill out the form below and our team will schedule your live demo.'))

const openDemoModal = () => {
    contactModal.value?.show()
}

const getShareUrl = (platform) => {
    if (typeof window === 'undefined') {
        return '#'
    }

    const url = encodeURIComponent(window.location.href)
    const title = encodeURIComponent(product.value.name || '')

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
import ContactRequestModal from '@/Components/ContactRequestModal.vue'
import ProductCard from '@/Components/ProductCard.vue'

export default {
    components: {
        AppLayout,
        CtaTwo,
        ContactRequestModal,
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

.product-detail {
    --product-blue: #2189ca;
    --product-accent: #7fc457;
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

.product-detail__glow {
    position: absolute;
    inset: -30% -10% auto;
    height: 240px;
    background: radial-gradient(circle, rgba(33, 137, 202, 0.35) 0%, rgba(127, 196, 87, 0.08) 45%, transparent 70%);
    pointer-events: none;
}

.product-detail__header {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    padding-bottom: 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.product-detail__identity {
    display: flex;
    align-items: center;
    gap: 18px;
    min-width: 0;
}

.product-detail__thumb {
    flex-shrink: 0;
    width: 88px;
    height: 88px;
    border-radius: 20px;
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.25);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
    background: linear-gradient(135deg, var(--product-blue), var(--product-accent));
}

.product-detail__thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.product-detail__category {
    margin: 0 0 6px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--product-accent);
}

.product-detail__title {
    margin: 0 0 8px;
    font-size: clamp(1.5rem, 3vw, 2rem);
    font-weight: 700;
    color: #fff;
    line-height: 1.3;
}

.product-detail__price {
    margin: 0 0 10px;
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    gap: 6px;
}

.product-detail__price-amount {
    font-size: clamp(1.15rem, 2.2vw, 1.45rem);
    font-weight: 700;
    color: var(--product-accent, #2189ca);
    letter-spacing: 0.01em;
}

.product-detail__price-billing {
    font-size: 0.9rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.65);
}

.product-detail__subtitle {
    margin: 0;
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.72);
    line-height: 1.5;
}

.product-detail__actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 16px;
}

.product-detail__share {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.product-detail__share-label {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: rgba(255, 255, 255, 0.7);
}

.product-detail__share-links {
    display: flex;
    gap: 8px;
}

.product-detail__share-btn {
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

.product-detail__share-btn:hover {
    background: rgba(33, 137, 202, 0.4);
    border-color: rgba(33, 137, 202, 0.5);
    color: #fff;
}

.product-detail__meta {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 20px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.product-detail__pill {
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
    cursor: pointer;
    transition: background 0.25s ease, border-color 0.25s ease;
}

.product-detail__pill--featured {
    background: rgba(127, 196, 87, 0.25);
    border-color: rgba(127, 196, 87, 0.4);
}

.product-detail__pill--cta {
    background: rgba(33, 137, 202, 0.35);
    border-color: rgba(33, 137, 202, 0.5);
    color: #fff;
}

.product-detail__pill--cta:hover {
    background: var(--product-blue);
    border-color: var(--product-blue);
    color: #fff;
}

.product-detail__hero {
    position: relative;
    margin-top: 24px;
    border-radius: 28px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.product-detail__hero img {
    width: 100%;
    max-height: 480px;
    object-fit: cover;
    display: block;
}

.product-detail__section {
    position: relative;
    padding: 24px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.product-detail__section-head {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
}

.product-detail__section-icon {
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

.product-detail__section-label {
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.85);
}

.product-detail__text {
    margin: 0;
    font-size: 0.975rem;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.78);
}

.product-detail__content {
    font-size: 0.975rem;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.78);
}

.product-detail__content :deep(p) {
    margin-bottom: 1rem;
    color: rgba(255, 255, 255, 0.78);
}

.product-detail__content :deep(h1),
.product-detail__content :deep(h2),
.product-detail__content :deep(h3),
.product-detail__content :deep(h4) {
    color: #fff;
    margin-bottom: 0.75rem;
}

.product-detail__content :deep(a) {
    color: var(--product-accent);
}

.product-detail__content :deep(ul),
.product-detail__content :deep(ol) {
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}

.product-detail__content :deep(li) {
    margin-bottom: 0.5rem;
}

.product-detail__content :deep(img) {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    margin: 1rem 0;
}

.product-detail__footer {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding-top: 24px;
}

.products-related {
    position: relative;
    z-index: 1;
    margin-top: 50px;
    padding: 32px;
    border-radius: 40px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
}

.blog-details.products-page :deep(.product-card) {
    margin-bottom: 30px;
}

.products-related__head {
    text-align: center;
    margin-bottom: 48px;
}

.products-related__head :deep(.section-title__tagline) {
    color: rgba(255, 255, 255, 0.85);
}

.products-related__title {
    margin: 12px 0 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff;
}

@media (max-width: 767px) {
    .product-detail {
        padding: 20px;
        border-radius: 28px;
    }

    .product-detail__header {
        flex-direction: column;
    }

    .product-detail__actions {
        width: 100%;
        justify-content: space-between;
    }

    .product-detail__pill--cta {
        width: 100%;
        justify-content: center;
    }
}
</style>
