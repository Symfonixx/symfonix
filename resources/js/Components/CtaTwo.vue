<template>
    <!--CTA Two Start-->
    <section class="cta-two">
        <div class="cta-two__bg-color">
            <div class="cta-two__shape-1 img-bounce">
                <img :src="asset_path + 'site/images/shapes/cta-two-shape-1.png'" alt="">
            </div>
            <div class="cta-two__shape-2 float-bob-y">
                <img :src="asset_path + 'site/images/shapes/cta-two-shape-2.png'" alt="">
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xl-7 col-lg-7">
                    <div class="cta-two__left">
                        <div class="section-title text-left sec-title-animation animation-style2">
                            <div class="section-title__tagline-box">
                                <div class="section-title__tagline-shape-1"></div>
                                <span class="section-title__tagline">{{ trans('Start now') }}</span>
                                <div class="section-title__tagline-shape-2"></div>
                            </div>
                            <h2 class="section-title__title title-animation">
                                {{ trans('Are you ready to take your project to the next stage?') }}
                            </h2>
                        </div>
                        <div class="cta-two__btn-box">
                            <Link :href="route('contact-us')" class="thm-btn">
                                {{ trans('Book your free consultation') }}
                                <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
                            </Link>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-5">
                    <div :class="`cta-two__right wow slideIn${locale === 'ar' ? 'Left' : 'Right'}`" data-wow-delay="100ms" data-wow-duration="2500ms">
                        <h3 class="cta-two__title">{{ trans('24/7 Technical Support') }}</h3>
                        <p class="cta-two__text">{{ trans('Professional technical support and immediate solutions for your project to ensure the uninterrupted continuity of your business.') }}</p>
                        <ul class="cta-two__list list-unstyled">
                            <li>
                                <div class="icon">
                                    <span class="icon-mail"></span>
                                </div>
                                <div class="content">
                                    <h4>{{ trans('Contact our experts') }}</h4>
                                    <p><a :href="`mailto:${settings.email}`">{{ settings.email }}</a></p>
                                </div>
                            </li>
                            <li>
                                <div class="icon">
                                    <span class="icon-phone-call"></span>
                                </div>
                                <div class="content">
                                    <h4>{{ trans('Contact Us') }}</h4>
                                    <p>{{ trans('Tel:') }} <a :href="`tel:${settings.phone}`">{{ settings.phone }}</a></p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--CTA Two End-->
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const asset_path = computed(() => page.props.asset_path || '')
const settings = computed(() => page.props.settings || {})
const locale = computed(() => page.props.locale)
</script>
