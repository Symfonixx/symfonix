<template>
    <Head>
        <title>{{ trans("500 Error") }} | {{ seo.website_name }}</title>
    </Head>
    <app-layout>
        <div class="breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg">
            <div class="banner-home__middel-shape inner-top-shape"></div>
            <div class="container">
                <div class="banner-all-shape-wrapper">
                    <div class="banner-home__banner-shape-1 first-shape">
                        <img class="upDown-top" :src="asset_path + 'site/imgs/banner-1/banner-shape-1.svg'" alt="img not found">
                    </div>
                    <div class="banner-home__banner-shape-2 second-shape">
                        <img class="upDown-bottom" :src="asset_path + 'site/imgs/banner-1/banner-shape-2.svg'" alt="img not found">
                    </div>
                    <div class="right-shape">
                        <img class="zooming" :src="asset_path + 'site/imgs/inner-img/inner-right-shape.svg'" alt="img not found">
                    </div>
                </div>
                <div class="row align-items-center justify-content-between">
                    <div class="col-12">
                        <div class="breadcrumb__content text-center">
                            <div class="breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5">
                                <h1 class="breadcrumb__title color-white wow fadeIn animated" data-wow-delay=".1s">{{ trans("500 Error") }}</h1>
                            </div>
                            <div class="breadcrumb__menu wow fadeIn animated" data-wow-delay=".5s">
                                <nav>
                                    <ul>
                                        <li><span><Link :href="getHomeUrl()">{{ trans("Home") }}</Link></span></li>
                                        <li class="active"><span>{{ trans("500 Error") }}</span></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="error section-space">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="error__content">
                           
                            <div class="section__title-wrapper text-center">
                                <h3 class="section__title mb-15 mb-xs-10 wow fadeIn animated" data-wow-delay=".3s">
                                    {{ trans("Internal Server Error") }}
                                </h3>
                                <p class="mb-40 mb-sm-25 mb-xs-20 wow fadeIn animated" data-wow-delay=".5s">
                                    {{ trans("We're sorry, but something went wrong on our end. Please try again later or contact support if the problem persists.") }}
                                </p>

                                <!-- Debug details (only visible when env is NOT production) -->
                                <div
                                    v-if="isNonProduction && (page?.props?.error || page?.props?.trace)"
                                    class="alert alert-danger text-start mb-40"
                                    style="white-space: pre-wrap; word-break: break-word;"
                                >
                                    <strong>Debug Error:</strong>
                                    <div v-if="page?.props?.error">{{ page.props.error }}</div>
                                    <details v-if="page?.props?.trace" class="mt-3">
                                        <summary>Stack trace</summary>
                                        <pre class="mt-2">{{ page.props.trace }}</pre>
                                    </details>
                                </div>

                                <div class="error-btn-wrap">
                                    <Link :href="getHomeUrl()" class="error-btn wow fadeIn animated" data-wow-delay=".7s">
                                        {{ trans("Back To Home Page") }}
                                    </Link>
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
const appEnv = computed(() => page.props.app_env || 'production')
const isNonProduction = computed(() => appEnv.value !== 'production')

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
.breadcrumb__title.color-white,
.breadcrumb__menu a.color-white,
.breadcrumb__menu span.color-white {
    color: #ffffff !important;
}

.error {
    padding: 100px 0;
}

.error__content {
    text-align: center;
}

.error__content-media img {
    max-width: 500px;
    width: 100%;
    height: auto;
    margin: 0 auto;
}

.section__title {
    color: var(--rr-heading-primary);
    font-family: var(--rr-ff-heading);
    font-weight: var(--rr-fw-sbold);
    line-height: var(--rr-lh-h5);
    font-style: normal;
}

.section__title-wrapper p {
    color: #646464;
    font-family: 'Rubik';
    font-style: normal;
    font-weight: 400;
    font-size: 18px;
    line-height: 28px;
}

.error-btn {
    background: var(--rr-heading-primary);
    color: var(--rr-common-white);
    width: 217px;
    height: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: auto;
    border-radius: 4px;
    transition: .3s;
    text-decoration: none;
}

.error-btn:hover {
    background: var(--rr-theme-primary);
    color: var(--rr-common-white);
}
</style>

