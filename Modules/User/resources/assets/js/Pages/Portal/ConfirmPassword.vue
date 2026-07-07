<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'" />
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/portal.css'" />
        <title>{{ metaTitle }}</title>
        <meta name="description" :content="metaDescription">
        <meta name="robots" content="noindex, nofollow">
    </Head>

    <app-layout>
        <section class="page-header portal-page-header">
            <div class="page-header__bg" :style="{ backgroundImage: `url(${asset_path}images/backgrounds/login-bg.jpg)` }"></div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ t('profile.confirm_password_title') }}</h2>
                    <p class="portal-page-header__subtitle">{{ t('profile.confirm_password_description') }}</p>
                </div>
            </div>
        </section>

        <section class="portal-one">
            <div class="container">
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
            </div>
        </section>
    </app-layout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/App.vue';
import { usePortalTranslations } from '@/Composables/usePortalTranslations';

const page = usePage();
const { t } = usePortalTranslations();
const asset_path = computed(() => page.props.asset_path || '');
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
