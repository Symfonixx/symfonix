<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'" />
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/portal.css'" />
        <title>{{ pageTitle }}</title>
        <meta name="description" :content="pageDescription">
        <meta name="robots" content="noindex, nofollow">
    </Head>

    <app-layout>
        <section class="page-header portal-page-header">
            <div class="page-header__bg" :style="{ backgroundImage: `url(${asset_path}images/backgrounds/login-bg.jpg)` }"></div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ title }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li v-for="item in flatBreadcrumbs" :key="item.key">
                                <span v-if="item.type === 'separator'" :class="arrowClass"></span>
                                <Link v-else-if="item.type === 'link'" :href="item.href">
                                    <i v-if="item.home" class="fas fa-home"></i>{{ item.label }}
                                </Link>
                                <template v-else>{{ item.label }}</template>
                            </li>
                        </ul>
                    </div>
                    <p v-if="subtitle" class="portal-page-header__subtitle">{{ subtitle }}</p>
                </div>
            </div>
        </section>

        <section class="portal-one">
            <div class="container">
                <portal-nav :active="active" />
                <slot />
            </div>
        </section>
    </app-layout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/App.vue';
import PortalNav from '@/Components/Portal/PortalNav.vue';

const props = defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    active: { type: String, default: 'dashboard' },
    breadcrumbs: { type: Array, default: () => [] },
    metaTitle: { type: String, default: '' },
    metaDescription: { type: String, default: '' },
});

const page = usePage();
const locale = computed(() => page.props.locale);
const asset_path = computed(() => page.props.asset_path || '');
const seo = computed(() => page.props.seo || {});

const homeLabels = { en: 'Home', ar: 'الرئيسية', tr: 'Ana Sayfa' };
const homeLabel = computed(() => homeLabels[locale.value] || homeLabels.en);
const arrowClass = computed(() => `icon-${locale.value === 'ar' ? 'left' : 'right'}-arrow-1`);

const flatBreadcrumbs = computed(() => {
    const items = [
        {
            key: 'home',
            type: 'link',
            label: homeLabel.value,
            href: route('home'),
            home: true,
        },
    ];

    props.breadcrumbs.forEach((crumb, index) => {
        items.push({ key: `sep-${index}`, type: 'separator' });

        if (crumb.href) {
            items.push({
                key: `link-${index}`,
                type: 'link',
                label: crumb.label,
                href: crumb.href,
            });
        } else {
            items.push({
                key: `text-${index}`,
                type: 'text',
                label: crumb.label,
            });
        }
    });

    return items;
});

const pageTitle = computed(() => {
    const title = props.metaTitle || props.title;
    return `${title} | ${seo.value.website_name || ''}`.trim();
});
const pageDescription = computed(() => props.metaDescription || props.subtitle || '');
</script>
