<template>
    <Head>
        <title>{{ trans('FAQ') }} | {{ seo.website_name }}</title>
    </Head>
    <app-layout>

        <!--Page Header Start-->
        <section class="page-header">
            <div class="page-header__bg"
                :style="{ backgroundImage: `url(${asset_path}images/backgrounds/page-header-bg.jpg)` }">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans('FAQ\'s') }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li>
                                <Link :href="route('home')" v-if="typeof route !== 'undefined'">
                                    <i class="fas fa-home"></i>{{ trans('Home') }}
                                </Link>
                                <a :href="`/${locale === 'ar' ? 'ar' : ''}`" v-else>
                                    <i class="fas fa-home"></i>{{ trans('Home') }}
                                </a>
                            </li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`""></span></li>
                            <li>{{ trans('FAQ\'s') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!--Page Header End-->

        <!--FAQ Page Start-->
        <section class="faq-two faq-page">
            <div class="container">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <div class="section-title__tagline-shape-1"></div>
                        <span class="section-title__tagline">{{ trans('FAQS') }}</span>
                        <div class="section-title__tagline-shape-2"></div>
                    </div>
                    <h2 class="section-title__title title-animation">
                        {{ trans('Get answers to the most common') }}<br>
                        {{ trans('questions about our products, services,') }}<br>
                        {{ trans('and policies.') }}
                    </h2>
                </div>
                <div class="faq-two__right">
                    <div class="accrodion-grp" data-grp-name="faq-one-accrodion">
                        <div class="accrodion" v-for="(faq, index) in faqs" :key="index"
                            :class="{ 'active': index === 0 }">
                            <div class="accrodion-title">
                                <h4>{{ translateField(faq.question) }}</h4>
                            </div>
                            <div class="accrodion-content">
                                <div class="inner">
                                    <p class="accrodion-content__text-1">{{ translateField(faq.answer) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--FAQ Page End-->

        <!--CTA Two Start-->
        <section class="cta-two">
            <div class="cta-two__bg-color">
                <div class="cta-two__shape-1 img-bounce">
                    <img :src="asset_path + 'images/shapes/cta-two-shape-1.png'" alt="">
                </div>
                <div class="cta-two__shape-2 float-bob-y">
                    <img :src="asset_path + 'images/shapes/cta-two-shape-2.png'" alt="">
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-7 col-lg-7">
                        <div class="cta-two__left">
                            <div class="section-title text-left sec-title-animation animation-style2">
                                <div class="section-title__tagline-box">
                                    <div class="section-title__tagline-shape-1"></div>
                                    <span class="section-title__tagline">{{ trans('Get In Touch') }}</span>
                                    <div class="section-title__tagline-shape-2"></div>
                                </div>
                                <h2 class="section-title__title title-animation">
                                    {{ trans('If you have any questions,') }}<br>
                                    {{ trans('please contact us.') }}
                                </h2>
                            </div>
                            <div class="cta-two__btn-box">
                                <Link :href="route('contact-us')" class="thm-btn" v-if="typeof route !== 'undefined'">
                                    {{ trans('Get in Touch') }}
                                    <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                                </Link>
                                <a :href="`/${locale === 'ar' ? 'ar' : ''}/contact-us`" class="thm-btn" v-else>
                                    {{ trans('Get in Touch') }}
                                    <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-5">
                        <div class="cta-two__right wow slideInRight" data-wow-delay="100ms" data-wow-duration="2500ms">
                            <h3 class="cta-two__title">{{ trans('Quick Support') }}</h3>
                            <p class="cta-two__text">
                                {{ trans('Get Instant and Reliable Support Anytime, Anywhere with Ease') }}
                            </p>
                            <ul class="cta-two__list list-unstyled">
                                <li>
                                    <div class="icon">
                                        <span class="icon-mail"></span>
                                    </div>
                                    <div class="content">
                                        <h4>{{ trans('Email Us') }}</h4>
                                        <p v-if="settings.contact_email">
                                            <a :href="`mailto:${settings.contact_email}`">{{ settings.contact_email }}</a>
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-phone-call"></span>
                                    </div>
                                    <div class="content">
                                        <h4>{{ trans('Phone') }}</h4>
                                        <p v-if="settings.contact_phone">
                                            <a :href="`tel:${settings.contact_phone}`">{{ settings.contact_phone }}</a>
                                        </p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--CTA Two End-->

        <PartnersBrand />
    </app-layout>
</template>

<script setup>
import { computed, onMounted, nextTick } from 'vue'
import { Link, usePage, Head } from '@inertiajs/vue3'
import PartnersBrand from '@/Components/PartnersBrand.vue'

const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const seo = computed(() => page.props.seo)
const settings = computed(() => page.props.settings)
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale)

// Sample FAQ data - you can replace this with dynamic data from backend
const faqs = computed(() => [
    {
        question: {
            en: 'What services does your IT consultancy agency provide?',
            ar: 'ما هي الخدمات التي تقدمها وكالة الاستشارات التقنية الخاصة بك؟'
        },
        answer: {
            en: 'We offer a wide range of IT consulting services, including software development, cloud computing solutions, cybersecurity, IT infrastructure management, and digital transformation strategies tailored to your business needs.',
            ar: 'نقدم مجموعة واسعة من خدمات الاستشارات التقنية، بما في ذلك تطوير البرمجيات وحلول الحوسبة السحابية والأمن السيبراني وإدارة البنية التحتية لتكنولوجيا المعلومات واستراتيجيات التحول الرقمي المصممة خصيصًا لاحتياجات عملك.'
        }
    },
    {
        question: {
            en: 'How can IT consulting benefit my business?',
            ar: 'كيف يمكن أن تفيد الاستشارات التقنية عملي؟'
        },
        answer: {
            en: 'Our IT consulting services help businesses improve efficiency, enhance security, reduce operational costs, and stay ahead of technology trends. We provide expert guidance to optimize your IT infrastructure and implement innovative solutions.',
            ar: 'تساعد خدمات الاستشارات التقنية لدينا الشركات على تحسين الكفاءة وتعزيز الأمان وتقليل التكاليف التشغيلية والبقاء في المقدمة من اتجاهات التكنولوجيا. نقدم إرشادات خبيرة لتحسين البنية التحتية لتكنولوجيا المعلومات وتنفيذ حلول مبتكرة.'
        }
    },
    {
        question: {
            en: 'Do you offer customized IT solutions?',
            ar: 'هل تقدمون حلول تقنية مخصصة؟'
        },
        answer: {
            en: 'Yes, we specialize in creating customized IT solutions that are specifically designed to meet your unique business requirements and objectives.',
            ar: 'نعم، نحن متخصصون في إنشاء حلول تقنية مخصصة مصممة خصيصًا لتلبية متطلبات وأهداف عملك الفريدة.'
        }
    },
    {
        question: {
            en: 'How do you ensure data security and compliance?',
            ar: 'كيف تضمنون أمان البيانات والامتثال؟'
        },
        answer: {
            en: 'We implement industry-standard security measures, conduct regular security audits, and ensure compliance with relevant regulations such as GDPR, HIPAA, and other data protection standards.',
            ar: 'نطبق تدابير أمنية معيارية في الصناعة، ونقوم بإجراءات تدقيق أمنية منتظمة، ونتأكد من الامتثال للوائح ذات الصلة مثل GDPR وHIPAA ومعايير حماية البيانات الأخرى.'
        }
    },
    {
        question: {
            en: 'What IT services do you provide?',
            ar: 'ما هي الخدمات التقنية التي تقدمونها؟'
        },
        answer: {
            en: 'We provide comprehensive IT services including cloud migration, network security, software development, system integration, IT support, and strategic technology consulting.',
            ar: 'نقدم خدمات تقنية شاملة بما في ذلك الهجرة السحابية وأمان الشبكة وتطوير البرمجيات وتكامل الأنظمة ودعم تكنولوجيا المعلومات والاستشارات التقنية الاستراتيجية.'
        }
    },
    {
        question: {
            en: 'How do I get started with your IT solutions?',
            ar: 'كيف أبدأ مع حلولكم التقنية؟'
        },
        answer: {
            en: 'Getting started is easy! Simply contact us through our website or phone, and our team will schedule a consultation to understand your needs and provide a tailored solution proposal.',
            ar: 'البدء سهل! ما عليك سوى الاتصال بنا من خلال موقعنا الإلكتروني أو الهاتف، وسيقوم فريقنا بجدولة استشارة لفهم احتياجاتك وتقديم اقتراح حل مخصص.'
        }
    }
]);

const translateField = (value) => {
    if (!value) {
        return '';
    }
    if (typeof value === 'string') {
        return value;
    }
    const loc = locale.value;
    if (typeof value === 'object' && value !== null) {
        if (value[loc]) {
            return value[loc];
        }
        // Fallback to English if current locale not available
        if (value.en) {
            return value.en;
        }
    }
    return '';
};

onMounted(() => {
    nextTick(() => {
        // Initialize WOW animations
        if (typeof WOW !== 'undefined') {
            new WOW().init();
        }

        // Initialize accordion functionality
        if (typeof $ !== 'undefined') {
            $('.accrodion-grp').each(function () {
                const accrodionGrp = $(this);
                const accrodionName = accrodionGrp.data('grp-name');
                const accrodion = accrodionGrp.find('.accrodion');

                accrodion.each(function () {
                    const accrodionSingle = $(this);
                    accrodionSingle.children('.accrodion-title').on('click', function () {
                        const accrodionTitle = $(this);
                        if (false === accrodionSingle.hasClass('active')) {
                            accrodion.addClass('accrodion').removeClass('active');
                            accrodionSingle.addClass('active');
                        }
                        if (true === accrodionSingle.hasClass('active')) {
                            accrodionSingle.removeClass('active');
                        }
                    });
                });
            });
        }
    });
});
</script>

<script>
import AppLayout from '@/Layouts/App.vue';

export default {
    components: {
        AppLayout
    }
};
</script>
