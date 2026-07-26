<template>
    <article
        class="product-card"
        :class="[`product-card--${variant}`, { 'product-card--featured': item.is_featured }]"
    >
        <div class="product-card__glow" aria-hidden="true"></div>

        <Link :href="cardUrl" class="product-card__media" :aria-label="item.name">
            <img :src="item.main_image_link" :alt="item.name" width="640" height="400" loading="lazy" decoding="async">
            <div class="product-card__media-overlay" aria-hidden="true"></div>
            <span v-if="item.is_featured" class="product-card__badge">
                <i class="fas fa-star"></i>
                {{ trans('Featured') }}
            </span>
        </Link>

        <div class="product-card__body">
            <p v-if="item.category" class="product-card__category">{{ item.category.name }}</p>

            <h3 class="product-card__title">
                <Link :href="cardUrl">{{ item.name }}</Link>
            </h3>

            <p v-if="item.short_description && variant !== 'compact'" class="product-card__summary">
                {{ truncate(item.short_description, variant === 'compact' ? 80 : 130) }}
            </p>
        </div>

        <footer class="product-card__footer">
            <Link :href="cardUrl" class="product-card__pill product-card__pill--cta">
                <span>{{ trans('View Details') }}</span>
                <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow`"></span>
            </Link>
        </footer>
    </article>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    locale: {
        type: String,
        default: 'en',
    },
    variant: {
        type: String,
        default: 'default',
    },
})

const page = usePage()
const trans = (key) => page.props.translations?.[key] || key

const cardUrl = computed(() => {
    if (!props.item?.slug) {
        return '#'
    }

    try {
        return route('product.show', props.item.slug)
    } catch {
        return '#'
    }
})

const truncate = (text, length) => {
    if (!text) {
        return ''
    }

    return text.length > length ? `${text.substring(0, length)}…` : text
}
</script>

<style scoped>
.product-card {
    --product-blue: #2189ca;
    --product-accent: #7fc457;
    position: relative;
    display: flex;
    flex-direction: column;
    height: 100%;
    border-radius: 28px;
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.14);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    box-shadow:
        0 8px 32px rgba(11, 25, 44, 0.35),
        inset 0 1px 0 rgba(255, 255, 255, 0.12);
    overflow: hidden;
    transition: transform 0.35s ease, border-color 0.35s ease, box-shadow 0.35s ease;
}

.product-card:hover {
    transform: translateY(-8px);
    border-color: rgba(33, 137, 202, 0.45);
    box-shadow:
        0 24px 48px rgba(11, 25, 44, 0.45),
        0 0 0 1px rgba(33, 137, 202, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.16);
}

.product-card__glow {
    position: absolute;
    inset: -40% -20% auto;
    height: 180px;
    background: radial-gradient(circle, rgba(33, 137, 202, 0.35) 0%, rgba(127, 196, 87, 0.08) 45%, transparent 70%);
    pointer-events: none;
}

.product-card__media {
    position: relative;
    display: block;
    aspect-ratio: 16 / 10;
    overflow: hidden;
    text-decoration: none;
}

.product-card__media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.45s ease;
}

.product-card:hover .product-card__media img {
    transform: scale(1.06);
}

.product-card__media-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 45%, rgba(11, 25, 44, 0.55) 100%);
    pointer-events: none;
}

.product-card__badge {
    position: absolute;
    top: 14px;
    left: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    color: #fff;
    background: rgba(127, 196, 87, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(8px);
}

.product-card__body {
    position: relative;
    flex: 1;
    padding: 20px 22px 0;
}

.product-card__category {
    margin: 0 0 8px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--product-accent);
}

.product-card__title {
    margin: 0 0 10px;
    font-size: 1.15rem;
    font-weight: 700;
    line-height: 1.35;
}

.product-card__title a {
    color: #fff;
    text-decoration: none;
    transition: color 0.2s ease;
}

.product-card__title a:hover {
    color: var(--product-accent);
}

.product-card__summary {
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.65;
    color: rgba(255, 255, 255, 0.75);
}

.product-card__footer {
    position: relative;
    display: flex;
    align-items: center;
    padding: 18px 22px 22px;
    margin-top: auto;
}

.product-card__pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9);
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.12);
    text-decoration: none;
    transition: background 0.25s ease, border-color 0.25s ease, color 0.25s ease;
}

.product-card__pill--cta {
    width: 100%;
    justify-content: center;
    background: rgba(33, 137, 202, 0.35);
    border-color: rgba(33, 137, 202, 0.5);
    color: #fff;
}

.product-card__pill--cta:hover {
    background: var(--product-blue);
    border-color: var(--product-blue);
    color: #fff;
}

.product-card--compact .product-card__body {
    padding: 16px 18px 0;
}

.product-card--compact .product-card__footer {
    padding: 14px 18px 18px;
}

.product-card--compact .product-card__title {
    font-size: 1rem;
}

.product-card--compact .product-card__media {
    aspect-ratio: 16 / 9;
}

.product-card--featured {
    border-color: rgba(127, 196, 87, 0.35);
}
</style>
