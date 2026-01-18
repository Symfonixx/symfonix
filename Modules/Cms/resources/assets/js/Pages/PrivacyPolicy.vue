<template>
    <Head>
           <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'" />
        <title>{{trans('Privacy Policy')}} | {{seo.website_name}}</title>
    </Head>
    <app-layout>

        <!--Page Header Start-->
        <section class="page-header">
            <div class="page-header__bg" :style="{ backgroundImage: `url(${asset_path}images/contact-header-bg.jpg)` }">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans('Privacy Policy') }}</h2>
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
                            <li>{{ trans('Privacy Policy') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!--Page Header End-->

        <!--Privacy Policy Content Start-->
        <section class="privacy-policy my-5">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="privacy-policy__content">
                            <div class="privacy-policy__text">
                                <p class="privacy-policy__last-updated">
                                    <strong>{{ trans('Last Updated:') }}</strong> {{ new Date().toLocaleDateString() }}
                                </p>

                                <h3 class="privacy-policy__heading">{{ trans('1. Introduction') }}</h3>
                                <p>
                                    {{ trans('Welcome to our Privacy Policy. This document explains how we collect, use, disclose, and safeguard your information when you visit our website and use our services. Please read this privacy policy carefully. If you do not agree with the terms of this privacy policy, please do not access the site.') }}
                                </p>

                                <h3 class="privacy-policy__heading">{{ trans('2. Information We Collect') }}</h3>
                                <p>{{ trans('We may collect information about you in a variety of ways. The information we may collect on the site includes:') }}</p>
                                <ul>
                                    <li>
                                        <strong>{{ trans('Personal Data') }}</strong>: {{ trans('Personally identifiable information, such as your name, email address, phone number, and demographic information that you voluntarily give to us when you register with the site or when you choose to participate in various activities related to the site.') }}
                                    </li>
                                    <li>
                                        <strong>{{ trans('Derivative Data') }}</strong>: {{ trans('Information our servers automatically collect when you access the site, such as your IP address, your browser type, your operating system, your access times, and the pages you have viewed directly before and after accessing the site.') }}
                                    </li>
                                    <li>
                                        <strong>{{ trans('Financial Data') }}</strong>: {{ trans('Financial information, such as data related to your payment method (e.g., valid credit card number, card brand, expiration date) that we may collect when you purchase, order, return, exchange, or request information about our services from the site.') }}
                                    </li>
                                    <li>
                                        <strong>{{ trans('Mobile Device Data') }}</strong>: {{ trans('Device information, such as your mobile device ID, model, and manufacturer, and information about the location of your device, if you access the site from a mobile device.') }}
                                    </li>
                                </ul>

                                <h3 class="privacy-policy__heading">{{ trans('3. How We Use Your Information') }}</h3>
                                <p>{{ trans('Having accurate information about you permits us to provide you with a smooth, efficient, and customized experience. Specifically, we may use information collected about you via the site to:') }}</p>
                                <ul>
                                    <li>{{ trans('Create and manage your account') }}</li>
                                    <li>{{ trans('Process your transactions and send you related information') }}</li>
                                    <li>{{ trans('Email you regarding your account or order') }}</li>
                                    <li>{{ trans('Fulfill and manage purchases, orders, payments, and other transactions related to the site') }}</li>
                                    <li>{{ trans('Generate a personal profile about you to make future visits more personalized') }}</li>
                                    <li>{{ trans('Increase the efficiency and operation of the site') }}</li>
                                    <li>{{ trans('Monitor and analyze usage and trends to improve your experience with the site') }}</li>
                                    <li>{{ trans('Notify you of updates to the site') }}</li>
                                    <li>{{ trans('Perform other business activities as needed') }}</li>
                                    <li>{{ trans('Request feedback and contact you about your use of the site') }}</li>
                                    <li>{{ trans('Resolve disputes and troubleshoot problems') }}</li>
                                    <li>{{ trans('Respond to product and customer service requests') }}</li>
                                    <li>{{ trans('Send you a newsletter') }}</li>
                                    <li>{{ trans('Solicit support for the site') }}</li>
                                </ul>

                                <h3 class="privacy-policy__heading">{{ trans('4. Disclosure of Your Information') }}</h3>
                                <p>{{ trans('We may share information we have collected about you in certain situations. Your information may be disclosed as follows:') }}</p>
                                <ul>
                                    <li>
                                        <strong>{{ trans('By Law or to Protect Rights') }}</strong>: {{ trans('If we believe the release of information about you is necessary to respond to legal process, to investigate or remedy potential violations of our policies, or to protect the rights, property, and safety of others, we may share your information as permitted or required by any applicable law, rule, or regulation.') }}
                                    </li>
                                    <li>
                                        <strong>{{ trans('Third-Party Service Providers') }}</strong>: {{ trans('We may share your information with third parties that perform services for us or on our behalf, including payment processing, data analysis, email delivery, hosting services, customer service, and marketing assistance.') }}
                                    </li>
                                    <li>
                                        <strong>{{ trans('Business Transfers') }}</strong>: {{ trans('We may share or transfer your information in connection with, or during negotiations of, any merger, sale of company assets, financing, or acquisition of all or a portion of our business to another company.') }}
                                    </li>
                                    <li>
                                        <strong>{{ trans('Affiliates') }}</strong>: {{ trans('We may share your information with our affiliates, in which case we will require those affiliates to honor this Privacy Policy. Affiliates include our parent company and any subsidiaries, joint venture partners, or other companies that we control or that are under common control with us.') }}
                                    </li>
                                    <li>
                                        <strong>{{ trans('Business Partners') }}</strong>: {{ trans('We may share your information with our business partners to offer you certain products, services, or promotions.') }}
                                    </li>
                                </ul>

                                <h3 class="privacy-policy__heading">{{ trans('5. Security of Your Information') }}</h3>
                                <p>
                                    {{ trans('We use administrative, technical, and physical security measures to help protect your personal information. While we have taken reasonable steps to secure the personal information you provide to us, please be aware that despite our efforts, no security measures are perfect or impenetrable, and no method of data transmission can be guaranteed against any interception or other type of misuse. Any information disclosed online is vulnerable to interception and misuse by unauthorized parties. Therefore, we cannot guarantee complete security if you provide personal information.') }}
                                </p>

                                <h3 class="privacy-policy__heading">{{ trans('6. Policy for Children') }}</h3>
                                <p>
                                    {{ trans('We do not knowingly solicit information from or market to children under the age of 13. If we learn that we have collected personal information from a child under age 13 without verification of parental consent, we will delete that information as quickly as possible. If you become aware of any data we have collected from children under age 13, please contact us.') }}
                                </p>

                                <h3 class="privacy-policy__heading">{{ trans('7. Your Rights') }}</h3>
                                <p>{{ trans('Depending on your location, you may have the following rights regarding your personal information:') }}</p>
                                <ul>
                                    <li>{{ trans('The right to access – You have the right to request copies of your personal data') }}</li>
                                    <li>{{ trans('The right to rectification – You have the right to request that we correct any information you believe is inaccurate') }}</li>
                                    <li>{{ trans('The right to erasure – You have the right to request that we erase your personal data, under certain conditions') }}</li>
                                    <li>{{ trans('The right to restrict processing – You have the right to request that we restrict the processing of your personal data, under certain conditions') }}</li>
                                    <li>{{ trans('The right to object to processing – You have the right to object to our processing of your personal data, under certain conditions') }}</li>
                                    <li>{{ trans('The right to data portability – You have the right to request that we transfer the data that we have collected to another organization, or directly to you, under certain conditions') }}</li>
                                </ul>

                                <h3 class="privacy-policy__heading">{{ trans('8. Cookies and Tracking Technologies') }}</h3>
                                <p>
                                    {{ trans('We may use cookies, web beacons, tracking pixels, and other tracking technologies on the site to help customize the site and improve your experience. When you access the site, your personal information is not collected through the use of tracking technology. Most browsers are set to accept cookies by default. You can remove or reject cookies, but be aware that such action could affect the availability and functionality of the site.') }}
                                </p>

                                <h3 class="privacy-policy__heading">{{ trans('9. Third-Party Websites') }}</h3>
                                <p>
                                    {{ trans('The site may contain links to third-party websites and applications of interest, including advertisements and external services, that are not affiliated with us. Once you have used these links to leave the site, any information you provide to these third parties is not covered by this Privacy Policy, and we cannot guarantee the safety and privacy of your information. Before visiting and providing any information to any third-party websites, you should inform yourself of the privacy policies and practices (if any) of the third party responsible for that website, and should take those steps necessary to, in your discretion, protect the privacy of your information.') }}
                                </p>

                                <h3 class="privacy-policy__heading">{{ trans('10. Changes to This Privacy Policy') }}</h3>
                                <p>
                                    {{ trans('We may update this Privacy Policy from time to time in order to reflect, for example, changes to our practices or for other operational, legal, or regulatory reasons. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date. You are advised to review this Privacy Policy periodically for any changes.') }}
                                </p>

                                <h3 class="privacy-policy__heading">{{ trans('11. Contact Us') }}</h3>
                                <p>
                                    {{ trans('If you have questions or comments about this Privacy Policy, please contact us at:') }}
                                </p>
                                <p>
                                    <strong>{{ trans('Email:') }}</strong> {{ settings?.email  }}<br>
                                    <strong>{{ trans('Phone:') }}</strong> {{ settings?.phone  }}<br>
                                    <strong>{{ trans('Address:') }}</strong> {{ settings?.address  }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Privacy Policy Content End-->

    </app-layout>
</template>

<script setup>
import {computed} from 'vue'
import {Link, usePage, Head} from '@inertiajs/vue3'

const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const seo = computed(() => page.props.seo)
const asset_path = computed(() => page.props.asset_path || '')
const settings = computed(() => page.props.settings || {})
const locale = computed(() => page.props.locale)
</script>

<script>
import AppLayout from '@/Layouts/App.vue';

export default {
    components: {
        AppLayout
    }
};
</script>

<style scoped>

.privacy-policy__last-updated {
    margin-bottom: 30px;

    font-size: 14px;
}

.privacy-policy__heading {

    font-size: 24px;
    font-weight: 700;
    margin-top: 40px;
    margin-bottom: 20px;
    padding-bottom: 10px;

}

.privacy-policy__text p {
    margin-bottom: 20px;
    font-size: 16px;
}

.privacy-policy__text ul {
    margin-bottom: 25px;
    padding-left: 30px;
}

.privacy-policy__text ul li {
    margin-bottom: 12px;
    font-size: 16px;
    line-height: 1.8;
}

.privacy-policy__text ul li strong {
    font-weight: 600;
}

@media (max-width: 768px) {
    .privacy-policy__content {
        padding: 30px 20px;
    }

    .privacy-policy__heading {
        font-size: 20px;
    }

    .privacy-policy__text p,
    .privacy-policy__text ul li {
        font-size: 14px;
    }
}
</style>
