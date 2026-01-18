<template>
    <Head>
        <title>{{ trans('Pricing') }} | {{ seo.website_name }}</title>
    </Head>
    <app-layout>

        <!--Page Header Start-->
        <section class="page-header">
            <div class="page-header__bg"
                :style="{ backgroundImage: `url(${asset_path}images/backgrounds/page-header-bg.jpg)` }">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans('Pricing') }}</h2>
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
                            <li>{{ trans('Pricing') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!--Page Header End-->

        <!-- Pricing One Start -->
        <section class="pricing-one">
            <div class="container">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <div class="section-title__tagline-shape-1"></div>
                        <span class="section-title__tagline">{{ trans('Pricing & Plan') }}</span>
                        <div class="section-title__tagline-shape-2"></div>
                    </div>
                    <h2 class="section-title__title title-animation">
                        {{ trans('How We\'ve') }}
                        <span>{{ trans('Empowered Businesses') }}</span><br>
                        <span>{{ trans('with Innovative') }}</span>
                        {{ trans('Tech Solutions') }}
                    </h2>
                </div>
                <div class="row">
                    <!--Pricing One Single Start-->
                    <div class="col-xl-4 col-lg-6 wow fadeInLeft" data-wow-delay="100ms">
                        <div class="pricing-one__single">
                            <div class="pricing-one__icon">
                                <img :src="asset_path + 'images/icon/pricing-icon-1.png'" alt="">
                            </div>
                            <span class="pricing-one__sub-title">{{ trans('PERSONAL') }}</span>
                            <h2 class="pricing-one__price">$5.60</h2>
                            <p class="pricing-one__text">*{{ trans('free 3 months solutions') }}<br>
                                {{ trans('support') }}*</p>
                            <div class="pricing-one__bdr"></div>
                            <h5 class="pricing-one__points-title">{{ trans('Feature Description') }}</h5>
                            <ul class="pricing-one__points list-unstyled">
                                <li>
                                    <div class="icon">
                                        <span class="icon-tick-inside-circle"></span>
                                    </div>
                                    <p>{{ trans('Custom Website Design') }}</p>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-tick-inside-circle"></span>
                                    </div>
                                    <p>{{ trans('Priority Bug Fixes & Updates') }}</p>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-tick-inside-circle"></span>
                                    </div>
                                    <p>{{ trans('Basic & Technical SEO') }}</p>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="far fa-plus-circle"></span>
                                    </div>
                                    <p>{{ trans('System Integration Support') }}</p>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="far fa-plus-circle"></span>
                                    </div>
                                    <p>{{ trans('24/7 Dedicated IT Assistance') }}</p>
                                </li>
                            </ul>
                            <div class="pricing-one__btn-box">
                                <Link :href="route('contact-us')" class="pricing-one__btn" v-if="typeof route !== 'undefined'">
                                    {{ trans('Choose Plan') }}
                                    <span class="far fa-plus-circle"></span>
                                </Link>
                                <a :href="`/${locale === 'ar' ? 'ar' : ''}/contact-us`" class="pricing-one__btn" v-else>
                                    {{ trans('Choose Plan') }}
                                    <span class="far fa-plus-circle"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--Pricing One Single End-->
                    <!--Pricing One Single Start-->
                    <div class="col-xl-4 col-lg-6 wow fadeInUp" data-wow-delay="200ms">
                        <div class="pricing-one__single active">
                            <div class="pricing-one__icon">
                                <img :src="asset_path + 'images/icon/pricing-icon-1.png'" alt="">
                            </div>
                            <span class="pricing-one__sub-title">{{ trans('ENTERPRISE') }}</span>
                            <h2 class="pricing-one__price">$25.60</h2>
                            <p class="pricing-one__text">*{{ trans('free 6 months solutions') }}<br>
                                {{ trans('support') }}*</p>
                            <div class="pricing-one__bdr"></div>
                            <h5 class="pricing-one__points-title">{{ trans('Feature Description') }}</h5>
                            <ul class="pricing-one__points list-unstyled">
                                <li>
                                    <div class="icon">
                                        <span class="icon-tick-inside-circle"></span>
                                    </div>
                                    <p>{{ trans('Custom Website Design') }}</p>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-tick-inside-circle"></span>
                                    </div>
                                    <p>{{ trans('Priority Bug Fixes & Updates') }}</p>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-tick-inside-circle"></span>
                                    </div>
                                    <p>{{ trans('Basic & Technical SEO') }}</p>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="far fa-plus-circle"></span>
                                    </div>
                                    <p>{{ trans('System Integration Support') }}</p>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="far fa-plus-circle"></span>
                                    </div>
                                    <p>{{ trans('24/7 Dedicated IT Assistance') }}</p>
                                </li>
                            </ul>
                            <div class="pricing-one__btn-box">
                                <Link :href="route('contact-us')" class="pricing-one__btn" v-if="typeof route !== 'undefined'">
                                    {{ trans('Choose Plan') }}
                                    <span class="far fa-plus-circle"></span>
                                </Link>
                                <a :href="`/${locale === 'ar' ? 'ar' : ''}/contact-us`" class="pricing-one__btn" v-else>
                                    {{ trans('Choose Plan') }}
                                    <span class="far fa-plus-circle"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--Pricing One Single End-->
                    <!--Pricing One Single Start-->
                    <div class="col-xl-4 col-lg-6 wow fadeInRight" data-wow-delay="300ms">
                        <div class="pricing-one__single">
                            <div class="pricing-one__icon">
                                <img :src="asset_path + 'images/icon/pricing-icon-1.png'" alt="">
                            </div>
                            <span class="pricing-one__sub-title">{{ trans('Premium') }}</span>
                            <h2 class="pricing-one__price">$120.60</h2>
                            <p class="pricing-one__text">*{{ trans('free 12 months solutions') }}<br>
                                {{ trans('support') }}*</p>
                            <div class="pricing-one__bdr"></div>
                            <h5 class="pricing-one__points-title">{{ trans('Feature Description') }}</h5>
                            <ul class="pricing-one__points list-unstyled">
                                <li>
                                    <div class="icon">
                                        <span class="icon-tick-inside-circle"></span>
                                    </div>
                                    <p>{{ trans('Custom Website Design') }}</p>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-tick-inside-circle"></span>
                                    </div>
                                    <p>{{ trans('Priority Bug Fixes & Updates') }}</p>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-tick-inside-circle"></span>
                                    </div>
                                    <p>{{ trans('Basic & Technical SEO') }}</p>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="far fa-plus-circle"></span>
                                    </div>
                                    <p>{{ trans('System Integration Support') }}</p>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="far fa-plus-circle"></span>
                                    </div>
                                    <p>{{ trans('24/7 Dedicated IT Assistance') }}</p>
                                </li>
                            </ul>
                            <div class="pricing-one__btn-box">
                                <Link :href="route('contact-us')" class="pricing-one__btn" v-if="typeof route !== 'undefined'">
                                    {{ trans('Choose Plan') }}
                                    <span class="far fa-plus-circle"></span>
                                </Link>
                                <a :href="`/${locale === 'ar' ? 'ar' : ''}/contact-us`" class="pricing-one__btn" v-else>
                                    {{ trans('Choose Plan') }}
                                    <span class="far fa-plus-circle"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--Pricing One Single End-->
                </div>
            </div>
        </section>
        <!-- Pricing One End -->

        <PartnersBrand />
    </app-layout>
</template>

<script setup>
import { computed, onMounted, nextTick } from 'vue'
import { Link, usePage, Head } from '@inertiajs/vue3'
import PartnersBrand from '@/Components/PartnersBrand.vue'

const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const seo = computed(() => page.props.seo)
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale)

onMounted(() => {
    nextTick(() => {
        // Initialize WOW animations
        if (typeof WOW !== 'undefined') {
            new WOW().init();
        }
    });
});
</script>

<script>
import AppLayout from '@/Layouts/App.vue';

export default {
    components: {
        AppLayout
    }
};
</script>
