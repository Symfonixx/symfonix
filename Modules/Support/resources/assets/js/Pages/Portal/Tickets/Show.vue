<template>
    <portal-shell
        :title="ticket.subject"
        :subtitle="ticket.ticket_number"
        active="tickets"
        :breadcrumbs="[
            { label: t('menu.my_dashboard'), href: route('portal.dashboard') },
            { label: t('tickets.title'), href: route('portal.tickets.index') },
            { label: ticket.ticket_number },
        ]"
        :meta-title="metaTitle"
        :meta-description="metaDescription"
    >
        <Link :href="route('portal.tickets.index')" class="portal-back">
            <i :class="`fas fa-arrow-${locale === 'ar' ? 'right' : 'left'}`"></i>
            {{ t('tickets.back_to_tickets') }}
        </Link>

        <div class="portal-grid portal-grid--show">
            <div>
                <div class="portal-panel">
                    <div class="portal-panel__header">
                        <h2 class="portal-panel__title">{{ t('projects.details') }}</h2>
                    </div>
                    <div class="portal-panel__body">
                        <div class="portal-details">
                            <div class="portal-details__row">
                                <div class="portal-details__label">{{ t('fields.ticket_number') }}</div>
                                <div class="portal-details__value">{{ ticket.ticket_number }}</div>
                            </div>
                            <div class="portal-details__row">
                                <div class="portal-details__label">{{ t('fields.status') }}</div>
                                <div class="portal-details__value">
                                    <span class="portal-badge" :class="statusBadgeClass(ticket.status)">
                                        {{ ticketStatusLabel(ticket.status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="portal-details__row">
                                <div class="portal-details__label">{{ t('fields.priority') }}</div>
                                <div class="portal-details__value">{{ ticketPriorityLabel(ticket.priority) }}</div>
                            </div>
                            <div class="portal-details__row">
                                <div class="portal-details__label">{{ t('fields.category') }}</div>
                                <div class="portal-details__value">{{ ticket.category || '—' }}</div>
                            </div>
                            <div class="portal-details__row">
                                <div class="portal-details__label">{{ t('fields.created_at') }}</div>
                                <div class="portal-details__value">{{ formatDate(ticket.created_at) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="ticket.can_reply" class="portal-panel" style="margin-top: 24px;">
                    <div class="portal-panel__body">
                        <button type="button" class="portal-panel__action text-danger" @click="closeTicket">
                            <i class="fas fa-times-circle me-1"></i>{{ t('tickets.close_ticket') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="portal-grid__stack">
                <div class="portal-panel">
                    <div class="portal-panel__header">
                        <h2 class="portal-panel__title">{{ t('tickets.conversation') }}</h2>
                    </div>
                    <div class="portal-panel__body">
                        <div class="portal-ticket-thread">
                            <div class="portal-ticket-message portal-ticket-message--customer">
                                <div class="portal-ticket-message__header">
                                    <strong>{{ t('tickets.original_message') }}</strong>
                                    <span>{{ formatDate(ticket.created_at) }}</span>
                                </div>
                                <div class="portal-ticket-message__body">{{ ticket.description }}</div>
                                <a
                                    v-if="ticket.attachment"
                                    :href="ticket.attachment.url"
                                    target="_blank"
                                    class="portal-ticket-message__attachment"
                                >
                                    <i class="fas fa-paperclip"></i>{{ ticket.attachment.name }}
                                </a>
                            </div>

                            <div
                                v-for="message in ticket.messages"
                                :key="message.id"
                                class="portal-ticket-message"
                                :class="message.is_staff ? 'portal-ticket-message--staff' : 'portal-ticket-message--customer'"
                            >
                                <div class="portal-ticket-message__header">
                                    <strong>{{ message.author }}</strong>
                                    <span>{{ formatDate(message.created_at) }}</span>
                                </div>
                                <div class="portal-ticket-message__body">{{ message.body }}</div>
                                <a
                                    v-if="message.attachment"
                                    :href="message.attachment.url"
                                    target="_blank"
                                    class="portal-ticket-message__attachment"
                                >
                                    <i class="fas fa-paperclip"></i>{{ message.attachment.name }}
                                </a>
                            </div>
                        </div>

                        <div v-if="ticket.can_reply" class="portal-ticket-reply">
                            <form @submit.prevent="submitReply">
                                <div class="portal-form-group">
                                    <label for="reply">{{ t('tickets.send_reply') }}</label>
                                    <textarea
                                        id="reply"
                                        v-model="replyForm.body"
                                        rows="4"
                                        class="portal-input"
                                        :placeholder="t('tickets.reply_placeholder')"
                                        :class="{ 'portal-input--error': replyForm.errors.body }"
                                        required
                                    ></textarea>
                                    <p v-if="replyForm.errors.body" class="portal-form-error">{{ replyForm.errors.body }}</p>
                                </div>
                                <div class="portal-form-group">
                                    <input
                                        type="file"
                                        class="portal-input"
                                        :class="{ 'portal-input--error': replyForm.errors.attachment }"
                                        @change="onReplyFileChange"
                                    >
                                    <p v-if="replyForm.errors.attachment" class="portal-form-error">{{ replyForm.errors.attachment }}</p>
                                </div>
                                <button type="submit" class="thm-btn" :disabled="replyForm.processing">
                                    {{ t('tickets.send_reply') }}
                                </button>
                            </form>
                        </div>

                        <div v-else class="portal-empty portal-empty--compact">
                            {{ t('tickets.ticket_closed') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </portal-shell>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import PortalShell from '@/Components/Portal/PortalShell.vue';
import { usePortalTranslations } from '@/Composables/usePortalTranslations';

const props = defineProps({
    ticket: { type: Object, required: true },
    meta: { type: Object, default: () => ({}) },
});

const page = usePage();
const { t, ticketStatusLabel, ticketPriorityLabel } = usePortalTranslations();
const locale = computed(() => page.props.locale);
const metaTitle = computed(() => props.meta?.title || props.ticket.subject);
const metaDescription = computed(() => props.meta?.description || t('pages.ticket_show_description'));

const replyForm = useForm({
    body: '',
    attachment: null,
});

const onReplyFileChange = (event) => {
    replyForm.attachment = event.target.files[0] || null;
};

const submitReply = () => {
    replyForm.post(route('portal.tickets.reply', props.ticket.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => replyForm.reset(),
    });
};

const closeTicket = () => {
    if (!window.confirm(t('tickets.close_confirm'))) {
        return;
    }

    router.post(route('portal.tickets.close', props.ticket.id), {}, {
        preserveScroll: true,
    });
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
