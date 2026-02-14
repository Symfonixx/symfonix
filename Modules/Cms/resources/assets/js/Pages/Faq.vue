<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'" />
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/faq.css'" />
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
            <div class="page-header__bg" :style="{ backgroundImage: `url(${asset_path}images/contact-header-bg.jpg)` }">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2 v-html="trans('FAQs')"></h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li>
                                <Link :href="route('home')" v-if="typeof route !== 'undefined'">
                                    <i class="fas fa-home"></i>{{ trans('Home') }}
                                </Link>
                                <a :href="`/${locale === 'ar' ? 'ar' : ''}`" v-else>
                                    <i class="fas fa-home"></i>{{ trans('Home') }}
                                </a>
                            </li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li v-html="trans('FAQs')"></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!--Page Header End-->

        <!--FAQ Page Start-->
        <section class="faq-two faq-page">
            <div class="container">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <div class="section-title__tagline-shape-1"></div>
                        <span class="section-title__tagline">{{ trans('FAQs') }}</span>
                        <div class="section-title__tagline-shape-2"></div>
                    </div>
                    <h2 class="section-title__title title-animation">
                        {{ trans('Get answers to the most common questions about our products, services, and policies.') }}
                    </h2>
                </div>
                <div class="faq-two__right">
                    <div class="accrodion-grp" data-grp-name="faq-one-accrodion" v-if="faqs && faqs.length > 0">
                        <div class="accrodion" v-for="(faq, index) in faqs" :key="faq.id || index" :class="{ 'active': activeIndex === index }">
                            <div class="accrodion-title" @click="toggleAccordion(index)">
                                <h4>{{ translateField(faq.question) }}</h4>
                            </div>
                            <div class="accrodion-content" v-show="activeIndex === index">
                                <div class="inner">
                                    <p class="accrodion-content__text-1" v-html="translateField(faq.answer)"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-5">
                        <p>{{ trans('No FAQs found.') }}</p>
                    </div>
                </div>
            </div>
        </section>
        <!--FAQ Page End-->

        <CtaTwo />

    </app-layout>
</template>

