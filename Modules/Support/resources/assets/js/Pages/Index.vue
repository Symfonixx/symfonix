<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'"/>
        <title>{{trans("Contact Us") }} | {{seo.website_name}}</title>
    </Head>
    <app-layout>


        <section class="page-header">
            <div class="page-header__bg" :style="{ backgroundImage: `url(${asset_path}images/contact-header-bg.jpg)`}">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans("Contact Us") }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="/"><i class="fas fa-home"></i>{{ trans("Home") }}</a></li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`""></span></li>
                            <li>{{ trans("Contact Us") }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact One Start -->
        <section class="contact-one">
            <div class="contact-one__bg-shape"
                 :style="{ backgroundImage: `url(${asset_path}site/images/shapes/contact-one-bg-shape.png)`}">

            </div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-6 col-lg-6">
                        <div :class="`contact-one__left wow slideIn${locale !== 'ar' ? 'Left' : 'Right'}`" data-wow-delay="100ms"
                             data-wow-duration="2500ms">
                            <div class="contact-one__left-shape-1"></div>
                            <div class="contact-one__left-shape-2"></div>
                            <h3 class="contact-one__from-title">{{ trans("How Can We Help You?") }}</h3>
                            <form @submit.prevent="handleSubmit" class="contact-one__form">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6">
                                        <h4 class="contact-one__input-title">{{ trans("Full Name") }}</h4>
                                        <div class="contact-one__input-box">
                                            <div class="contact-one__input-icon">
                                                <span class="icon-user-1"></span>
                                            </div>
                                            <input
                                                v-model="contactForm.name"
                                                type="text"
                                                name="name"
                                                :placeholder="trans('Full Name')"
                                                :class="{ 'error': contactForm.errors.name }"
                                                :disabled="contactForm.processing"
                                                required>
                                        </div>
                                        <div v-if="contactForm.errors.name" class="text-danger mt-1 small">
                                            {{ contactForm.errors.name }}
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <h4 class="contact-one__input-title">{{ trans("Email") }}</h4>
                                        <div class="contact-one__input-box">
                                            <div class="contact-one__input-icon">
                                                <span class="icon-email"></span>
                                            </div>
                                            <input
                                                v-model="contactForm.email"
                                                type="email"
                                                name="email"
                                                :placeholder="trans('Email')"
                                                :class="{ 'error': contactForm.errors.email }"
                                                :disabled="contactForm.processing"
                                                required>
                                        </div>
                                        <div v-if="contactForm.errors.email" class="text-danger mt-1 small">
                                            {{ contactForm.errors.email }}
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <h4 class="contact-one__input-title">{{ trans("Phone Number") }}</h4>
                                        <div class="contact-one__input-box">
                                            <div class="contact-one__input-icon">
                                                <span class="icon-phone-call"></span>
                                            </div>
                                            <input
                                                v-model="contactForm.mobile"
                                                type="text"
                                                name="mobile"
                                                :placeholder="trans('Phone Number')"
                                                :class="{ 'error': contactForm.errors.mobile }"
                                                :disabled="contactForm.processing"
                                                required>
                                        </div>
                                        <div v-if="contactForm.errors.mobile" class="text-danger mt-1 small">
                                            {{ contactForm.errors.mobile }}
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <h4 class="contact-one__input-title">{{ trans("Subject") }}</h4>
                                        <div class="contact-one__input-box">
                                            <div class="contact-one__input-icon">
                                                <span class="icon-edit"></span>
                                            </div>
                                            <input
                                                v-model="contactForm.subject"
                                                type="text"
                                                name="subject"
                                                :placeholder="trans('Subject')"
                                                :class="{ 'error': contactForm.errors.subject }"
                                                :disabled="contactForm.processing"
                                                required>
                                        </div>
                                        <div v-if="contactForm.errors.subject" class="text-danger mt-1 small">
                                            {{ contactForm.errors.subject }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <h4 class="contact-one__input-title">{{ trans("Message") }}</h4>
                                    <div class="contact-one__input-box text-message-box">
                                        <div class="contact-one__input-icon">
                                            <span class="icon-edit"></span>
                                        </div>
                                        <textarea
                                            v-model="contactForm.message"
                                            name="message"
                                            :placeholder="trans('Write your message')"
                                            :class="{ 'error': contactForm.errors.message }"
                                            :disabled="contactForm.processing"
                                            required></textarea>
                                    </div>
                                    <div v-if="contactForm.errors.message" class="text-danger mt-1 small">
                                        {{ contactForm.errors.message }}
                                    </div>
                                    <div class="contact-one__btn-box">
                                        <button
                                            type="submit"
                                            class="thm-btn"
                                            :disabled="contactForm.processing"
                                            :class="{ 'opacity-50': contactForm.processing }">
                                            <span v-if="contactForm.processing">
                                                <i class="fa-solid fa-spinner fa-spin me-2"></i>{{
                                                    trans("Sending...")
                                                }}
                                            </span>
                                            <span v-else>
                                                <span>{{ trans("Submit") }}</span> <i :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow mx-1`"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                <div v-if="submitSuccess" class="col-12 mt-3">
                                    <div class="alert alert-success">
                                        {{ trans("Thank you for contacting us! We will get back to you soon.") }}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="contact-one__right">
                            <div class="section-title text-left sec-title-animation animation-style2">
                                <div class="section-title__tagline-box">
                                    <div class="section-title__tagline-shape-1"></div>
                                    <span class="section-title__tagline">{{ trans("Get In Touch") }}</span>
                                    <div class="section-title__tagline-shape-2"></div>
                                </div>
                                <h2  class="section-title__title title-animation"
                                    v-html="trans('Start the Conversation')+ '<span>–</span><br><span>' + trans('Reach Out Anytime') +'</span>'"></h2>


                            </div>
                            <p  class="contact-one__text">{{
                                    trans("We're here to listen! Whether you have questions, feedback, or just want to say hello, feel free to reach out")
                                }}</p>


                            <ul class="contact-one__list list-unstyled">
                                <li>
                                    <div class="icon">
                                        <span class="icon-pin"></span>
                                    </div>
                                    <div class="content">
                                        <h4>{{ trans('Our Location') }}</h4>
                                        <p>{{ settings.address }}</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-mail"></span>
                                    </div>
                                    <div class="content">
                                        <h4>{{ trans('Email') }}</h4>
                                        <p><a dir="ltr" href="mailto:{{settings.email}}">{{ settings.email }}</a></p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-phone-call"></span>
                                    </div>
                                    <div class="content">
                                        <h4>{{ trans('Phone') }}</h4>
                                        <p><a dir="ltr" href="tel:{{settings.phone}}">{{ settings.phone }}</a></p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Contact One End -->


    </app-layout>
