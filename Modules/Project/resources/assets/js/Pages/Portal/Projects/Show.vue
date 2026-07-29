<template>
    <portal-shell
        :title="project.title"
        :subtitle="project.company?.name"
        active="projects"
        :breadcrumbs="[
            { label: t('menu.my_dashboard'), href: route('portal.dashboard') },
            { label: t('projects.title'), href: route('portal.projects.index') },
            { label: project.title },
        ]"
        :meta-title="metaTitle"
        :meta-description="metaDescription"
    >
        <div class="portal-grid portal-grid--show">
            <div>
                <div class="portal-panel">
                    <div class="portal-panel__header">
                        <h2 class="portal-panel__title">{{ t('projects.details') }}</h2>
                    </div>
                    <div class="portal-panel__body">
                        <div class="portal-details">
                            <div class="portal-details__row">
                                <div class="portal-details__label">{{ t('fields.status') }}</div>
                                <div class="portal-details__value">
                                    <span
                                        class="portal-badge"
                                        :style="statusBadgeStyle"
                                    >
                                        {{ project.status?.name || '—' }}
                                    </span>
                                </div>
                            </div>
                            <div class="portal-details__row">
                                <div class="portal-details__label">{{ t('fields.payment_status') }}</div>
                                <div class="portal-details__value">{{ paymentStatusLabel(project.payment_status) }}</div>
                            </div>
                            <div v-if="project.company?.name" class="portal-details__row">
                                <div class="portal-details__label">{{ t('fields.company') }}</div>
                                <div class="portal-details__value">{{ project.company.name }}</div>
                            </div>
                            <div v-if="project.start_date" class="portal-details__row">
                                <div class="portal-details__label">{{ t('fields.start_date') }}</div>
                                <div class="portal-details__value">{{ project.start_date }}</div>
                            </div>
                            <div v-if="project.due_date" class="portal-details__row">
                                <div class="portal-details__label">{{ t('fields.due_date') }}</div>
                                <div class="portal-details__value">{{ project.due_date }}</div>
                            </div>
                            <div v-if="project.description" class="portal-details__row">
                                <div class="portal-details__label">{{ t('fields.description') }}</div>
                                <div class="portal-details__value">{{ project.description }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="attachments.length" class="portal-panel" style="margin-top: 24px;">
                    <div class="portal-panel__header">
                        <h2 class="portal-panel__title">{{ t('projects.attachments') }}</h2>
                    </div>
                    <div class="portal-panel__body">
                        <ul class="list-unstyled mb-0">
                            <li v-for="(attachment, index) in attachments" :key="index" class="mb-2">
                                <a :href="attachment.url" target="_blank" class="portal-panel__action">
                                    <i class="fas fa-paperclip"></i>{{ attachment.name }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div v-if="project.is_completed" class="portal-panel" style="margin-top: 24px;">
                    <div class="portal-panel__header">
                        <h2 class="portal-panel__title">{{ t('projects.review_title') }}</h2>
                    </div>
                    <div class="portal-panel__body">
                        <div v-if="project.review" class="portal-details">
                            <p class="mb-2">{{ t('projects.review_submitted_hint') }}</p>
                            <div class="portal-review-quote">“{{ reviewQuote }}”</div>
                            <span
                                v-if="project.review.status"
                                class="portal-badge mt-3"
                                :class="project.review.status === 'Published' ? 'portal-badge--paid' : 'portal-badge--invoice'"
                            >
                                {{ project.review.status === 'Published'
                                    ? t('projects.review_status_published')
                                    : t('projects.review_status_pending') }}
                            </span>
                        </div>
                        <form v-else-if="project.can_review" @submit.prevent="submitReview">
                            <p class="mb-3">{{ t('projects.review_prompt') }}</p>
                            <textarea
                                v-model="reviewForm.quote"
                                class="portal-input"
                                rows="4"
                                :placeholder="t('projects.review_placeholder')"
                                required
                                minlength="10"
                                maxlength="2000"
                            ></textarea>
                            <p v-if="reviewForm.errors.quote" class="portal-form-error mt-2 mb-0">{{ reviewForm.errors.quote }}</p>
                            <button type="submit" class="thm-btn mt-3" :disabled="reviewForm.processing" style="padding: 10px 20px; font-size: 14px;">
                                {{ t('projects.submit_review') }}
                            </button>
                        </form>
                        <div v-else class="portal-empty">
                            {{ t('projects.review_unavailable') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="portal-grid__stack">
                <div class="portal-panel">
                    <div class="portal-panel__header">
                        <h2 class="portal-panel__title">{{ t('projects.billing') }}</h2>
                    </div>
                    <div class="portal-panel__body">
                        <div class="portal-billing-grid">
                            <div class="portal-billing-item">
                                <div class="portal-billing-item__label">{{ t('fields.budget') }}</div>
                                <div class="portal-billing-item__value">
                                    {{ formatMoney(collection.budget, collection.currency) }}
                                </div>
                            </div>
                            <div class="portal-billing-item">
                                <div class="portal-billing-item__label">{{ t('fields.invoiced') }}</div>
                                <div class="portal-billing-item__value">
                                    {{ formatMoney(collection.invoiced, collection.currency) }}
                                </div>
                            </div>
                            <div class="portal-billing-item">
                                <div class="portal-billing-item__label">{{ t('fields.remaining') }}</div>
                                <div class="portal-billing-item__value portal-billing-item__value--accent">
                                    {{ formatMoney(collection.remaining, collection.currency) }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="portal-details__label">{{ t('fields.collection_rate') }}</span>
                                <span class="portal-details__value">{{ collection.collection_rate }}%</span>
                            </div>
                            <div class="portal-progress" style="height: 10px;">
                                <div
                                    class="portal-progress__bar"
                                    :style="{ width: `${collection.collection_rate}%` }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="portal-panel">
                    <div class="portal-panel__header">
                        <h2 class="portal-panel__title">{{ t('projects.invoices') }}</h2>
                    </div>
                    <div class="portal-panel__body">
                        <div v-if="invoiceList.length === 0" class="portal-empty">
                            <span class="portal-empty__icon"><i class="fas fa-file-alt"></i></span>
                            {{ t('projects.no_invoices') }}
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
                                    {{ t('projects.download_pdf') }}
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
import { useForm, usePage } from '@inertiajs/vue3';
import PortalShell from '@/Components/Portal/PortalShell.vue';
import { usePortalTranslations } from '@/Composables/usePortalTranslations';

const props = defineProps({
    project: { type: Object, required: true },
    invoices: { type: Array, default: () => [] },
    meta: { type: Object, default: () => ({}) },
});

const page = usePage();
const { t, paymentStatusLabel, invoiceStatusLabel } = usePortalTranslations();

const reviewForm = useForm({
    quote: '',
});

const reviewQuote = computed(() => {
    const quote = props.project.review?.quote;
    if (!quote) return '';
    if (typeof quote === 'string') return quote;
    const loc = page.props.locale || 'en';
    return quote[loc] || Object.values(quote)[0] || '';
});

const submitReview = () => {
    reviewForm.post(route('portal.projects.review', props.project.id), {
        preserveScroll: true,
        onSuccess: () => reviewForm.reset('quote'),
    });
};

const metaTitle = computed(() => props.meta?.title || props.project.title);
const metaDescription = computed(() => props.meta?.description || t('pages.project_show_description'));

const collection = computed(() => props.project.collection ?? {
    budget: 0,
    invoiced: 0,
    remaining: 0,
    currency: 'USD',
    collection_rate: 0,
});

const invoiceList = computed(() => {
    if (Array.isArray(props.invoices)) {
        return props.invoices;
    }

    return Object.values(props.invoices ?? {});
});

const attachments = computed(() => {
    const items = props.project.attachments;

    return Array.isArray(items) ? items : [];
});

const statusBadgeStyle = computed(() => {
    const color = props.project.status?.color_code || '#6c757d';

    return {
        backgroundColor: `${color}33`,
        color,
    };
});

const formatMoney = (amount, currency) => `${Number(amount ?? 0).toFixed(2)} ${currency || ''}`.trim();

const invoiceBadgeClass = (status) => {
    if (status === 'paid') return 'portal-badge--paid';
    if (status === 'overdue') return 'portal-badge--overdue';
    return 'portal-badge--invoice';
};
</script>
