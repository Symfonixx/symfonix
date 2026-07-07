<template>
    <portal-shell
        :title="t('tickets.create_title')"
        :subtitle="t('tickets.create_subtitle')"
        active="tickets"
        :breadcrumbs="[
            { label: t('menu.my_dashboard'), href: route('portal.dashboard') },
            { label: t('tickets.title'), href: route('portal.tickets.index') },
            { label: t('tickets.create_title') },
        ]"
        :meta-title="metaTitle"
        :meta-description="metaDescription"
    >
        <div class="portal-panel portal-panel--allow-overflow">
            <div class="portal-panel__body">
                <form class="portal-ticket-form" @submit.prevent="submit">
                    <div class="portal-form-group">
                        <label for="subject">{{ t('fields.subject') }} *</label>
                        <input
                            id="subject"
                            v-model="form.subject"
                            type="text"
                            class="portal-input"
                            :class="{ 'portal-input--error': form.errors.subject }"
                            required
                        >
                        <p v-if="form.errors.subject" class="portal-form-error">{{ form.errors.subject }}</p>
                    </div>

                    <div class="portal-form-row">
                        <div class="portal-form-group">
                            <label for="ticket_category_id">{{ t('fields.category') }} *</label>
                            <portal-select
                                id="ticket_category_id"
                                v-model="form.ticket_category_id"
                                :options="categoryOptions"
                                :placeholder="t('tickets.select_category')"
                                :has-error="!!form.errors.ticket_category_id"
                            />
                            <p v-if="form.errors.ticket_category_id" class="portal-form-error">{{ form.errors.ticket_category_id }}</p>
                        </div>

                        <div class="portal-form-group">
                            <label for="priority">{{ t('fields.priority') }} *</label>
                            <portal-select
                                id="priority"
                                v-model="form.priority"
                                :options="priorityOptions"
                                :placeholder="t('tickets.select_priority')"
                                :has-error="!!form.errors.priority"
                            />
                            <p v-if="form.errors.priority" class="portal-form-error">{{ form.errors.priority }}</p>
                        </div>
                    </div>

                    <div class="portal-form-group">
                        <label for="description">{{ t('fields.description') }} *</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="6"
                            class="portal-input"
                            :class="{ 'portal-input--error': form.errors.description }"
                            required
                        ></textarea>
                        <p v-if="form.errors.description" class="portal-form-error">{{ form.errors.description }}</p>
                    </div>

                    <div class="portal-form-group">
                        <label for="attachment">{{ t('projects.attachments') }}</label>
                        <input
                            id="attachment"
                            type="file"
                            class="portal-input"
                            :class="{ 'portal-input--error': form.errors.attachment }"
                            @change="onFileChange"
                        >
                        <p class="portal-form-hint">{{ t('tickets.attachment_hint') }}</p>
                        <p v-if="form.errors.attachment" class="portal-form-error">{{ form.errors.attachment }}</p>
                    </div>

                    <div class="portal-ticket-form__actions">
                        <Link :href="route('portal.tickets.index')" class="portal-panel__action">
                            {{ t('tickets.back_to_tickets') }}
                        </Link>
                        <button type="submit" class="thm-btn" :disabled="form.processing">
                            {{ t('tickets.submit_ticket') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </portal-shell>
</template>

<script setup>
import { computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import PortalShell from '@/Components/Portal/PortalShell.vue';
import PortalSelect from '@/Components/Portal/PortalSelect.vue';
import { usePortalTranslations } from '@/Composables/usePortalTranslations';

const props = defineProps({
    categories: { type: Array, default: () => [] },
    priorities: { type: Array, default: () => [] },
    meta: { type: Object, default: () => ({}) },
});

const page = usePage();
const { t, ticketPriorityLabel } = usePortalTranslations();
const metaTitle = computed(() => props.meta?.title || t('tickets.create_title'));
const metaDescription = computed(() => props.meta?.description || t('pages.tickets_description'));

const categoryOptions = computed(() => props.categories.map((category) => ({
    value: category.id,
    label: category.name,
})));

const priorityOptions = computed(() => props.priorities.map((priority) => ({
    value: priority,
    label: ticketPriorityLabel(priority),
})));

const form = useForm({
    subject: '',
    description: '',
    ticket_category_id: '',
    priority: 'medium',
    attachment: null,
});

const onFileChange = (event) => {
    form.attachment = event.target.files[0] || null;
};

const submit = () => {
    form.post(route('portal.tickets.store'), {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>
