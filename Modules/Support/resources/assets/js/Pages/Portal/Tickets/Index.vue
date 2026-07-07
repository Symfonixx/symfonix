<template>
    <portal-shell
        :title="t('tickets.title')"
        :subtitle="t('tickets.subtitle')"
        active="tickets"
        :breadcrumbs="[
            { label: t('menu.my_dashboard'), href: route('portal.dashboard') },
            { label: t('tickets.title') },
        ]"
        :meta-title="metaTitle"
        :meta-description="metaDescription"
    >
        <div class="portal-panel portal-panel--allow-overflow mb-4">
            <div class="portal-panel__body">
                <div class="portal-ticket-toolbar">
                    <form class="portal-ticket-filters" @submit.prevent="applyFilters">
                        <div class="portal-ticket-filters__group">
                            <label>{{ t('tickets.filter_status') }}</label>
                            <portal-select
                                v-model="filterForm.status"
                                :options="statusOptions"
                                :placeholder="t('tickets.all_statuses')"
                            />
                        </div>
                        <div class="portal-ticket-filters__group">
                            <label>{{ t('tickets.filter_priority') }}</label>
                            <portal-select
                                v-model="filterForm.priority"
                                :options="priorityOptions"
                                :placeholder="t('tickets.all_priorities')"
                            />
                        </div>
                        <div class="portal-ticket-filters__group">
                            <label>{{ t('tickets.sort_newest') }}</label>
                            <portal-select
                                v-model="filterForm.sort"
                                :options="sortOptions"
                            />
                        </div>
                        <div class="portal-ticket-filters__actions">
                            <button type="submit" class="thm-btn">{{ t('tickets.apply_filters') }}</button>
                            <button type="button" class="portal-panel__action" @click="clearFilters">
                                {{ t('tickets.clear_filters') }}
                            </button>
                        </div>
                    </form>
                    <Link :href="route('portal.tickets.create')" class="thm-btn">
                        <i class="fas fa-plus me-1"></i>{{ t('tickets.new_ticket') }}
                    </Link>
                </div>
            </div>
        </div>

        <div v-if="tickets.data.length === 0" class="portal-panel">
            <div class="portal-empty">
                <i class="fas fa-life-ring portal-empty__icon"></i>
                {{ t('tickets.no_tickets') }}
            </div>
        </div>

        <div v-else class="portal-ticket-list">
            <article v-for="ticket in tickets.data" :key="ticket.id" class="portal-ticket-card">
                <div class="portal-ticket-card__top">
                    <div>
                        <div class="portal-ticket-card__number">{{ ticket.ticket_number }}</div>
                        <h3 class="portal-ticket-card__title">{{ ticket.subject }}</h3>
                    </div>
                    <span class="portal-badge" :class="statusBadgeClass(ticket.status)">
                        {{ ticketStatusLabel(ticket.status) }}
                    </span>
                </div>

                <div class="portal-ticket-card__meta">
                    <div>
                        <span>{{ t('fields.category') }}</span>
                        <strong>{{ ticket.category || '—' }}</strong>
                    </div>
                    <div>
                        <span>{{ t('fields.priority') }}</span>
                        <strong>{{ ticketPriorityLabel(ticket.priority) }}</strong>
                    </div>
                    <div>
                        <span>{{ t('fields.status') }}</span>
                        <strong>{{ formatDate(ticket.created_at) }}</strong>
                    </div>
                </div>

                <div class="portal-ticket-card__footer">
                    <Link :href="route('portal.tickets.show', ticket.id)" class="thm-btn w-100 text-center">
                        {{ t('tickets.view_ticket') }}
                        <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                    </Link>
                </div>
            </article>
        </div>

        <nav v-if="tickets.links?.length > 3" class="portal-pagination" aria-label="Pagination">
            <Link
                v-for="link in tickets.links"
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
import { computed, reactive } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import PortalShell from '@/Components/Portal/PortalShell.vue';
import PortalSelect from '@/Components/Portal/PortalSelect.vue';
import { usePortalTranslations } from '@/Composables/usePortalTranslations';

const props = defineProps({
    tickets: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    statuses: { type: Array, default: () => [] },
    priorities: { type: Array, default: () => [] },
    meta: { type: Object, default: () => ({}) },
});

const page = usePage();
const { t, ticketStatusLabel, ticketPriorityLabel } = usePortalTranslations();
const locale = computed(() => page.props.locale);
const metaTitle = computed(() => props.meta?.title || t('pages.tickets_title'));
const metaDescription = computed(() => props.meta?.description || t('pages.tickets_description'));

const filterForm = reactive({
    status: props.filters.status || '',
    priority: props.filters.priority || '',
    sort: props.filters.sort || 'newest',
});

const statusOptions = computed(() => props.statuses.map((status) => ({
    value: status,
    label: ticketStatusLabel(status),
})));

const priorityOptions = computed(() => props.priorities.map((priority) => ({
    value: priority,
    label: ticketPriorityLabel(priority),
})));

const sortOptions = computed(() => [
    { value: 'newest', label: t('tickets.sort_newest') },
    { value: 'oldest', label: t('tickets.sort_oldest') },
]);

const applyFilters = () => {
    router.get(route('portal.tickets.index'), {
        status: filterForm.status || undefined,
        priority: filterForm.priority || undefined,
        sort: filterForm.sort || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    filterForm.status = '';
    filterForm.priority = '';
    filterForm.sort = 'newest';
    router.get(route('portal.tickets.index'));
};

const statusBadgeClass = (status) => {
    if (status === 'closed' || status === 'resolved') return 'portal-badge--success';
    if (status === 'in_progress') return 'portal-badge--info';
    return 'portal-badge--neutral';
};

const formatDate = (value) => {
    if (!value) return '—';
    return new Date(value).toLocaleString(locale.value);
};
</script>
