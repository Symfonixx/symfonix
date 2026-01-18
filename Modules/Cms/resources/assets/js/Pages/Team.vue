<template>
    <Head>
             <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'" />
        <title>{{ trans('Our Members') }} | {{ seo.website_name }}</title>
    </Head>
    <app-layout>

        <!--Page Header Start-->
        <section class="page-header">
            <div class="page-header__bg"
                :style="{ backgroundImage: `url(${asset_path}images/contact-header-bg.jpg)` }">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans('Our Members') }}</h2>
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
                            <li>{{ trans('Our Members') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!--Page Header End-->

        <!--Team Page Start-->
        <section class="team-page my-5">
            <div class="team-page__shape-1">
                <img :src="asset_path + 'images/shapes/team-page-shape-1.png'" alt="">
            </div>
            <div class="container">
                <div class="row" v-if="teams && teams.length > 0">
                    <!--Team One Single Start-->
                    <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInLeft" v-for="(team, index) in teams"
                        :key="team.id" :data-wow-delay="`${(index % 4) * 100}ms`">
                        <div class="team-one__single">
                            <div class="team-one__img-box">
                                <div class="team-one__img">
                                    <img :src="team.avatar_link" :alt="translateField(team.name)">
                                </div>
                                <div class="team-one__social-box-inner">
                                    <div class="team-one__social-box">
                                        <div class="team-one__social">
                                            <a v-if="team.facebook" :href="team.facebook" target="_blank">
                                                <span class="icon-facebook"></span>
                                            </a>
                                            <a v-if="team.behance" :href="team.behance" target="_blank">
                                                <span class="icon-dribble"></span>
                                            </a>
                                        </div>
                                        <div class="team-one__social">
                                            <a v-if="team.linked_in" :href="team.linked_in" target="_blank">
                                                <span class="icon-linkedin"></span>
                                            </a>
                                            <a v-if="team.github" :href="team.github" target="_blank">
                                                <span class="icon-github"></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="team-one__title-box">
                                    <h3><a href="#">{{ translateField(team.name) }}</a></h3>
                                    <p>{{ translateField(team.position) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Team One Single End-->
                </div>
                <div v-else class="text-center py-5">
                    <p>{{ trans('No team members found.') }}</p>
                </div>
            </div>
        </section>
        <!--Team Page End-->
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
const teams = computed(() => page.props.teams || [])

const translateField = (value) => {
    if (!value) {
        return '';
    }
    if (typeof value === 'string') {
        return value;
    }
    const loc = locale.value;
    if (typeof value === 'object' && value !== null) {
        if (value[loc]) {
            return value[loc];
        }
    }
    return '';
};

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
