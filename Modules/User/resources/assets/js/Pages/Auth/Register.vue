<template>
    <Head>
           <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'" />
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/shop.css'" />
        <title>{{trans("Register")}} | {{seo.website_name}}</title>
    </Head>

    <app-layout>
        <section class="page-header">
            <div class="page-header__bg"  :style="{ backgroundImage: `url(${asset_path}images/login-header-bg.jpg)`}"></div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans("Sign UP") }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="/"><i class="fas fa-home"></i>{{ trans("Home") }}</a></li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`""></span></li>
                            <li>{{ trans("Sign UP") }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="sign-up-one">
            <div class="container">
                <div class="sign-up-one__form">
                    <div class="inner-title text-center">
                        <h2>{{ trans("Sing Up") }}</h2>
                    </div>

                    <form id="sign-up-one__form" name="sign-up-one_form" action="#" method="post" @submit.prevent="form.post(route('register'))">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input
                                            id="formName"
                                            v-model="form.name"
                                            type="text"
                                            name="form_name"
                                            placeholder="Name..."
                                            :disabled="form.processing"
                                            required=""
                                            value="">
                                    </div>
                                    <div v-if="errors.name" class="text-danger mt-1 small">{{ errors.name }}</div>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input
                                            id="formEmail"
                                            v-model="form.email"
                                            type="email"
                                            name="form_email"
                                            placeholder="Email..."
                                            :disabled="form.processing"
                                            required=""
                                            value="">
                                    </div>
                                    <div v-if="errors.email" class="text-danger mt-1 small">{{ errors.email }}</div>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input
                                            id="formPhone"
                                            v-model="form.mobile"
                                            type="text"
                                            name="form_phone"
                                            placeholder="Phone..."
                                            :disabled="form.processing"
                                            required=""
                                            value="">
                                    </div>
                                    <div v-if="errors.mobile" class="text-danger mt-1 small">{{ errors.mobile }}</div>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input
                                            id="formPassword"
                                            v-model="form.password"
                                            type="password"
                                            name="form_password"
                                            placeholder="Password..."
                                            :disabled="form.processing"
                                            required=""
                                            value="">
                                    </div>
                                    <div v-if="errors.password" class="text-danger mt-1 small">{{ errors.password }}</div>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="form-group">
                                    <div class="input-box">
                                        <input
                                            id="formPasswordConfirm"
                                            v-model="form.password_confirmation"
                                            type="password"
                                            name="password_confirmation"
                                            placeholder="Confirm Password..."
                                            :disabled="form.processing"
                                            required=""
                                            value="">
                                    </div>
                                    <div v-if="errors.password_confirmation" class="text-danger mt-1 small">{{ errors.password_confirmation }}</div>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="form-group">
                                    <button
                                        class="thm-btn"
                                        type="submit"
                                        data-loading-text="Please wait..."
                                        :disabled="form.processing"
                                        :class="{ 'opacity-50': form.processing }"
                                    >
                                        <span v-if="form.processing">
                                            <i class="fa-solid fa-spinner fa-spin me-2"></i>{{ trans("Registering...") }}
                                        </span>
                                        <span v-else>
                                            {{ trans("Sign UP") }}
                                            <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow `"></span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>


                        <div class="create-account text-center">
                            <p>{{ trans("Already have an account?") }} <Link :href="route('login')">{{ trans("Login Here") }}</Link></p>
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
        const asset_path = computed(() => page.props.asset_path)
        const trans = (key) => {
            try {
                return page.props.translations?.[key] || key;
            } catch (e) {
                return key;
            }
        };

        const form = useForm({
            name: '',
            email: '',
            mobile: '',
            password: '',
            password_confirmation: '',

        });

        return {form, seo, trans , asset_path};
    }
}

</script>

<style scoped>
.thm-btn.opacity-50 {
    opacity: 0.6;
}

.input-box input.error {
    border-color: #dc3545;
}

.text-danger {
    color: #dc3545;
}
</style>
