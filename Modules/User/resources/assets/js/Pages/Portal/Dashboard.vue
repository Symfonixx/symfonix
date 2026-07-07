<template>
    <portal-shell
        :title="t('menu.my_dashboard')"
        :subtitle="welcomeSubtitle"
        active="dashboard"
        :breadcrumbs="[{ label: t('menu.my_dashboard') }]"
        :meta-title="metaTitle"
        :meta-description="metaDescription"
    >
        <div class="portal-stats">
            <div class="portal-stat">
                <span class="portal-stat__icon"><i class="fas fa-briefcase"></i></span>
                <span class="portal-stat__label">{{ t('dashboard.total_projects') }}</span>
                <span class="portal-stat__value">{{ stats.total_projects }}</span>
            </div>
            <div class="portal-stat">
                <span class="portal-stat__icon"><i class="fas fa-spinner"></i></span>
                <span class="portal-stat__label">{{ t('dashboard.active_projects') }}</span>
                <span class="portal-stat__value">{{ stats.active_projects }}</span>
            </div>
            <div class="portal-stat">
                <span class="portal-stat__icon"><i class="fas fa-bell"></i></span>
                <span class="portal-stat__label">{{ t('menu.notifications') }}</span>
                <span class="portal-stat__value">{{ stats.unread_notifications }}</span>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="portal-panel">
                    <div class="portal-panel__header">
                        <h2 class="portal-panel__title">{{ t('dashboard.recent_projects') }}</h2>
                        <Link :href="route('portal.projects.index')" class="portal-panel__action">
                            {{ t('dashboard.view_all_projects') }}
                            <i :class="`fas fa-arrow-${locale === 'ar' ? 'left' : 'right'}`"></i>
                        </Link>
                    </div>
                    <div class="portal-panel__body">
                        <div v-if="projects.length === 0" class="portal-empty">
                            <i class="fas fa-folder-open"></i>
                            {{ t('dashboard.no_projects') }}
                        </div>
                        <div v-else class="portal-table-wrap">
                            <table class="portal-table">
                                <thead>
                                    <tr>
                                        <th>{{ t('fields.status') }}</th>
                                        <th>{{ t('projects.title') }}</th>
                                        <th>{{ t('fields.remaining') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="project in projects" :key="project.id">
                                        <td>
                                            <span
                                                class="portal-badge"
                                                :style="{ backgroundColor: (project.status?.color_code || '#6c757d') + '33', color: project.status?.color_code || '#C5C8CD' }"
                                            >
                                                {{ project.status?.name }}
                                            </span>
                                        </td>
                                        <td>{{ project.title }}</td>
                                        <td>{{ formatMoney(project.collection.remaining, project.collection.currency) }}</td>
                                        <td class="text-end">
                                            <Link :href="route('portal.projects.show', project.id)" class="thm-btn" style="padding: 10px 20px; font-size: 14px;">
                                                {{ t('projects.view_details') }}
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="portal-panel">
                    <div class="portal-panel__header">
                        <h2 class="portal-panel__title">{{ t('dashboard.recent_notifications') }}</h2>
                        <button
                            v-if="notifications.length"
                            type="button"
                            class="portal-link-muted"
                            @click="markAllRead"
                        >
                            {{ t('notifications.mark_all_read') }}
                        </button>
                    </div>
                    <div class="portal-panel__body">
                        <div v-if="notifications.length === 0" class="portal-empty">
                            <i class="fas fa-bell-slash"></i>
                            {{ t('dashboard.no_notifications') }}
                        </div>
                        <div v-else class="portal-notifications">
                            <button
                                v-for="notification in notifications"
                                :key="notification.id"
                                type="button"
                                class="portal-notification"
                                :class="{ 'portal-notification--unread': !notification.read_at }"
                                @click="openNotification(notification)"
                            >
                                <div class="portal-notification__inner">
                                    <span class="portal-notification__dot"></span>
                                    <div>
                                        <div class="portal-notification__message">{{ notification.message }}</div>
                                        <div class="portal-notification__time">{{ notification.created_at }}</div>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </portal-shell>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import PortalShell from '@/Components/Portal/PortalShell.vue';
import { usePortalTranslations } from '@/Composables/usePortalTranslations';

const props = defineProps({
    projects: { type: Array, default: () => [] },
    stats: { type: Object, required: true },
    notifications: { type: Array, default: () => [] },
    meta: { type: Object, default: () => ({}) },
});

const page = usePage();
const { t } = usePortalTranslations();
const locale = computed(() => page.props.locale);
const auth = computed(() => page.props.auth);
const metaTitle = computed(() => props.meta?.title || t('pages.dashboard_title'));
const metaDescription = computed(() => props.meta?.description || t('pages.dashboard_description'));
const welcomeSubtitle = computed(() => `${t('dashboard.welcome', { name: auth.value?.name })} — ${t('dashboard.subtitle')}`);

const formatMoney = (amount, currency) => `${Number(amount).toFixed(2)} ${currency || ''}`.trim();

const markAllRead = () => router.post(route('portal.notifications.read-all'));

const openNotification = (notification) => router.post(route('portal.notifications.read', notification.id));
</script>
