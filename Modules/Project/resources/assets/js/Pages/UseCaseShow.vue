<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'"/>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/portfolio.css'"/>
        <title>{{ metaTitle }}</title>
        <meta name="description" :content="metaDescription">
    </Head>

    <app-layout>
        <div class="page-header">
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
                                <Link :href="route('portfolio.index')">{{ trans('Portfolio') }}</Link>
                            </li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li>{{ useCase.title }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <section class="portfolio-details">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="portfolio-details__top">
                            <div class="portfolio-details__title-and-social">
                                <h3 class="portfolio-details__top-title" v-html="useCase.title"></h3>
                            </div>
                        </div>

                        <ul class="portfolio-details__portfolio-list list-unstyled mb-5">
                            <li v-if="useCase.client_name">
                                <span>{{ trans('Client') }}</span>
                                <p>{{ useCase.client_name }}</p>
                            </li>
                            <li v-if="useCase.completed_year">
                                <span>{{ trans('Year') }}</span>
                                <p>{{ useCase.completed_year }}</p>
                            </li>
                            <li v-if="useCase.category_tag">
                                <span>{{ trans('Category') }}</span>
                                <p>{{ useCase.category_tag }}</p>
                            </li>
                        </ul>

                        <div class="portfolio-details__img-1 mb-5">
                            <img :src="useCase.image_link" :alt="useCase.title">
                        </div>

                        <p v-if="useCase.summary" class="portfolio-details__text-1">{{ useCase.summary }}</p>

                        <div v-if="useCase.challenge" class="mb-5">
                            <h4 class="portfolio-details__title-1">{{ trans('The Challenge') }}</h4>
                            <p class="portfolio-details__text-1">{{ useCase.challenge }}</p>
                        </div>

                        <div v-if="useCase.solution" class="mb-5">
                            <h4 class="portfolio-details__title-2">{{ trans('Our Solution') }}</h4>
                            <p class="portfolio-details__text-2">{{ useCase.solution }}</p>
                        </div>

                        <div v-if="useCase.results" class="mb-5">
                            <h4 class="portfolio-details__title-2">{{ trans('The Results') }}</h4>
                            <p class="portfolio-details__text-3">{{ useCase.results }}</p>
                        </div>

                        <div v-if="useCase.content" class="content mb-5" v-html="useCase.content"></div>

                        <div v-if="useCase.technologies && useCase.technologies.length" class="mb-5">
                            <h4 class="portfolio-details__title-3">{{ trans('Technologies Used') }}</h4>
                            <div class="d-flex flex-wrap gap-2">
                                <span v-for="tech in useCase.technologies" :key="tech" class="badge bg-primary">{{ tech }}</span>
                            </div>
                        </div>

                        <a v-if="useCase.project_url" :href="useCase.project_url" target="_blank" rel="noopener"
                           class="thm-btn mb-5">
                            {{ trans('Visit Live Project') }}
                            <span class="icon-right-arrow"></span>
                        </a>

                        <div v-if="relatedUseCases.length" class="mt-5">
                            <h4 class="portfolio-details__title-3 mb-4">{{ trans('More Case Studies') }}</h4>
                            <div class="row">
                                <div v-for="item in relatedUseCases" :key="item.id" class="col-md-4 mb-4">
                                    <div class="portfolio-one__single">
                                        <div class="portfolio-one__img-box">
                                            <div class="portfolio-one__img">
                                                <img :src="item.image_link" :alt="item.title">
                                            </div>
                                        </div>
                                        <div class="portfolio-one__content">
                                            <h3 class="portfolio-one__title">
                                                <Link :href="route('portfolio.show', item.slug)">{{ item.title }}</Link>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
const useCase = computed(() => page.props.useCase || {})
const relatedUseCases = computed(() => page.props.relatedUseCases || [])
const meta = computed(() => page.props.meta || {})

const metaTitle = computed(() => meta.value.title || useCase.value.title || '')
const metaDescription = computed(() => meta.value.description || useCase.value.summary || '')
</script>
