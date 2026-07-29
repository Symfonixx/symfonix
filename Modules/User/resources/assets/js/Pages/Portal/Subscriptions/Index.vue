<template>
    <portal-shell
        :title="t('subscriptions.title')"
        :subtitle="t('subscriptions.subtitle')"
        active="subscriptions"
        :breadcrumbs="[
            { label: t('menu.my_dashboard'), href: route('portal.dashboard') },
            { label: t('subscriptions.title') },
        ]"
        :meta-title="metaTitle"
        :meta-description="metaDescription"
    >
        <div v-if="subscriptions.data.length === 0" class="portal-panel">
            <div class="portal-empty">
                <i class="fas fa-sync-alt"></i>
                {{ t('subscriptions.no_subscriptions') }}
            </div>
        </div>

        <div v-else class="row g-4">
            <div v-for="subscription in subscriptions.data" :key="subscription.id" class="col-md-6 col-xl-4">
                <article class="portal-project-card">
                    <div class="portal-project-card__top">
                        <h3 class="portal-project-card__title">{{ subscription.name }}</h3>
                        <span class="portal-badge" :class="subscriptionBadgeClass(subscription.status)">
                            {{ subscription.status_label }}
                        </span>
                    </div>

                    <div v-if="subscription.company?.name" class="portal-project-card__company">
                        <i class="fas fa-building me-1"></i>{{ subscription.company.name }}
                    </div>

                    <div class="portal-project-card__meta">
                        <span>{{ t('subscriptions.amount') }}</span>
                        <strong>{{ formatMoney(subscription.amount, subscription.currency) }}</strong>
                    </div>
                    <div class="portal-project-card__meta">
                        <span>{{ t('subscriptions.billing_cycle') }}</span>
                        <strong>{{ subscription.billing_cycle_label }}</strong>
                    </div>
                    <div class="portal-project-card__meta" v-if="subscription.renewal_at">
                        <span>{{ t('subscriptions.renewal_at') }}</span>
                        <strong>{{ subscription.renewal_at }}</strong>
                    </div>
                    <div class="portal-project-card__meta" v-if="subscription.service_name">
                        <span>{{ t('subscriptions.service') }}</span>
                        <strong>{{ subscription.service_name }}</strong>
                    </div>
                    <div class="portal-project-card__meta" v-if="subscription.auto_renew">
                        <span>{{ t('subscriptions.auto_renew') }}</span>
                        <strong>{{ t('subscriptions.auto_renew_enabled') }}</strong>
                    </div>

                    <div class="portal-project-card__footer">
                        <Link :href="route('portal.subscriptions.show', subscription.id)" class="thm-btn w-100 text-center">
                            {{ t('subscriptions.view_details') }}
                            <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                        </Link>
                    </div>
                </article>
            </div>
        </div>

        <nav v-if="subscriptions.links?.length > 3" class="portal-pagination" aria-label="Pagination">
            <Link
                v-for="link in subscriptions.links"
                :key="link.label"
                :href="link.url || '#'"
                class="portal-pagination__link"
                :class="{ 'portal-pagination__link--active': link.active }"
                v-html="link.label"
            />
        </nav>
    </portal-shell>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import PortalShell from '@/Components/Portal/PortalShell.vue';
import { usePortalTranslations } from '@/Composables/usePortalTranslations';

const props = defineProps({
    subscriptions: { type: Object, required: true },
    meta: { type: Object, default: () => ({}) },
});

const page = usePage();
const { t } = usePortalTranslations();
const locale = computed(() => page.props.locale);
const metaTitle = computed(() => props.meta?.title || t('pages.subscriptions_title'));
const metaDescription = computed(() => props.meta?.description || t('pages.subscriptions_description'));

const formatMoney = (amount, currency) => `${Number(amount ?? 0).toFixed(2)} ${currency || ''}`.trim();

const subscriptionBadgeClass = (status) => {
    if (status === 'active' || status === 'trial') return 'portal-badge--paid';
    if (status === 'cancelled' || status === 'expired') return 'portal-badge--overdue';
    return 'portal-badge--invoice';
};
</script>
