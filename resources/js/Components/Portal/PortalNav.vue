<template>
    <nav class="portal-tabs" aria-label="Portal navigation">
        <Link
            :href="route('portal.dashboard')"
            class="portal-tabs__link"
            :class="{ 'portal-tabs__link--active': active === 'dashboard' }"
        >
            <i class="fas fa-th-large"></i>
            {{ t('menu.my_dashboard') }}
        </Link>
        <Link
            :href="route('portal.projects.index')"
            class="portal-tabs__link"
            :class="{ 'portal-tabs__link--active': active === 'projects' }"
        >
            <i class="fas fa-folder-open"></i>
            {{ t('menu.projects') }}
        </Link>
        <Link
            :href="route('portal.subscriptions.index')"
            class="portal-tabs__link"
            :class="{ 'portal-tabs__link--active': active === 'subscriptions' }"
        >
            <i class="fas fa-sync-alt"></i>
            {{ t('menu.subscriptions') }}
        </Link>
        <Link
            :href="route('portal.tickets.index')"
            class="portal-tabs__link"
            :class="{ 'portal-tabs__link--active': active === 'tickets' }"
        >
            <i class="fas fa-life-ring"></i>
            {{ t('menu.tickets') }}
            <span v-if="openTicketsCount" class="portal-tabs__badge">{{ openTicketsCount }}</span>
        </Link>
        <Link
            :href="route('portal.profile.index')"
            class="portal-tabs__link"
            :class="{ 'portal-tabs__link--active': active === 'profile' }"
        >
            <i class="fas fa-user-cog"></i>
            {{ t('menu.profile') }}
        </Link>
    </nav>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { usePortalTranslations } from '@/Composables/usePortalTranslations';

defineProps({
    active: { type: String, default: 'dashboard' },
});

const page = usePage();
const { t } = usePortalTranslations();
const openTicketsCount = computed(() => page.props.portal?.open_tickets || 0);
</script>
