<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'"/>
        <title>{{ trans("Our Services") }} | {{ seo.website_name }}</title>
    </Head>
    <app-layout>
        <div class="page-header">
            <div class="page-header__bg"
                 :style="{ backgroundImage: `url(${asset_path}site/images/backgrounds/page-header-bg.jpg)`}">
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
            <div class="services-two__shape-1"></div>
            <div class="container">
                <div class="services-two__top">
                    <div class="section-title text-left sec-title-animation animation-style2">
                        <div class="section-title__tagline-box">
                            <div class="section-title__tagline-shape-1"></div>
                            <span class="section-title__tagline">{{ trans("Our Services") }}</span>
                            <div class="section-title__tagline-shape-2"></div>
                        </div>
                        <h2 class="section-title__title title-animation">
                            {{ trans("Your Business with Cutting-Edge IT Solutions") }}
                            <span>{{ trans("Innovative IT Services") }}</span>
                        </h2>
                    </div>
                </div>
                <div class="services-two__bottom">
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="services-two__services-list">
                                <div
                                    v-for="(serviceItem, index) in services.data"
                                    :key="serviceItem.id"
                                    class="services-two__services-list-single"
                                >
                                    <div class="services-two__count-and-title">
                                        <div class="services-two__count"></div>
                                        <h3 class="services-two__title">
                                            <Link :href="getServiceUrl(serviceItem)">
                                                {{ getServiceTitle(serviceItem) }}
                                            </Link>
                                        </h3>
                                    </div>
                                    <div class="services-two__service-list-box">
                                        <ul class="services-two__services-list-inner list-unstyled">
                                            <li v-for="(chunk, chunkIndex) in chunkHighlights(getServiceHighlights(serviceItem))"
                                                :key="`${serviceItem.id}-chunk-${chunkIndex}`">
                                                <p v-for="(item, itemIndex) in chunk" :key="`${serviceItem.id}-${chunkIndex}-${itemIndex}`">
                                                    <span class="icon-plus"></span>{{ item }}
                                                </p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="services-two__hover-img" v-if="serviceItem.image_link">
                                        <img :src="serviceItem.image_link" :alt="getServiceTitle(serviceItem)">
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
                                    <h3 class="services-details__services-list-title">{{ trans("Service Categories") }}</h3>
                                    <ul class="services-details__services-list list-unstyled">
                                        <li>
                                            <Link
                                                :href="route('services.index')"
                                                :class="{ 'active': !filters.category }"
                                            >
                                                {{ trans("All Services") }}
                                                <span class="icon-right-arrow-2"></span>
                                            </Link>
                                        </li>
                                        <li v-for="category in categories" :key="category.id">
                                            <Link
                                                :href="route('services.index', { category: category.slug })"
                                                :class="{ 'active': filters.category === category.slug }"
                                            >
                                                {{ getCategoryName(category) }}
                                                <span class="icon-right-arrow-2"></span>
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

    </app-layout>
</template>

<script setup>
import {computed, ref} from 'vue'
import { usePage, Link, Head, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/App.vue'


const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const seo = computed(() => page.props.seo)
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale || 'en')
const categories = computed(() => page.props.categories || [])
const filters = computed(() => page.props.filters || {})

// Search and filter state
const searchQuery = ref(filters.value.search || '')
const selectedCategory = ref(filters.value.category || '')

// Do NOT mutate page.props.services; return a derived, immutable copy instead
const services = computed(() => {
    const source = page.props.services || { data: [], links: [], last_page: 1 }
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

// Helper function to get category name (handles translatable)
const getCategoryName = (category) => {
    return translateField(category?.title)
}

// Helper function to get service description (handles translatable)
const getServiceDescription = (service) => {
    const desc = translateField(service?.description)
    if (!desc) return ''
    return desc.length > 140 ? `${desc.substring(0, 140)}...` : desc
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

const chunkHighlights = (items, size = 2, maxItems = 6) => {
    const safeItems = Array.isArray(items) ? items.slice(0, maxItems) : []
    const chunks = []
    for (let i = 0; i < safeItems.length; i += size) {
        chunks.push(safeItems.slice(i, i + size))
    }
    return chunks
}

// Search and filter handlers
const handleSearch = () => {
    router.get(route('services.index'), {
        search: searchQuery.value || null,
        category: selectedCategory.value || null,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const handleCategoryFilter = () => {
    router.get(route('services.index'), {
        search: searchQuery.value || null,
        category: selectedCategory.value || null,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const clearSearch = () => {
    searchQuery.value = ''
    router.get(route('services.index'), {
        category: selectedCategory.value || null,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const clearCategory = () => {
    selectedCategory.value = ''
    router.get(route('services.index'), {
        search: searchQuery.value || null,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}
</script>

<script>
export default {
    components: {
        AppLayout
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
