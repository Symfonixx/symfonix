<template>
    <Head :title="metaTitle">
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'"/>
    </Head>
    <AppLayout>
        <section class="page-header">
            <div class="page-header__bg" :style="{backgroundImage: `url(${asset_path}images/backgrounds/our-team-bg.jpg)`}"></div>
            <div class="container">
                <div class="page-header__inner">
                    <h1>{{ trans('Careers') }}</h1>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><Link :href="route('home')"><i class="fas fa-home"></i>{{ trans('Home') }}</Link></li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li>{{ trans('Careers') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="jobs-page py-5">
            <div class="container">
                <div class="text-center mb-5">
                    <h2>{{ trans('Join Our Team') }}</h2>
                    <p>{{ trans('Explore current opportunities and help us build technology in perfect harmony.') }}</p>
                </div>
                <div class="row g-4">
                    <div v-for="position in positions.data" :key="position.id" class="col-lg-4 col-md-6">
                        <article class="job-card h-100">
                            <span class="job-card__department">{{ position.department }}</span>
                            <h3>{{ position.title }}</h3>
                            <p class="job-card__meta">
                                <span><i class="fas fa-map-marker-alt"></i>{{ position.location }}</span>
                                <span><i class="fas fa-briefcase"></i>{{ formatEmploymentType(position.employment_type) }}</span>
                            </p>
                            <p class="text-muted">{{ trans('Posted') }}: {{ formatDate(position.posted_at) }}</p>
                            <Link :href="route('jobs.show', position.slug)" class="thm-btn">
                                {{ trans('View & Apply') }}
                                <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                            </Link>
                        </article>
                    </div>
                    <div v-if="positions.data.length === 0" class="col-12 text-center py-5">
                        <p class="text-muted">{{ trans('There are no open positions at the moment. Please check back soon.') }}</p>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import {computed} from 'vue'
import {Head, Link, usePage} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/App.vue'

const props = defineProps({positions: {type: Object, required: true}})
const page = usePage()
const trans = (key) => page.props.translations[key] || key
const locale = computed(() => page.props.locale || 'en')
const asset_path = computed(() => page.props.asset_path || '')
const metaTitle = computed(() => page.props.meta?.title || `${trans('Careers')} | ${page.props.seo?.website_name || page.props.appName}`)

const formatDate = (value) => new Intl.DateTimeFormat(locale.value, {year: 'numeric', month: 'long', day: 'numeric'}).format(new Date(`${value}T00:00:00`))
const formatEmploymentType = (value) => trans(String(value || '').replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase()))
</script>

<style scoped>
.jobs-page { background: #101a2f; color: #f4f7fb; }
.jobs-page h2,
.job-card h3 { color: #fff; }
.jobs-page > .container > .text-center > p,
.jobs-page :deep(.text-muted) { color: #aab8d1 !important; }
.job-card { background: #17233d; border: 1px solid #2a3c60; border-radius: 12px; padding: 2rem; box-shadow: 0 8px 24px rgba(0, 0, 0, .2); }
.job-card h3 { font-size: 1.35rem; margin: 1rem 0; }
.job-card__meta { display: flex; flex-wrap: wrap; gap: .75rem 1.25rem; color: #d3dded; font-size: .9rem; }
.job-card__meta span { display: inline-flex; align-items: center; gap: .45rem; }
.job-card .thm-btn { margin-top: 1.25rem; }
.job-card__department { color: var(--techguru-base, #5cb0e9); font-weight: 700; text-transform: uppercase; font-size: .8rem; letter-spacing: .08em; }
</style>
