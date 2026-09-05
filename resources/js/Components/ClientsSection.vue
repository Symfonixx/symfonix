<template>
    <section
        v-if="clients.length"
        ref="sectionEl"
        class="clients-one"
        @mouseenter="startSlide"
        @mouseleave="stopSlide"
        @focusin="startSlide"
        @focusout="onFocusOut"
    >
        <div class="container">
            <div class="section-title text-center sec-title-animation animation-style1">
                <div class="section-title__tagline-box">
                    <div class="section-title__tagline-shape-1"></div>
                    <span class="section-title__tagline">{{ trans('Our Clients') }}</span>
                    <div class="section-title__tagline-shape-2"></div>
                </div>
                <h2 class="section-title__title title-animation">
                    {{ trans('Trusted by businesses') }}
                    <span>{{ trans('we partner with') }}</span>
                </h2>
            </div>

            <div class="clients-one__carousel owl-theme owl-carousel">
                <div class="item" v-for="client in clients" :key="client.id">
                    <component
                        :is="client.url ? 'a' : 'div'"
                        class="clients-one__card"
                        :href="client.url || undefined"
                        :target="client.url ? '_blank' : undefined"
                        :rel="client.url ? 'noopener noreferrer' : undefined"
                        :aria-label="client.name"
                    >
                        <span class="clients-one__card-bg" aria-hidden="true">
                            <img :src="client.logo_link" alt="" loading="lazy" decoding="async">
                        </span>
                        <span class="clients-one__card-overlay" aria-hidden="true"></span>
                        <span class="clients-one__card-content">
                            <img
                                class="clients-one__logo"
                                :src="client.logo_link"
                                :alt="client.name"
                                loading="lazy"
                                decoding="async"
                            >
                            <span class="clients-one__name">{{ client.name }}</span>
                        </span>
                    </component>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    clients: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()
const trans = (key) => page.props.translations?.[key] || key
const locale = computed(() => page.props.locale || 'en')
const isRtl = computed(() => locale.value === 'ar')
const sectionEl = ref(null)
let $carousel = null

const startSlide = () => {
    if ($carousel?.hasClass('owl-loaded')) {
        $carousel.trigger('play.owl.autoplay', [3500])
    }
}

const stopSlide = () => {
    if ($carousel?.hasClass('owl-loaded')) {
        $carousel.trigger('stop.owl.autoplay')
    }
}

const onFocusOut = (event) => {
    if (!sectionEl.value?.contains(event.relatedTarget)) {
        stopSlide()
    }
}

const initCarousel = () => {
    if (!sectionEl.value) {
        return false
    }

    if (typeof window.$ === 'undefined' || typeof window.$.fn?.owlCarousel !== 'function') {
        return false
    }

    const $el = window.$(sectionEl.value).find('.clients-one__carousel')
    if (!$el.length) {
        return false
    }

    if ($el.hasClass('owl-loaded')) {
        $carousel = $el
        return true
    }

    $carousel = $el.owlCarousel({
        loop: props.clients.length > 3,
        margin: 24,
        nav: false,
        dots: false,
        smartSpeed: 650,
        autoplay: true,
        autoplayTimeout: 3500,
        autoplayHoverPause: false,
        rtl: isRtl.value,
        responsive: {
            0: { items: 1 },
            576: { items: 2 },
            992: { items: 3 },
            1200: { items: 3 },
        },
    })

    $carousel.trigger('stop.owl.autoplay')

    return true
}

onMounted(() => {
    if (!props.clients.length) {
        return
    }

    nextTick(() => {
        let tries = 40
        const tick = () => {
            if (initCarousel()) {
                return
            }
            if (tries-- <= 0) {
                return
            }
            setTimeout(tick, 150)
        }
        tick()
    })
})

onBeforeUnmount(() => {
    if ($carousel?.hasClass('owl-loaded')) {
        $carousel.trigger('destroy.owl.carousel')
    }
})
</script>
