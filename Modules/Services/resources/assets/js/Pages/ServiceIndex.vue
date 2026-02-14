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
                 :style="{ backgroundImage: `url(${asset_path}images/backgrounds/services-bg.jpg)`}">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans("Our Services") }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li>
                                <Link :href="route('home')">
                                    <i class="fas fa-home"></i>{{ trans("Home") }}
                                </Link>
                            </li>
                            <li>
                                <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span>
                            </li>
                            <li>{{ trans("Our Services") }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Two Start -->
        <section class="services-two">

            <div class="container">
                <div class="services-two__top">
                    <div class="section-title text-left sec-title-animation animation-style2">
                        <div class="section-title__tagline-box">
                            <div class="section-title__tagline-shape-1"></div>
                            <span class="section-title__tagline">{{ trans("Our Services") }}</span>

                        </div>
                        <h2 class="section-title__title title-animation">
                            {{ trans("Scale Your Business Smarter with Next-Gen IT Solutions") }}
                            <span></span>
                        </h2>
                    </div>
                </div>
                <div class="services-two__bottom">
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="services-two__services-list">
                                <div class="row">
                                    <div
                                        v-for="serviceItem in services.data"
                                        :key="serviceItem.id"
                                        class="col-xl-6 col-lg-6 col-md-6 mb-4"
                                    >
                                        <ServiceCardThree
                                            :title="getServiceTitle(serviceItem)"
                                            :short-desc="serviceItem.short_desc"
                                            :description="getServiceDescription(serviceItem)"
                                            :highlights="getServiceHighlights(serviceItem)"
                                            :link="getServiceUrl(serviceItem)"
                                            :image="serviceItem.image_link"
                                            :button-label="trans('Read More')"
                                            :is-rtl="locale === 'ar'"
                                            :reading-time="serviceItem.reading_time"
                                            :reading-time-label="trans('min read')"
                                        />
                                    </div>
                                </div>
                                <div v-if="!services.data.length" class="text-center py-5">
                                    <h3 class="text-muted">{{ trans("No services found") }}</h3>
                                </div>
                            </div>

                            <div v-if="services.last_page > 1" class="blog-page__pagination services-pagination">
                                <ul class="pg-pagination list-unstyled">
                                    <li v-if="services.prev_page_url" class="prev">
                                        <Link :href="services.prev_page_url" aria-label="prev">
                                            <span class="icon-left-arrow-1"></span>
                                        </Link>
                                    </li>
                                    <template v-for="(link, linkIndex) in services.links" :key="linkIndex">
                                        <li v-if="link.url && linkIndex > 0 && linkIndex < services.links.length - 1"
                                            :class="['count', link.active ? 'active' : '']">
                                            <Link :href="link.url">{{ link.label }}</Link>
                                        </li>
                                    </template>
                                    <li v-if="services.next_page_url" class="next">
                                        <Link :href="services.next_page_url" aria-label="Next">
                                            <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span>
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="services-index__sidebar">
                                <div class="services-details__services-list-box">
                                    <h3 class="services-details__services-list-title">{{
                                            trans("Service Categories")
                                        }}</h3>
                                    <ul class="services-details__services-list list-unstyled">
                                        <li>
                                            <Link
                                                :href="route('services.index')"
                                                :class="{ 'active': !filters.category }"
                                            >
                                                ({{ totalServicesCount }}) {{ trans("All Services") }}


                                                <span
                                                    :class="locale === 'ar' ? 'icon-left-arrow-2' : 'icon-right-arrow-2'"></span>
                                            </Link>
                                        </li>
                                        <li v-for="category in categories" :key="category.id">
                                            <Link
                                                :href="route('services.index', { category: category.slug })"
                                                :class="{ 'active': filters.category === category.slug }"
                                            >
                                                ({{ category.services_count || 0 }}) {{ getCategoryName(category) }}


                                                <span
                                                    :class="locale === 'ar' ? 'icon-left-arrow-2' : 'icon-right-arrow-2'"></span>
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Services Two End -->

        <CtaTwo />

    </app-layout>
</template>

<script setup>
import {computed} from 'vue'
import {usePage, Link, Head} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/App.vue'
import ServiceCardThree from '@/Components/Services/ServiceCardThree.vue'
import CtaTwo from '@/Components/CtaTwo.vue'


const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const seo = computed(() => page.props.seo)
const settings = computed(() => page.props.settings || {})
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale || 'en')
const categories = computed(() => page.props.categories || [])
const filters = computed(() => page.props.filters || {})
const meta = computed(() => page.props.meta || {})
const totalServicesCount = computed(() => page.props.totalServicesCount || 0)

const metaTitle = computed(() => {
    return `${trans("Our Services")} | ${seo.value.website_name || ''}`.trim()
})
const metaDescription = computed(() => {
    return meta.value.description
        || trans('Discover our IT services designed to scale and modernize your business.')
        || seo.value.website_desc
        || ''
})
const metaKeywords = computed(() => {
    return meta.value.keywords
        || trans('IT services, web development, mobile apps, AI solutions, cloud services')
        || seo.value.website_keywords
        || ''
})
const metaImage = computed(() => {
    return meta.value?.og?.image || meta.value?.twitter?.image || settings.value?.meta_img || ''
})
const metaCanonical = computed(() => meta.value.canonical || '')
const metaRobots = computed(() => meta.value.robots || 'index, follow')

// Do NOT mutate page.props.services; return a derived, immutable copy instead
const services = computed(() => {
    const source = page.props.services || {data: [], links: [], last_page: 1}
    const data = Array.isArray(source.data)
        ? source.data.filter(service => service && service.id)
        : []
    // Return a shallow copy with filtered data to avoid reactive feedback loops
    return {
        ...source,
        data,
    }
})

const translateField = (value) => {
    if (!value) return ''
    if (typeof value === 'string') return value
    if (typeof value === 'object' && value !== null) {
        return value[locale.value] || value['en'] || value[Object.keys(value)[0]] || ''
    }
    return ''
}

// Helper function to safely get service URL
const getServiceUrl = (service) => {
    if (!service || !service.slug) {
        return '#'
    }
    try {
        return route('services.show', service.slug)
    } catch (e) {
        return '#'
    }
}

// Helper function to get service title (handles translatable)
const getServiceTitle = (service) => {
    return translateField(service?.title)
}

const getServiceDescription = (service) => {
    return translateField(service?.description)
}

// Helper function to get category name (handles translatable)
const getCategoryName = (category) => {
    return translateField(category?.title)
}

const normalizeKeywords = (rawKeywords) => {
    if (!rawKeywords) {
        return []
    }

    let parsed = rawKeywords
    if (typeof rawKeywords === 'string') {
        try {
            parsed = JSON.parse(rawKeywords)
        } catch (e) {
            parsed = rawKeywords
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
            .map((item) => item?.toString().trim())
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

const getServiceHighlights = (service) => {
    const keywords = normalizeKeywords(service?.keywords)
    if (keywords.length) {
        return keywords
    }
    return [
        trans("Web Development"),
        trans("App Development"),
        trans("Graphics Design"),
    ]
}

</script>

<script>
export default {
    components: {
        AppLayout,
        CtaTwo
    }
};
</script>

<style scoped>
.services-pagination {
    margin-top: 40px;
}

.services-index__sidebar {
    position: sticky;
    top: 120px;
}

.services-two__services-list-single {
    margin-bottom: 30px;
}
</style>
