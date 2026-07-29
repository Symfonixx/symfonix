<template>
    <aside class="portal-sidebar" :class="{ 'portal-sidebar--open': open }" aria-label="Portal navigation">
        <div class="portal-sidebar__brand">
            <Link :href="route('home')" class="portal-sidebar__logo" :title="brandName">
                <img v-if="logoSrc" :src="logoSrc" :alt="brandName">
                <span v-else class="portal-sidebar__logo-text">{{ brandName }}</span>
            </Link>
            <button
                type="button"
                class="portal-sidebar__close d-lg-none"
                :aria-label="t('menu.close_menu')"
                @click="$emit('close')"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        <p class="portal-sidebar__label">{{ t('menu.navigation') }}</p>

        <nav class="portal-sidebar__nav">
            <Link
                v-for="item in items"
                :key="item.key"
                :href="item.href"
                class="portal-sidebar__link"
                :class="{ 'portal-sidebar__link--active': active === item.key }"
                @click="$emit('close')"
            >
                <span class="portal-sidebar__link-icon">
                    <i :class="item.icon"></i>
                </span>
                <span class="portal-sidebar__link-text">{{ item.label }}</span>
                <span v-if="item.badge" class="portal-sidebar__badge">{{ item.badge }}</span>
            </Link>
        </nav>

        <div class="portal-sidebar__footer">
            <div class="portal-sidebar__user">
                <span class="portal-sidebar__avatar">
                    <img v-if="auth?.avatar" :src="auth.avatar" :alt="auth?.name || ''">
                    <i v-else class="fas fa-user"></i>
                </span>
                <div class="portal-sidebar__user-meta">
                    <strong>{{ auth?.name }}</strong>
                    <span>{{ auth?.email }}</span>
                </div>
            </div>
            <Link
                :href="route('logout')"
                method="post"
                as="button"
                type="button"
                class="portal-sidebar__logout"
            >
                <i class="fas fa-sign-out-alt"></i>
                {{ t('menu.logout') }}
            </Link>
            <Link :href="route('home')" class="portal-sidebar__site-link" @click="$emit('close')">
                <i class="fas fa-external-link-alt"></i>
                {{ t('menu.back_to_site') }}
            </Link>
        </div>
    </aside>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { usePortalTranslations } from '@/Composables/usePortalTranslations';

defineProps({
    active: { type: String, default: 'dashboard' },
    open: { type: Boolean, default: false },
});

defineEmits(['close']);

const page = usePage();
const { t } = usePortalTranslations();

const auth = computed(() => page.props.auth);
const settings = computed(() => page.props.settings || {});
const storage_path = computed(() => page.props.storage_path || '');
const seo = computed(() => page.props.seo || {});
const brandName = computed(() => seo.value.website_name || page.props.appName || 'Symfonix');
const unreadCount = computed(() => page.props.portal?.unread_notifications || 0);
const openTicketsCount = computed(() => page.props.portal?.open_tickets || 0);

const logoSrc = computed(() => {
    const logo = settings.value?.site_logo;
    if (!logo || logo === false || logo === 'false' || logo === 'default.jpg') {
        return '';
    }
    if (/^https?:\/\//i.test(logo) || String(logo).startsWith('//') || String(logo).startsWith('/')) {
        return logo;
    }
    return `${storage_path.value}${logo}`;
});

const items = computed(() => [
    {
        key: 'dashboard',
        href: route('portal.dashboard'),
        icon: 'fas fa-th-large',
        label: t('menu.my_dashboard'),
    },
    {
        key: 'projects',
        href: route('portal.projects.index'),
        icon: 'fas fa-folder-open',
        label: t('menu.projects'),
        badge: unreadCount.value || null,
    },
    {
        key: 'subscriptions',
        href: route('portal.subscriptions.index'),
        icon: 'fas fa-sync-alt',
        label: t('menu.subscriptions'),
    },
    {
        key: 'tickets',
        href: route('portal.tickets.index'),
        icon: 'fas fa-life-ring',
        label: t('menu.tickets'),
        badge: openTicketsCount.value || null,
    },
    {
        key: 'profile',
        href: route('portal.profile.index'),
        icon: 'fas fa-user-cog',
        label: t('menu.profile'),
    },
]);
</script>
