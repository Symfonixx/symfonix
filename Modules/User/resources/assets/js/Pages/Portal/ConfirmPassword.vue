<template>
    <portal-shell
        :title="t('profile.confirm_password_title')"
        :subtitle="t('profile.confirm_password_description')"
        active="profile"
        :breadcrumbs="[
            { label: t('menu.profile'), href: route('portal.profile.index') },
            { label: t('profile.confirm_password_title') },
        ]"
        :meta-title="metaTitle"
        :meta-description="metaDescription"
    >
        <div class="portal-panel portal-panel--narrow">
            <div class="portal-panel__body">
                <form class="portal-profile-form" @submit.prevent="submit">
                    <div class="portal-form-group">
                        <label for="password">{{ t('profile.current_password') }} *</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="portal-input"
                            :class="{ 'portal-input--error': form.errors.password }"
                            required
                            autofocus
                            autocomplete="current-password"
                        >
                        <p v-if="form.errors.password" class="portal-form-error">{{ form.errors.password }}</p>
                    </div>

                    <div class="portal-profile-form__actions">
                        <Link :href="route('portal.profile.index')" class="portal-panel__action">
                            {{ t('profile.back_to_profile') }}
                        </Link>
                        <button type="submit" class="thm-btn" :disabled="form.processing">
                            {{ t('profile.confirm_password_button') }}
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
import { usePortalTranslations } from '@/Composables/usePortalTranslations';

const page = usePage();
const { t } = usePortalTranslations();
const seo = computed(() => page.props.seo || {});
const metaTitle = computed(() => `${t('profile.confirm_password_title')} | ${seo.value.website_name || ''}`.trim());
const metaDescription = computed(() => t('profile.confirm_password_description'));

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm.store'), {
        preserveScroll: true,
    });
};
</script>
