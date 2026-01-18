<template>
    <Head>
                <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'" />
        <title>{{custom_page.title[locale]}} | {{seo.website_name}}</title>
    </Head>
    <app-layout>


                <!--Page Header Start-->
        <section class="page-header">
            <div class="page-header__bg" :style="{ backgroundImage: `url(${banner})` }">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ custom_page.title[locale] }}</h2>
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
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`""></span></li>
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
import {computed, onMounted, nextTick, ref} from 'vue'
import {usePage, useForm, router, Head, Link} from '@inertiajs/vue3'

const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const seo = computed(() => page.props.seo)
const custom_page = computed(() => page.props.custom_page)
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale || 'en')
const banner = computed(() => page.props.banner)
</script>
<script>


import AppLayout from '@/Layouts/App.vue';

export default {
    components: {
        AppLayout
    }

};
</script>

<style scoped>


/* Page banner styling */
.page-banner {
    position: relative;
    height: 600px !important;
    min-height: 600px !important;
    padding: 0 !important;
    display: flex;
    align-items: center;
    background-size: cover !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
    background-attachment: fixed;
}

.breadcrumb__area {
    position: relative;
    min-height: 600px !important;
    height: 600px !important;
}

.banner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.4);
    z-index: 1;
    pointer-events: none;
}

.breadcrumb__area .container {
    position: relative;
    z-index: 2;
    height: 100%;
    display: flex;
    align-items: center;
}

.breadcrumb__area .banner-home__middel-shape {
    position: relative;
    z-index: 2;
}


/* Fix white spots - ensure overlay covers everything with ::before as backup */
.breadcrumb__area::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.3);
    z-index: 1;
    pointer-events: none;
}
</style>
