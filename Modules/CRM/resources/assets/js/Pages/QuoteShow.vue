<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'"/>
        <title>{{ metaTitle }}</title>
        <meta name="description" :content="metaDescription">
        <meta name="robots" content="noindex,nofollow">
        <meta property="og:title" :content="metaTitle">
        <meta property="og:description" :content="metaDescription">
    </Head>

    <app-layout>
        <section class="page-header">
            <div class="page-header__bg"
                 :style="{ backgroundImage: `url(${asset_path}images/backgrounds/contact-us-bg.jpg)`}">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h1>{{ labels.title }}</h1>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li>
                                <Link :href="route('home')">
                                    <i class="fas fa-home"></i>{{ trans('Home') }}
                                </Link>
                            </li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li>{{ quote.quote_number }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="quote-show">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-10">
                        <div class="quote-show__card">
                            <div class="quote-show__header">
                                <div>
                                    <img v-if="branding.logo_url" :src="branding.logo_url" :alt="branding.name"
                                         class="quote-show__logo mb-3">
                                    <h3 v-else class="quote-show__brand mb-2">{{ branding.name }}</h3>
                                    <div class="quote-show__muted">
                                        <div v-if="branding.phone">{{ branding.phone }}</div>
                                        <div v-if="branding.email">{{ branding.email }}</div>
                                        <div v-if="branding.address">{{ branding.address }}</div>
                                    </div>
                                </div>
                                <div class="quote-show__meta text-md-end">
                                    <div class="quote-show__number">{{ quote.quote_number }}</div>
                                    <span class="quote-show__badge">{{ labels.status }}</span>
                                    <div class="quote-show__total">
                                        {{ formatMoney(quote.total) }} {{ quote.currency }}
                                    </div>
                                    <a :href="urls.pdf" class="thm-btn quote-show__pdf-btn">
                                        {{ labels.download_pdf }}
                                    </a>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="quote-show__label">{{ labels.quote_to }}</div>
                                    <div class="quote-show__value">{{ quote.company?.name }}</div>
                                    <div class="quote-show__muted" v-if="quote.company?.email">{{ quote.company.email }}</div>
                                    <div class="quote-show__muted" v-if="quote.deal?.title">{{ quote.deal.title }}</div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div>
                                        <span class="quote-show__muted">{{ labels.issued_at }}:</span>
                                        <span class="quote-show__value"> {{ quote.issued_at }}</span>
                                    </div>
                                    <div v-if="quote.expires_at">
                                        <span class="quote-show__muted">{{ labels.expires_at }}:</span>
                                        <span class="quote-show__value"> {{ quote.expires_at }}</span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="flashSuccess" class="alert alert-success" role="alert">
                                {{ flashSuccess }}
                            </div>

                            <div v-if="statusBanner" class="alert" :class="statusBannerClass" role="alert">
                                {{ statusBanner }}
                            </div>

                            <div class="table-responsive mb-4">
                                <table class="quote-show__table">
                                    <thead>
                                    <tr>
                                        <th>{{ labels.description }}</th>
                                        <th class="text-end">{{ labels.quantity }}</th>
                                        <th class="text-end">{{ labels.unit_price }}</th>
                                        <th class="text-end">{{ labels.discount }}</th>
                                        <th class="text-end">{{ labels.tax }}</th>
                                        <th class="text-end">{{ labels.amount }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(line, index) in quote.lines" :key="index">
                                        <td>{{ line.description }}</td>
                                        <td class="text-end">{{ line.quantity }}</td>
                                        <td class="text-end">{{ formatMoney(line.unit_price) }}</td>
                                        <td class="text-end">{{ formatMoney(line.discount_amount) }}</td>
                                        <td class="text-end">{{ formatMoney(line.tax_amount) }}</td>
                                        <td class="text-end">{{ formatMoney(line.amount) }}</td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end">{{ labels.subtotal }}</td>
                                        <td class="text-end">{{ formatMoney(quote.subtotal) }} {{ quote.currency }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end">{{ labels.discount }}</td>
                                        <td class="text-end">{{ formatMoney(quote.discount_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end">{{ labels.tax }}</td>
                                        <td class="text-end">{{ formatMoney(quote.tax_amount) }}</td>
                                    </tr>
                                    <tr class="quote-show__grand">
                                        <td colspan="5" class="text-end">{{ labels.total }}</td>
                                        <td class="text-end">{{ formatMoney(quote.total) }} {{ quote.currency }}</td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div v-if="quote.terms" class="mb-4">
                                <h5 class="quote-show__section-title">{{ labels.terms }}</h5>
                                <p class="quote-show__muted" style="white-space: pre-wrap;">{{ quote.terms }}</p>
                            </div>

                            <div v-if="branding.sign_url" class="text-center mb-5">
                                <div class="quote-show__label mb-2">{{ labels.company_sign }}</div>
                                <img :src="branding.sign_url" :alt="branding.name" class="quote-show__sign">
                            </div>

                            <div v-if="quote.can_respond" class="row g-4">
                                <div class="col-md-6">
                                    <div class="quote-show__panel h-100">
                                        <h4 class="quote-show__section-title">{{ labels.accept_title }}</h4>
                                        <form @submit.prevent="submitAccept">
                                            <div class="mb-3">
                                                <label class="quote-show__label form-label">{{ labels.your_name }}</label>
                                                <input v-model="acceptForm.responder_name" type="text" class="quote-show__input" required>
                                                <div v-if="acceptForm.errors.responder_name" class="text-danger small mt-1">
                                                    {{ acceptForm.errors.responder_name }}
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="quote-show__label form-label">{{ labels.your_email }}</label>
                                                <input v-model="acceptForm.responder_email" type="email" class="quote-show__input">
                                            </div>
                                            <div class="mb-3">
                                                <label class="quote-show__label form-label">{{ labels.optional_note }}</label>
                                                <textarea v-model="acceptForm.response_note" rows="3" class="quote-show__input"></textarea>
                                            </div>
                                            <button type="submit" class="thm-btn" :disabled="acceptForm.processing">
                                                {{ labels.confirm_accept }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="quote-show__panel h-100">
                                        <h4 class="quote-show__section-title">{{ labels.reject_title }}</h4>
                                        <form @submit.prevent="submitReject">
                                            <div class="mb-3">
                                                <label class="quote-show__label form-label">{{ labels.your_name }}</label>
                                                <input v-model="rejectForm.responder_name" type="text" class="quote-show__input" required>
                                                <div v-if="rejectForm.errors.responder_name" class="text-danger small mt-1">
                                                    {{ rejectForm.errors.responder_name }}
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="quote-show__label form-label">{{ labels.your_email }}</label>
                                                <input v-model="rejectForm.responder_email" type="email" class="quote-show__input">
                                            </div>
                                            <div class="mb-3">
                                                <label class="quote-show__label form-label">{{ labels.optional_note }}</label>
                                                <textarea v-model="rejectForm.response_note" rows="3" class="quote-show__input"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-outline-danger" :disabled="rejectForm.processing">
                                                {{ labels.confirm_reject }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </app-layout>
</template>

<script setup>
import {computed} from 'vue';
import {Head, Link, useForm, usePage} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/App.vue';

const props = defineProps({
    quote: {type: Object, required: true},
    branding: {type: Object, required: true},
    urls: {type: Object, required: true},
    labels: {type: Object, required: true},
    viewer: {type: Object, default: () => ({})},
    meta: {type: Object, default: () => ({})},
});

const page = usePage();
const locale = computed(() => page.props.locale || 'en');
const asset_path = computed(() => page.props.asset_path || '/');
const flashSuccess = computed(() => page.props.flash?.success || null);
const metaTitle = computed(() => props.meta?.title || props.labels.title);
const metaDescription = computed(() => props.meta?.description || props.labels.subtitle);

const statusBanner = computed(() => {
    if (props.quote.status === 'expired') return props.labels.expired;
    if (props.quote.status === 'accepted') return props.labels.accepted;
    if (props.quote.status === 'rejected') return props.labels.rejected;
    if (props.quote.status === 'void') return props.labels.void;
    return null;
});

const statusBannerClass = computed(() => {
    if (props.quote.status === 'accepted') return 'alert-success';
    if (props.quote.status === 'rejected' || props.quote.status === 'void' || props.quote.status === 'expired') {
        return 'alert-warning';
    }
    return 'alert-info';
});

const acceptForm = useForm({
    responder_name: props.viewer?.name || '',
    responder_email: props.viewer?.email || '',
    response_note: '',
});

const rejectForm = useForm({
    responder_name: props.viewer?.name || '',
    responder_email: props.viewer?.email || '',
    response_note: '',
});

function formatMoney(value) {
    return Number(value || 0).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function submitAccept() {
    acceptForm.post(props.urls.accept, {preserveScroll: true});
}

function submitReject() {
    rejectForm.post(props.urls.reject, {preserveScroll: true});
}
</script>

<style scoped>
.quote-show {
    padding: 60px 0 90px;
    background: #0b1220;
}

.quote-show__card {
    background: linear-gradient(180deg, #121a2b 0%, #0f1624 100%);
    border: 1px solid rgba(148, 163, 184, 0.18);
    border-radius: 18px;
    padding: 2rem;
    color: #e2e8f0;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
}

.quote-show__header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 1.5rem;
    margin-bottom: 1.75rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(148, 163, 184, 0.16);
}

.quote-show__logo {
    max-height: 48px;
    max-width: 180px;
}

.quote-show__brand,
.quote-show__number,
.quote-show__value,
.quote-show__section-title {
    color: #f8fafc;
}

.quote-show__muted,
.quote-show__label {
    color: #94a3b8;
}

.quote-show__label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 0.35rem;
}

.quote-show__number {
    font-size: 1.35rem;
    font-weight: 700;
}

.quote-show__badge {
    display: inline-block;
    margin-top: 0.35rem;
    padding: 0.25rem 0.65rem;
    border-radius: 999px;
    background: rgba(59, 130, 246, 0.18);
    color: #93c5fd;
    font-size: 0.8rem;
}

.quote-show__total {
    margin-top: 0.75rem;
    font-size: 2rem;
    font-weight: 700;
    color: #f8fafc;
}

.quote-show__pdf-btn {
    margin-top: 0.85rem;
    padding: 10px 18px;
    font-size: 14px;
}

.quote-show__table {
    width: 100%;
    border-collapse: collapse;
    color: #e2e8f0;
}

.quote-show__table th,
.quote-show__table td {
    padding: 0.85rem 0.65rem;
    border-bottom: 1px solid rgba(148, 163, 184, 0.14);
    vertical-align: middle;
}

.quote-show__table thead th {
    color: #94a3b8;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    font-weight: 600;
}

.quote-show__table tfoot td {
    border-bottom: none;
    color: #cbd5e1;
}

.quote-show__grand td {
    color: #f8fafc !important;
    font-weight: 700;
    font-size: 1.05rem;
    padding-top: 1rem;
}

.quote-show__panel {
    background: rgba(15, 23, 42, 0.65);
    border: 1px solid rgba(148, 163, 184, 0.16);
    border-radius: 14px;
    padding: 1.25rem;
}

.quote-show__input {
    width: 100%;
    background: #0b1220;
    border: 1px solid rgba(148, 163, 184, 0.25);
    color: #f1f5f9;
    border-radius: 10px;
    padding: 0.7rem 0.85rem;
}

.quote-show__input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.quote-show__sign {
    max-height: 80px;
    max-width: 200px;
}

@media (max-width: 767.98px) {
    .quote-show__card {
        padding: 1.25rem;
    }

    .quote-show__total {
        font-size: 1.6rem;
    }
}
</style>
