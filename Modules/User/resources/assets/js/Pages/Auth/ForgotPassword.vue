<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'"/>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/shop.css'"/>
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
            <div class="page-header__bg"
                 :style="{ backgroundImage: `url(${asset_path}images/backgrounds/login-bg.jpg)`}"></div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans("Forgot Password") }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="/"><i class="fas fa-home"></i> {{ trans("Home") }}</a></li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li>{{ trans("Forgot Password") }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="login-one">
            <div class="container">
                <div class="login-one__form">
                    <div class="inner-title text-center">
                        <h2>{{ trans("Reset Your Password") }}</h2>
                    </div>

                    <div v-if="flash.success" class="flash-message flash-message--success" role="alert">
                        {{ flash.success }}
                    </div>
                    <div v-if="flash.error" class="flash-message flash-message--error" role="alert">
                        {{ flash.error }}
                    </div>

                    <form id="forgot-password__form" @submit.prevent="form.post(route('password.email'))">
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
                                            :placeholder="trans('Email')"
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
                                    <button
                                        class="thm-btn"
                                        type="submit"
                                        :disabled="form.processing"
                                        :class="{ 'opacity-50': form.processing }"
                                    >
                                        <span v-if="form.processing">
                                            <i class="fa-solid fa-spinner fa-spin me-2"></i>{{ trans("Sending...") }}
                                        </span>
                                        <span v-else>
                                            {{ trans("Send Email Verification") }}<span
                                            :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow `"></span>
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <div class="create-account text-center">
                                <p>
                                    <Link :href="route('login')">{{ trans("Back to Login") }}</Link>
                                </p>
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
        const asset_path = computed(() => page.props.asset_path)
        const flash = computed(() => page.props.flash || {})
        const meta = computed(() => page.props.meta || {})
        const trans = (key) => {
            try {
                return page.props.translations?.[key] || key;
            } catch (e) {
                return key;
            }
        };

        const metaTitle = computed(() => `${trans("Forgot Password")} | ${seo.value.website_name || ''}`.trim())
        const metaDescription = computed(() => {
            return meta.value.description || trans('Request a password reset link to regain access to your account.')
        })
        const metaKeywords = computed(() => {
            return meta.value.keywords || trans('forgot password, reset password, account recovery')
        })
        const metaImage = computed(() => {
            return meta.value?.og?.image || meta.value?.twitter?.image || settings.value?.meta_img || ''
        })
        const metaCanonical = computed(() => meta.value.canonical || '')
        const metaRobots = computed(() => meta.value.robots || 'noindex, nofollow')

        const form = useForm({
            email: '',
        });

        return {
            form,
            seo,
            locale,
            trans,
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

input.error {
    border-color: #dc3545;
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
