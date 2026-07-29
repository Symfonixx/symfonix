<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/portal.css'" />
        <title>{{ pageTitle }}</title>
        <meta name="description" :content="pageDescription">
        <meta name="robots" content="noindex, nofollow">
    </Head>

    <div class="portal-app" :class="{ 'portal-app--nav-open': navOpen }">
        <div
            class="portal-app__overlay"
            :class="{ 'portal-app__overlay--visible': navOpen }"
            @click="navOpen = false"
        ></div>

        <portal-nav
            :active="active"
            :open="navOpen"
            @close="navOpen = false"
        />

        <div class="portal-app__main">
            <header class="portal-topbar">
                <div class="portal-topbar__start">
                    <button
                        type="button"
                        class="portal-topbar__menu d-lg-none"
                        :aria-label="t('menu.open_menu')"
                        :aria-expanded="navOpen"
                        @click="navOpen = true"
                    >
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="portal-topbar__titles">
                        <nav class="portal-topbar__crumbs" aria-label="Breadcrumb">
                            <ol>
                                <li v-for="item in flatBreadcrumbs" :key="item.key">
                                    <span v-if="item.type === 'separator'" class="portal-topbar__sep" aria-hidden="true">/</span>
                                    <Link v-else-if="item.type === 'link'" :href="item.href">{{ item.label }}</Link>
                                    <span v-else aria-current="page">{{ item.label }}</span>
                                </li>
                            </ol>
                        </nav>
                        <h1 class="portal-topbar__title">{{ title }}</h1>
                        <p v-if="subtitle" class="portal-topbar__subtitle">{{ subtitle }}</p>
                    </div>
                </div>
                <div class="portal-topbar__end">
                    <Link
                        :href="route('portal.profile.index')"
                        class="portal-topbar__user"
                        :title="auth?.name"
                    >
                        <span class="portal-topbar__avatar">
                            <img v-if="auth?.avatar" :src="auth.avatar" :alt="auth?.name || ''">
                            <i v-else class="fas fa-user"></i>
                        </span>
                        <span class="portal-topbar__user-name d-none d-md-inline">{{ auth?.name }}</span>
                    </Link>
                </div>
            </header>

            <div class="portal-app__content">
                <slot />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import PortalNav from '@/Components/Portal/PortalNav.vue';
import { usePortalTranslations } from '@/Composables/usePortalTranslations';

const props = defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    active: { type: String, default: 'dashboard' },
    breadcrumbs: { type: Array, default: () => [] },
    metaTitle: { type: String, default: '' },
    metaDescription: { type: String, default: '' },
});

const page = usePage();
const { t } = usePortalTranslations();
const navOpen = ref(false);

const asset_path = computed(() => page.props.asset_path || '');
const seo = computed(() => page.props.seo || {});
const auth = computed(() => page.props.auth);

const portalHomeLabel = computed(() => t('menu.dashboard'));

const flatBreadcrumbs = computed(() => {
    const items = [
        {
            key: 'portal',
            type: 'link',
            label: portalHomeLabel.value,
            href: route('portal.dashboard'),
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

watch(navOpen, (open) => {
    document.body.classList.toggle('portal-nav-locked', open);
});

watch(() => page.url, () => {
    navOpen.value = false;
});

onMounted(() => {
    document.body.classList.add('portal-panel-active');
});

onUnmounted(() => {
    document.body.classList.remove('portal-nav-locked');
    document.body.classList.remove('portal-panel-active');
});
</script>
