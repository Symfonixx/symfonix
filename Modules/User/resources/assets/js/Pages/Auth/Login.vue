<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'" />
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/shop.css'" />
        <title>{{trans("Login")}} | {{seo.website_name}}</title>
    </Head>

    <app-layout>
        <section class="page-header">
            <div class="page-header__bg"  :style="{ backgroundImage: `url(${asset_path}images/login-header-bg.jpg)`}"></div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans("Login") }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="/"><i class="fas fa-home"></i>{{ trans("Home") }}</a></li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`""></span></li>
                            <li>{{ trans("Login") }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="login-one">
            <div class="container">
                <div class="login-one__form">
                    <div class="inner-title text-center">
                        <h2>{{ trans("Login Here") }}</h2>
                    </div>

                    <form id="login-one__form" name="Login-one_form" action="#" method="post" @submit.prevent="form.post(route('login'))">
                        <div class="row">
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
                                    <button
                                        class="thm-btn"
                                        type="submit"
                                        data-loading-text="Please wait..."
                                        :disabled="form.processing"
                                        :class="{ 'opacity-50': form.processing }"
                                    >
                                        <span v-if="form.processing">
                                            <i class="fa-solid fa-spinner fa-spin me-2"></i>{{ trans("Signing In...") }}
                                        </span>
                                        <span v-else>
                                            {{ trans("Login Here") }}
                                            <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow `"></span>
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <div class="remember-forget">
                                <div class="checked-box1">
                                    <input id="saveinfo" v-model="form.remember" type="checkbox" name="saveMyInfo" checked="">
                                    <label for="saveinfo">
                                        <span></span>
                                        {{ trans("Remember me") }}
                                    </label>
                                </div>
                                <div class="forget">
                                    <Link :href="route('password.request')">{{ trans("Forget password?") }}</Link>
                                </div>
                            </div>

                            <div class="create-account text-center">
                                <p>{{ trans("Not registered yet?") }} <Link :href="route('register')">{{ trans("Create an Account") }}</Link></p>
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
        const asset_path = computed(() => page.props.asset_path || '')
        const trans = (key) => {
            try {
                return page.props.translations?.[key] || key;
            } catch (e) {
                return key;
            }
        };

        const form = useForm({
            email: '',
            password: '',
            remember: false,
        });

        return {form, seo, trans , asset_path};
    }
}

</script>


