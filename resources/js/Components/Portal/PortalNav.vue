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
            <span v-if="unreadCount" class="portal-tabs__badge">{{ unreadCount }}</span>
        </Link>
        <Link
            :href="route('portal.subscriptions.index')"
            class="portal-tabs__link"
            :class="{ 'portal-tabs__link--active': active === 'subscriptions' }"
        >
            <i class="fas fa-sync-alt"></i>
            {{ t('menu.subscriptions') }}
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
const unreadCount = computed(() => page.props.portal?.unread_notifications || 0);
</script>