<script setup>
import { computed, ref, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { Link, usePage, Head } from '@inertiajs/vue3'
import CtaTwo from '@/Components/CtaTwo.vue'

const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const seo = computed(() => page.props.seo)
const asset_path = computed(() => page.props.asset_path || '')
const settings = computed(() => page.props.settings || {})
const locale = computed(() => page.props.locale)
const meta = computed(() => page.props.meta || {})

const metaTitle = computed(() => {
    return meta.value.title || `${trans('FAQs')} | ${seo.value.website_name || ''}`.trim()
})
const metaDescription = computed(() => {
    return meta.value.description
        || trans('Find answers to common questions about our services and policies.')
        || seo.value.website_desc
        || ''
})
const metaKeywords = computed(() => {
    return meta.value.keywords
        || trans('FAQ, help center, support, common questions')
        || seo.value.website_keywords
        || ''
})
const metaImage = computed(() => {
    return meta.value?.og?.image || meta.value?.twitter?.image || settings.value?.meta_img || ''
})
const metaCanonical = computed(() => meta.value.canonical || '')
const metaRobots = computed(() => meta.value.robots || 'index, follow')

const faqs = computed(() => {
    return page.props.faqs || []
})

const activeIndex = ref(0)

const toggleAccordion = (index) => {
    activeIndex.value = activeIndex.value === index ? null : index
}

// Set first accordion as active by default when FAQs are loaded
watch(() => faqs.value, (newFaqs) => {
    if (newFaqs && newFaqs.length > 0 && activeIndex.value === null) {
        activeIndex.value = 0
    }
}, { immediate: true })

const translateField = (field) => {
    if (!field) return ''
    if (typeof field === 'string') return field
    if (typeof field === 'object') {
        return field[locale.value] || field['en'] || field[Object.keys(field)[0]] || ''
    }
    return ''
}

// Generate FAQ Schema for SEO (JSON-LD) - Client-side fallback
// Note: Schema is primarily generated server-side in the controller for SEO
const faqSchema = computed(() => {
    // Check if schema was already provided server-side
    if (page.props.faqSchema) {
        return page.props.faqSchema
    }
    
    // Fallback: Generate client-side if not provided server-side
    if (!faqs.value || !Array.isArray(faqs.value) || faqs.value.length === 0) {
        return null
    }

    const mainEntity = faqs.value
        .map(faq => {
            const question = translateField(faq.question)
            const answer = translateField(faq.answer)

            // Skip if question or answer is empty
            if (!question || !answer || question.trim() === '' || answer.trim() === '') {
                return null
            }

            // Strip HTML tags and clean up entities for schema (keep plain text)
            const answerText = answer
                .replace(/<[^>]*>/g, '') // Remove HTML tags
                .replace(/&nbsp;/g, ' ') // Clean up common entities
                .replace(/&amp;/g, '&') // Decode ampersands
                .replace(/&lt;/g, '<') // Decode less than
                .replace(/&gt;/g, '>') // Decode greater than
                .replace(/&quot;/g, '"') // Decode quotes
                .replace(/&#39;/g, "'") // Decode apostrophes
                .replace(/\s\s+/g, ' ') // Remove extra spaces
                .trim()

            // Skip if answer text is empty after cleaning
            if (!answerText || answerText.trim() === '') {
                return null
            }

            return {
                '@type': 'Question',
                name: question.trim(),
                acceptedAnswer: {
                    '@type': 'Answer',
                    text: answerText
                }
            }
        })
        .filter(item => item !== null) // Remove null entries

    // Return null if no valid FAQs
    if (mainEntity.length === 0) {
        return null
    }

    return {
        '@context': 'https://schema.org',
        '@type': 'FAQPage',
        mainEntity: mainEntity
    }
})

const faqSchemaJson = computed(() => {
    if (!faqSchema.value) return ''
    return JSON.stringify(faqSchema.value, null, 2)
})

// Inject JSON-LD schema into document head (Vue/Vite doesn't allow script tags in templates)
let schemaScript = null
const SCRIPT_ID = 'faq-schema-json-ld'

const updateSchemaScript = () => {
    // Skip if schema is already in the page (server-side injected)
    const existingServerScript = document.querySelector('script[type="application/ld+json"]:not([data-inertia])')
    if (existingServerScript && existingServerScript.textContent.includes('FAQPage')) {
        // Server-side schema already exists, no need for client-side injection
        return
    }
    
    // Ensure document.head exists
    if (typeof document === 'undefined' || !document.head) {
        return
    }

    // Remove existing client-side script if it exists
    const existingScript = document.getElementById(SCRIPT_ID)
    if (existingScript) {
        existingScript.remove()
        schemaScript = null
    }
    
    // Add new script if schema exists (fallback for client-side only)
    if (faqSchemaJson.value && faqSchemaJson.value.trim() !== '') {
        try {
            schemaScript = document.createElement('script')
            schemaScript.id = SCRIPT_ID
            schemaScript.type = 'application/ld+json'
            schemaScript.textContent = faqSchemaJson.value
            schemaScript.setAttribute('data-inertia', 'true')
            document.head.appendChild(schemaScript)
        } catch (error) {
            console.error('Error injecting FAQ Schema:', error)
        }
    }
}

onMounted(() => {
    // Wait for next tick to ensure DOM and data are ready
    // Only inject if server-side schema wasn't provided
    nextTick(() => {
        updateSchemaScript()
    })
})

onUnmounted(() => {
    const existingScript = document.getElementById(SCRIPT_ID)
    if (existingScript) {
        existingScript.remove()
        schemaScript = null
    }
})

// Watch for changes in FAQs and update schema
watch(() => faqSchemaJson.value, (newValue) => {
    if (newValue && newValue.trim() !== '') {
        nextTick(() => {
            updateSchemaScript()
        })
    }
}, { immediate: true, deep: true })

// Also watch FAQs directly as a fallback
watch(() => faqs.value, () => {
    nextTick(() => {
        updateSchemaScript()
    })
}, { immediate: true, deep: true })
</script>

<script>
import AppLayout from '@/Layouts/App.vue';

export default {
    components: {
        AppLayout,
        CtaTwo
    }
};
</script>

<style scoped>
.faq-page {
    padding: 100px 0;
}

.accrodion {
    margin-bottom: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    overflow: hidden;
    background: var(--techguru-black);
}

.accrodion-title {
    padding: 20px 30px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.accrodion-title:hover {
    background: rgba(255, 255, 255, 0.05);
}

.accrodion-title h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: var(--techguru-white);
}

.accrodion.active .accrodion-title {
    background: rgba(255, 255, 255, 0.05);
}

.accrodion-content {
    padding: 0 30px;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, padding 0.3s ease;
}

.accrodion.active .accrodion-content {
    max-height: 1000px;
    padding: 20px 30px;
}

.accrodion-content__text-1 {
    margin: 0;
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.8;
    font-size: 16px;
}

@media (max-width: 768px) {
    .faq-page {
        padding: 60px 0;
    }

    .accrodion-title {
        padding: 15px 20px;
    }

    .accrodion-title h4 {
        font-size: 16px;
    }

    .accrodion-content {
        padding: 0 20px;
    }

    .accrodion.active .accrodion-content {
        padding: 15px 20px;
    }

    .accrodion-content__text-1 {
        font-size: 14px;
    }
}
</style>
