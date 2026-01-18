<template>
    <Head>
        <title>{{ trans("404 Error") }} | {{ seo.website_name }}</title>
    </Head>
    <app-layout>
        <section class="page-header">
            <div class="page-header__bg" style="background-image: url(assets/images/backgrounds/page-header-bg.jpg);">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans("404 Error") }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><Link :href="getHomeUrl()"><i class="fas fa-home"></i>{{ trans("Home") }}</Link></li>
                            <li><span class="icon-right-arrow-1"></span></li>
                            <li>{{ trans("404 Error") }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!--Start Error Page-->
        <section class="error-page">
            <div class="container">
                <div class="error-page__inner text-center">
                    <div class="error-page__img float-bob-y">
                        <img src="assets/images/resources/error-page-img1.png" alt="">
                    </div>

                    <div class="error-page__content">
                        <h2>{{ trans("Oops! Page Not Found!") }}</h2>
                        <p>{{ trans("The page you are looking for does not exist. It might have been moved or deleted.") }}</p>
                        <div class="btn-box">
                            <Link class="thm-btn" :href="getHomeUrl()">{{ trans("Back To Home") }} <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow `"></span></Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--End Error Page-->
    </app-layout>
</template>

<script setup>
import {computed} from 'vue'
import {usePage, Link, Head} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/App.vue'

const page = usePage()
const trans = (key) => {
    try {
        return page.props.translations?.[key] || key;
    } catch (e) {
        return key;
    }
}
const seo = computed(() => page.props.seo || { website_name: 'Sham Vision' })
const asset_path = computed(() => page.props.asset_path || '/')

// Helper function to safely get home URL
const getHomeUrl = () => {
    try {
        return route('home');
    } catch (e) {
        return '/';
    }
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
.thm-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>

