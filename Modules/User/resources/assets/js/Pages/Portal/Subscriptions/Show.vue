<template>
    <portal-shell
        :title="subscription.name"
        :subtitle="subscription.company?.name"
        active="subscriptions"
        :breadcrumbs="[
            { label: t('menu.my_dashboard'), href: route('portal.dashboard') },
            { label: t('subscriptions.title'), href: route('portal.subscriptions.index') },
            { label: subscription.name },
        ]"
        :meta-title="metaTitle"
        :meta-description="metaDescription"
    >
        <div class="portal-grid portal-grid--show">
            <div>
                <div class="portal-panel">
                    <div class="portal-panel__header">
                        <h2 class="portal-panel__title">{{ t('subscriptions.details') }}</h2>
                    </div>
                    <div class="portal-panel__body">
                        <div class="portal-details">
                            <div class="portal-details__row">
                                <div class="portal-details__label">{{ t('fields.status') }}</div>
                                <div class="portal-details__value">
                                    <span class="portal-badge" :class="subscriptionBadgeClass(subscription.status)">
                                        {{ subscription.status_label }}
                                    </span>
                                </div>
                            </div>
                            <div v-if="subscription.company?.name" class="portal-details__row">
                                <div class="portal-details__label">{{ t('fields.company') }}</div>
                                <div class="portal-details__value">{{ subscription.company.name }}</div>
                            </div>
                            <div class="portal-details__row">
                                <div class="portal-details__label">{{ t('subscriptions.amount') }}</div>
                                <div class="portal-details__value">
                                    {{ formatMoney(subscription.amount, subscription.currency) }}
                                </div>
                            </div>
                            <div class="portal-details__row">
                                <div class="portal-details__label">{{ t('subscriptions.billing_cycle') }}</div>
                                <div class="portal-details__value">{{ subscription.billing_cycle_label }}</div>
                            </div>
                            <div v-if="subscription.service_name" class="portal-details__row">
                                <div class="portal-details__label">{{ t('subscriptions.service') }}</div>
                                <div class="portal-details__value">{{ subscription.service_name }}</div>
                            </div>
                            <div v-if="subscription.starts_at" class="portal-details__row">
                                <div class="portal-details__label">{{ t('subscriptions.starts_at') }}</div>
                                <div class="portal-details__value">{{ subscription.starts_at }}</div>
                            </div>
                            <div v-if="subscription.ends_at" class="portal-details__row">
                                <div class="portal-details__label">{{ t('subscriptions.ends_at') }}</div>
                                <div class="portal-details__value">{{ subscription.ends_at }}</div>
                            </div>
                            <div v-if="subscription.renewal_at" class="portal-details__row">
                                <div class="portal-details__label">{{ t('subscriptions.renewal_at') }}</div>
                                <div class="portal-details__value">{{ subscription.renewal_at }}</div>
                            </div>
                            <div v-if="subscription.auto_renew" class="portal-details__row">
                                <div class="portal-details__label">{{ t('subscriptions.auto_renew') }}</div>
                                <div class="portal-details__value">{{ t('subscriptions.auto_renew_enabled') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="portal-grid__stack">
                <div class="portal-panel">
                    <div class="portal-panel__header">
                        <h2 class="portal-panel__title">{{ t('subscriptions.invoices') }}</h2>
                    </div>
                    <div class="portal-panel__body">
                        <div v-if="invoiceList.length === 0" class="portal-empty">
                            <span class="portal-empty__icon"><i class="fas fa-file-alt"></i></span>
                            {{ t('subscriptions.no_invoices') }}
                        </div>

                        <div v-else class="portal-invoice-list">
                            <article v-for="invoice in invoiceList" :key="invoice.id" class="portal-invoice-card">
                                <div class="portal-invoice-card__top">
                                    <div>
                                        <div class="portal-invoice-card__number">{{ invoice.invoice_number }}</div>
                                        <span
                                            class="portal-badge mt-2"
                                            :class="invoiceBadgeClass(invoice.status)"
                                        >
                                            {{ invoiceStatusLabel(invoice.status) }}
                                        </span>
                                    </div>
                                    <div class="portal-invoice-card__total">
                                        {{ formatMoney(invoice.total, invoice.currency) }}
                                    </div>
                                </div>
                                <div class="portal-invoice-card__meta">
                                    <div class="portal-invoice-card__meta-item">
                                        <span>{{ t('fields.issued_at') }}</span>
                                        <strong>{{ invoice.issued_at || '—' }}</strong>
                                    </div>
                                    <div class="portal-invoice-card__meta-item">
                                        <span>{{ t('fields.due_at') }}</span>
                                        <strong>{{ invoice.due_at || '—' }}</strong>
                                    </div>
                                    <div class="portal-invoice-card__meta-item">
                                        <span>{{ t('fields.paid_at') }}</span>
                                        <strong>{{ invoice.paid_at || '—' }}</strong>
                                    </div>
                                </div>
                                <a :href="invoice.pdf_url" class="portal-panel__action" target="_blank" rel="noopener">
                                    <i class="fas fa-download"></i>
                                    {{ t('subscriptions.download_pdf') }}
                                </a>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </portal-shell>
</template>

<script setup>
import { computed } from 'vue';
import PortalShell from '@/Components/Portal/PortalShell.vue';
import { usePortalTranslations } from '@/Composables/usePortalTranslations';

const props = defineProps({
    subscription: { type: Object, required: true },
    invoices: { type: Array, default: () => [] },
    meta: { type: Object, default: () => ({}) },
});

const { t, invoiceStatusLabel } = usePortalTranslations();

const metaTitle = computed(() => props.meta?.title || props.subscription.name);
const metaDescription = computed(() => props.meta?.description || t('pages.subscriptions_description'));

const invoiceList = computed(() => {
    if (Array.isArray(props.invoices)) {
        return props.invoices;
    }

    return Object.values(props.invoices ?? {});
});

const formatMoney = (amount, currency) => `${Number(amount ?? 0).toFixed(2)} ${currency || ''}`.trim();

const subscriptionBadgeClass = (status) => {
    if (status === 'active' || status === 'trial') return 'portal-badge--paid';
    if (status === 'cancelled' || status === 'expired') return 'portal-badge--overdue';
    return 'portal-badge--invoice';
};

const invoiceBadgeClass = (status) => {
    if (status === 'paid') return 'portal-badge--paid';
    if (status === 'overdue') return 'portal-badge--overdue';
    return 'portal-badge--invoice';
};
</script>
