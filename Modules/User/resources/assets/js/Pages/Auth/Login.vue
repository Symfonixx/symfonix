<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'" />
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/shop.css'" />
        <title>{{ metaTitle }}</title>
        <meta name="description" :content="metaDescription">
        <meta name="keywords" :content="metaKeywords">
        <meta name="robots" :content="metaRobots">
        <link v-if="metaCanonical" rel="canonical" :href="metaCanonical">
        <meta property="og:title" :content="metaTitle">
        <meta property="og:description" :content="metaDescription">
        <meta v-if="metaImage" property="og:image" :content="metaImage">
        <meta v-if="metaCanonical" property="og:url" :content="metaCanonical">
        <meta property="og:type" content="website">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" :content="metaTitle">
        <meta name="twitter:description" :content="metaDescription">
        <meta v-if="metaImage" name="twitter:image" :content="metaImage">
    </Head>

    <app-layout>
        <section class="page-header">
            <div class="page-header__bg"  :style="{ backgroundImage: `url(${asset_path}images/backgrounds/login-bg.jpg)`}"></div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans("Login") }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="/"><i class="fas fa-home"></i>{{ trans("Home") }}</a></li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
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
                        <h2>{{ trans("Login") }}</h2>
                    </div>

                    <div v-if="flash.success" class="flash-message flash-message--success" role="alert">
                        {{ flash.success }}
                    </div>
                    <div v-if="flash.error" class="flash-message flash-message--error" role="alert">
                        {{ flash.error }}
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
                                            :placeholder="trans('Email')"
                                            :disabled="form.processing"
                                            required="">
                                    </div>
                                    <div v-if="errors.email" class="text-danger mt-1 small">{{ formatError(errors.email) }}</div>
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
                                            :placeholder="trans('Password')"
                                            :disabled="form.processing"
                                            required="">
                                    </div>
                                    <div v-if="errors.password" class="text-danger mt-1 small">{{ formatError(errors.password) }}</div>
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
                                           {{ trans("Login") }}
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
                                        {{ trans("Remember Me") }}
                                    </label>
                                </div>
                                <div class="forget">
                                    <Link :href="route('password.request')">{{ trans("Forgot Password") }}</Link>
                                </div>
                            </div>

                            <div class="create-account text-center">
                                <p>{{ trans("I Don't Have Account!") }} <Link :href="route('register')">{{ trans("Create A New Account") }}</Link></p>
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

        const locale = computed(() => page.props.locale)
        const seo = computed(() => page.props.seo)
        const settings = computed(() => page.props.settings || {})
        const asset_path = computed(() => page.props.asset_path || '')
        const flash = computed(() => page.props.flash || {})
        const meta = computed(() => page.props.meta || {})
        const trans = (key) => {
            try {
                return page.props.translations?.[key] || key;
            } catch (e) {
                return key;
            }
        };

        const formatError = (error) => {
            if (!error) {
                return error;
            }

            const authErrors = {
                'auth.failed': trans('These credentials do not match our records.'),
                'auth.password': trans('The provided password is incorrect.'),
                'auth.throttle': trans('Too many login attempts. Please try again in :seconds seconds.'),
            };

            return authErrors[error] || trans(error) || error;
        };

        const metaTitle = computed(() => `${trans("Login")} | ${seo.value.website_name || ''}`.trim())
        const metaDescription = computed(() => {
            return meta.value.description || trans('Log in to manage your account and services.')
        })
        const metaKeywords = computed(() => {
            return meta.value.keywords || trans('login, sign in, account access')
        })
        const metaImage = computed(() => {
            return meta.value?.og?.image || meta.value?.twitter?.image || settings.value?.meta_img || ''
        })
        const metaCanonical = computed(() => meta.value.canonical || '')
        const metaRobots = computed(() => meta.value.robots || 'noindex, nofollow')

        const form = useForm({
            email: '',
            password: '',
            remember: false,
        });

        return {
            form,
            seo,
            locale,
            trans,
            formatError,
            asset_path,
            flash,
            metaTitle,
            metaDescription,
            metaKeywords,
            metaImage,
            metaCanonical,
            metaRobots
        };
    }
}

</script>

<style scoped>
.thm-btn.opacity-50 {
    opacity: 0.6;
}

.text-danger {
    color: #dc3545;
}

.flash-message {
    border-radius: 10px;
    margin-bottom: 20px;
    padding: 12px 16px;
}

.flash-message--success {
    background-color: #d1e7dd;
    border: 1px solid #badbcc;
    color: #0f5132;
}

.flash-message--error {
    background-color: #f8d7da;
    border: 1px solid #f5c2c7;
    color: #842029;
}
</style>
