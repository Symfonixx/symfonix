<template>
    <Head>
        <title>{{trans("Reset Password")}} | {{seo.website_name}}</title>
    </Head>

    <app-layout>
        <section class="page-header">
            <div class="page-header__bg"  :style="{ backgroundImage: `url(${asset_path}images/page-header-bg.jpg)`}"></div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans("Reset Password") }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="/"><i class="fas fa-home"></i> {{ trans("Home") }}</a></li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`""></span></li>
                            <li>{{ trans("Reset Password") }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="sign-up-one">
            <div class="container">
                <div class="sign-up-one__form">
                    <div class="inner-title text-center">
                        <h2>{{ trans("Set New Password") }}</h2>
                    </div>

                    <form id="reset-password__form" @submit.prevent="form.post(route('password.update'))">
                        <input :value="form.token" name="token" type="hidden">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input
                                            id="email"
                                            v-model="form.email"
                                            type="email"
                                            name="email"
                                            autocomplete="email"
                                            :placeholder="trans('Email...')"
                                            :class="{ 'error': errors.email }"
                                            :disabled="form.processing"
                                            required
                                        >
                                    </div>
                                    <div v-if="errors.email" class="text-danger mt-1 small">{{ errors.email }}</div>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input
                                            id="password"
                                            v-model="form.password"
                                            type="password"
                                            name="password"
                                            autocomplete="new-password"
                                            :placeholder="trans('Password...')"
                                            :class="{ 'error': errors.password }"
                                            :disabled="form.processing"
                                            required
                                        >
                                    </div>
                                    <div v-if="errors.password" class="text-danger mt-1 small">{{ errors.password }}</div>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            type="password"
                                            name="password_confirmation"
                                            autocomplete="new-password"
                                            :placeholder="trans('Confirm Password...')"
                                            :class="{ 'error': errors.password_confirmation }"
                                            :disabled="form.processing"
                                            required
                                        >
                                    </div>
                                    <div v-if="errors.password_confirmation" class="text-danger mt-1 small">{{ errors.password_confirmation }}</div>
                                </div>
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
                                            <i class="fa-solid fa-spinner fa-spin me-2"></i>{{ trans("Resetting...") }}
                                        </span>
                                        <span v-else>
                                            {{ trans("Reset Password") }}<span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow `"></span>
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="create-account text-center">
                                    <p><Link :href="route('login')">{{ trans("Back to Login") }}</Link></p>
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
import {computed} from 'vue';
import {usePage, Link, useForm, Head} from '@inertiajs/vue3';
import AppLayout from '@/Layouts/App.vue';

export default {
    components: {
        AppLayout, Link, Head
    },
    props: {
        errors: Object
    },
    setup() {
        const page = usePage();

        const seo = computed(() => page.props.seo)
        const trans = (key) => {
            try {
                return page.props.translations?.[key] || key;
            } catch (e) {
                return key;
            }
        };
        const params = new URLSearchParams(window.location.search);
        const form = useForm({
            email: '',
            password: '',
            remember: false,
            token: params.get('token') || ''
        });

        return {form, seo, trans};
    }
}

</script>

<style scoped>
.thm-btn.opacity-50 {
    opacity: 0.6;
}

input.error {
    border-color: #dc3545;
}

.text-danger {
    color: #dc3545;
}
</style>