</template>

<script setup>
import {computed, onMounted, nextTick, ref} from 'vue'
import {usePage, useForm, router, Head} from '@inertiajs/vue3'

const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const seo = computed(() => page.props.seo)
const settings = computed(() => page.props.settings)
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale || 'en')
const submitSuccess = ref(false)

const contactForm = useForm({
    name: '',
    email: '',
    mobile: '',
    subject: '',
    message: '',
})

const handleSubmit = () => {
    if (contactForm.processing) {
        return false;
    }

    // Validate required fields
    if (!contactForm.name || !contactForm.name.trim()) {
        return false;
    }

    if (!contactForm.email || !contactForm.email.trim()) {
        return false;
    }

    if (!contactForm.mobile || !contactForm.mobile.trim()) {
        return false;
    }

    if (!contactForm.subject || !contactForm.subject.trim()) {
        return false;
    }

    if (!contactForm.message || !contactForm.message.trim()) {
        return false;
    }

    let contactUrl = '/contact-us';
    try {
        if (typeof route !== 'undefined' && route) {
            contactUrl = route('contact-us.store');
        } else {
            const currentLocale = page.props.locale || '';
            contactUrl = currentLocale ? `/${currentLocale}/contact-us` : '/contact-us';
        }
    } catch (e) {
        const currentLocale = page.props.locale || '';
        contactUrl = currentLocale ? `/${currentLocale}/contact-us` : '/contact-us';
    }

    contactForm.post(contactUrl, {
        preserveScroll: true,
        preserveState: true,
        onBefore: () => {
            submitSuccess.value = false;
        },
        onSuccess: () => {
            submitSuccess.value = true;
            contactForm.reset();
            contactForm.clearErrors();
            setTimeout(() => {
                submitSuccess.value = false;
            }, 5000);
        },
        onError: () => {
            submitSuccess.value = false;
        },
    });

    return false;
}


</script>

<script>


import AppLayout from '@/Layouts/App.vue';

export default {
    components: {
        AppLayout
    }

};
</script>


