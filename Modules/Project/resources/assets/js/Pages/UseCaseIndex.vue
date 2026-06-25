<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'"/>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/portfolio.css'"/>
        <title>{{ metaTitle }}</title>
        <meta name="description" :content="metaDescription">
        <meta name="keywords" :content="metaKeywords">
    </Head>

    <app-layout>
        <div class="page-header">
            <div class="page-header__bg"
                 :style="{ backgroundImage: `url(${asset_path}images/backgrounds/page-header-bg.jpg)` }">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans('Portfolio') }}</h2>
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
                            <li>{{ trans('Portfolio') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <section class="portfolio-page">
            <h2 class="portfolio-one__big-text">portfolio</h2>
            <div class="container">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <div class="section-title__tagline-shape-1"></div>
                        <span class="section-title__tagline">{{ trans('Portfolio') }}</span>
                        <div class="section-title__tagline-shape-2"></div>
                    </div>
                    <h2 class="section-title__title title-animation">
                        {{ trans('How We\'ve Empowered Businesses with Innovative Tech Solutions') }}
                    </h2>
                </div>

                <div class="portfolio-one__bottom">
                    <div class="row">
                        <div
                            v-for="item in useCases.data"
                            :key="item.id"
                            class="col-xl-4 col-lg-6 col-md-6"
                        >
                            <div class="portfolio-one__single">
                                <div class="portfolio-one__img-box">
                                    <div class="portfolio-one__img">
                                        <img :src="item.image_link" :alt="item.title">
                                        <div v-if="item.category_tag || (item.technologies && item.technologies.length)" class="portfolio-one__tag">
                                            <span v-if="item.category_tag">{{ item.category_tag }}</span>
                                            <span v-for="tech in (item.technologies || []).slice(0, 2)" :key="tech">{{ tech }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="portfolio-one__content">
                                    <div class="portfolio-one__title-box">
                                        <h3 class="portfolio-one__title">
                                            <Link :href="route('portfolio.show', item.slug)">{{ item.title }}</Link>
                                        </h3>
                                        <p class="portfolio-one__text">{{ item.summary }}</p>
                                    </div>
                                    <div class="portfolio-one__arrow">
                                        <Link :href="route('portfolio.show', item.slug)">
                                            <span class="icon-right-arrow"></span>
                                        </Link>
                                    </div>
                                    <div v-if="item.completed_year" class="portfolio-one__year">
                                        <span>{{ item.completed_year }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="!useCases.data.length" class="text-center py-5">
                        <h3 class="text-muted">{{ trans('No records found') }}</h3>
                    </div>

                    <div v-if="useCases.last_page > 1" class="blog-page__pagination mt-5">
                        <ul class="pg-pagination list-unstyled">
                            <li v-if="useCases.prev_page_url" class="prev">
                                <Link :href="useCases.prev_page_url"><span class="icon-left-arrow-1"></span></Link>
                            </li>
                            <template v-for="(link, index) in useCases.links" :key="index">
                                <li v-if="link.url && index > 0 && index < useCases.links.length - 1"
                                    :class="['count', link.active ? 'active' : '']">
                                    <Link :href="link.url">{{ link.label }}</Link>
                                </li>
                            </template>
                            <li v-if="useCases.next_page_url" class="next">
                                <Link :href="useCases.next_page_url">
                                    <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </app-layout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const trans = (key) => page.props.translations[key] || key
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale)
const useCases = computed(() => page.props.useCases || { data: [] })
const meta = computed(() => page.props.meta || {})

const metaTitle = computed(() => meta.value.title || trans('Portfolio'))
const metaDescription = computed(() => meta.value.description || '')
const metaKeywords = computed(() => meta.value.keywords || '')
</script>
