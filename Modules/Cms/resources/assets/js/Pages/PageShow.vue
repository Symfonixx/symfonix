<template>
    <Head>
        <title>{{ metaTitle }}</title>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'" />
    </Head>
    <app-layout>


                <!--Page Header Start-->
        <section class="page-header">
            <div class="page-header__bg" :style="{ backgroundImage: `url(${banner})` }">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h1>{{ custom_page.title[locale] }}</h1>
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
                            <li>{{ custom_page.title[locale] }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!--Page Header End-->


        <div class="my-5">
            <div class="container">
                <div class="content mb-10">
                  <div v-html="custom_page.content[locale]"></div>
                </div>
            </div>
        </div>
     </app-layout>
</template>

<script setup>
import {computed} from 'vue'
import {usePage, Head, Link} from '@inertiajs/vue3'

const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const seo = computed(() => page.props.seo)
const custom_page = computed(() => page.props.custom_page)
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale || 'en')
const banner = computed(() => page.props.banner)

const metaTitle = computed(() => {
    const pageTitle = custom_page.value?.title?.[locale.value] || ''
    return `${pageTitle} | ${seo.value.website_name || ''}`.trim()
})
</script>
<script>


import AppLayout from '@/Layouts/App.vue';

export default {
    components: {
        AppLayout
    }

};
</script>
