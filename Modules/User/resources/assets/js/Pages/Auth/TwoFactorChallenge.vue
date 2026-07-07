<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'" />
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/shop.css'" />
        <title>{{ metaTitle }}</title>
        <meta name="description" :content="metaDescription">
        <meta name="robots" content="noindex, nofollow">
    </Head>

    <app-layout>
        <section class="page-header">
            <div class="page-header__bg" :style="{ backgroundImage: `url(${asset_path}images/backgrounds/login-bg.jpg)` }"></div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans('Two-Factor Authentication') }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="/"><i class="fas fa-home"></i>{{ trans('Home') }}</a></li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li>{{ trans('Two-Factor Authentication') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="login-one">
            <div class="container">
                <div class="login-one__form">
                    <div class="inner-title text-center">
                        <h2>{{ trans('Two-Factor Authentication') }}</h2>
                        <p class="mt-3 mb-0">
                            {{ trans('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
                        </p>
                    </div>

                    <form class="mt-4" @submit.prevent="submit">
                        <div class="row">
                            <div v-if="!useRecoveryCode" class="col-xl-12">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input
                                            v-model="form.code"
                                            type="text"
                                            inputmode="numeric"
                                            autocomplete="one-time-code"
                                            :placeholder="trans('Authentication Code')"
                                            :disabled="form.processing"
                                            required
                                            autofocus
                                        >
                                    </div>
                                    <div v-if="form.errors.code" class="text-danger mt-1 small">{{ form.errors.code }}</div>
                                </div>
                            </div>

                            <div v-else class="col-xl-12">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input
                                            v-model="form.recovery_code"
                                            type="text"
                                            autocomplete="one-time-code"
                                            :placeholder="trans('Recovery Code')"
                                            :disabled="form.processing"
                                            required
                                            autofocus
                                        >
                                    </div>
                                    <div v-if="form.errors.recovery_code" class="text-danger mt-1 small">{{ form.errors.recovery_code }}</div>
                                </div>
                            </div>

                            <div class="col-xl-12 text-center mb-3">
                                <button
                                    type="button"
                                    class="btn btn-link"
                                    :disabled="form.processing"
                                    @click="toggleRecovery"
                                >
                                    {{ useRecoveryCode ? trans('Use an authentication code') : trans('Use a recovery code') }}
                                </button>
                            </div>

                            <div class="col-xl-12">
                                <div class="form-group">
                                    <button
                                        class="thm-btn"
                                        type="submit"
                                        :disabled="form.processing"
                                        :class="{ 'opacity-50': form.processing }"
                                    >
                                        <span v-if="form.processing">
                                            <i class="fa-solid fa-spinner fa-spin me-2"></i>{{ trans('Signing In...') }}
                                        </span>
                                        <span v-else>
                                            {{ trans('Login') }}
                                            <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </app-layout>
</template>

<script>
import { computed, ref } from 'vue';
import { usePage, useForm, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/App.vue';

export default {
    components: {
        AppLayout,
        Head,
    },
    setup() {
        const page = usePage();
        const useRecoveryCode = ref(false);

        const locale = computed(() => page.props.locale);
        const seo = computed(() => page.props.seo);
        const asset_path = computed(() => page.props.asset_path || '');
        const meta = computed(() => page.props.meta || {});

        const trans = (key) => {
            try {
                return page.props.translations?.[key] || key;
            } catch (e) {
                return key;
            }
        };

        const metaTitle = computed(() => `${trans('Two-Factor Authentication')} | ${seo.value.website_name || ''}`.trim());
        const metaDescription = computed(() => {
            return meta.value.description || trans('Please confirm access to your account by entering the authentication code provided by your authenticator application.');
        });

        const form = useForm({
            code: '',
            recovery_code: '',
        });

        const toggleRecovery = () => {
            useRecoveryCode.value = !useRecoveryCode.value;
            form.code = '';
            form.recovery_code = '';
            form.clearErrors();
        };

        const submit = () => {
            form.post(route('two-factor.login.store'), {
                preserveScroll: true,
            });
        };

        return {
            form,
            locale,
            trans,
            asset_path,
            metaTitle,
            metaDescription,
            useRecoveryCode,
            toggleRecovery,
            submit,
        };
    },
};
</script>
