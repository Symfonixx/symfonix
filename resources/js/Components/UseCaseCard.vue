<template>
    <article
        class="use-case-card"
        :class="[`use-case-card--${variant}`, { 'use-case-card--featured': item.featured }]"
    >
        <div class="use-case-card__glow" aria-hidden="true"></div>

        <header class="use-case-card__header">
            <Link :href="cardUrl" class="use-case-card__avatar" :aria-label="item.title">
                <img :src="item.image_link" :alt="item.title" width="96" height="96" loading="lazy" decoding="async">
            </Link>
            <div class="use-case-card__identity">
                <h3 class="use-case-card__title">
                    <Link :href="cardUrl">{{ item.title }}</Link>
                </h3>
                <p v-if="item.client_name" class="use-case-card__subtitle">{{ item.client_name }}</p>
                <p v-else-if="item.category_tag" class="use-case-card__subtitle">{{ item.category_tag }}</p>
            </div>
        </header>

        <div v-if="item.summary && variant !== 'compact'" class="use-case-card__section">
            <div class="use-case-card__section-head">
                <span class="use-case-card__section-icon"><i class="fas fa-lightbulb"></i></span>
                <span class="use-case-card__section-label">{{ trans('Overview') }}</span>
            </div>
            <p class="use-case-card__summary">{{ truncate(item.summary, variant === 'compact' ? 90 : 140) }}</p>
        </div>

        <div
            v-if="item.category_tag || (item.technologies && item.technologies.length)"
            class="use-case-card__section use-case-card__section--tags"
        >
            <div class="use-case-card__section-head">
                <span class="use-case-card__section-icon"><i class="fas fa-code"></i></span>
                <span class="use-case-card__section-label">{{ trans('Technologies') }}</span>
            </div>
            <div class="use-case-card__tags">
                <span v-if="item.category_tag" class="use-case-card__tag use-case-card__tag--category">
                    {{ item.category_tag }}
                </span>
                <span
                    v-for="tech in (item.technologies || []).slice(0, variant === 'compact' ? 2 : 4)"
                    :key="tech"
                    class="use-case-card__tag"
                >
                    {{ tech }}
                </span>
            </div>
        </div>

        <footer class="use-case-card__footer">
            <div v-if="item.completed_year" class="use-case-card__pill">
                <i class="far fa-calendar-alt"></i>
                <span>{{ item.completed_year }}</span>
            </div>
            <Link :href="cardUrl" class="use-case-card__pill use-case-card__pill--cta">
                <span>{{ trans('View Case Study') }}</span>
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
        return route('use-cases.show', props.item.slug)
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
.use-case-card {
    --uc-blue: #2189ca;
    --uc-accent: #7fc457;
    --uc-dark: #0b192c;
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 0;
    height: 100%;
    padding: 24px;
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

.use-case-card:hover {
    transform: translateY(-8px);
    border-color: rgba(33, 137, 202, 0.45);
    box-shadow:
        0 24px 48px rgba(11, 25, 44, 0.45),
        0 0 0 1px rgba(33, 137, 202, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.16);
}

.use-case-card__glow {
    position: absolute;
    inset: -40% -20% auto;
    height: 180px;
    background: radial-gradient(circle, rgba(33, 137, 202, 0.35) 0%, rgba(127, 196, 87, 0.08) 45%, transparent 70%);
    pointer-events: none;
}

.use-case-card__header {
    position: relative;
    display: flex;
    align-items: center;
    gap: 16px;
    padding-bottom: 18px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.use-case-card__avatar {
    flex-shrink: 0;
    width: 72px;
    height: 72px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.25);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
    background: linear-gradient(135deg, var(--uc-blue), var(--uc-accent));
}

.use-case-card__avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.4s ease;
}

.use-case-card:hover .use-case-card__avatar img {
    transform: scale(1.08);
}

.use-case-card__identity {
    min-width: 0;
}

.use-case-card__title {
    margin: 0 0 6px;
    font-size: 1.15rem;
    font-weight: 700;
    line-height: 1.35;
}

.use-case-card__title a {
    color: #fff;
    text-decoration: none;
    transition: color 0.2s ease;
}

.use-case-card__title a:hover {
    color: var(--uc-accent);
}

.use-case-card__subtitle {
    margin: 0;
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.72);
    line-height: 1.4;
}

.use-case-card__section {
    position: relative;
    padding: 16px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.use-case-card__section-head {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
}

.use-case-card__section-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(33, 137, 202, 0.25);
    color: #fff;
    font-size: 0.75rem;
}

.use-case-card__section-label {
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.85);
}

.use-case-card__summary {
    margin: 0;
    font-size: 0.925rem;
    line-height: 1.65;
    color: rgba(255, 255, 255, 0.78);
}

.use-case-card__tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.use-case-card__tag {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 500;
    color: #fff;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.14);
    backdrop-filter: blur(8px);
}

.use-case-card__tag--category {
    background: rgba(33, 137, 202, 0.28);
    border-color: rgba(33, 137, 202, 0.4);
}

.use-case-card__footer {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    padding-top: 18px;
    margin-top: auto;
}

.use-case-card__pill {
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

.use-case-card__pill--cta {
    margin-inline-start: auto;
    background: rgba(33, 137, 202, 0.35);
    border-color: rgba(33, 137, 202, 0.5);
    color: #fff;
}

.use-case-card__pill--cta:hover {
    background: var(--uc-blue);
    border-color: var(--uc-blue);
    color: #fff;
}

.use-case-card--compact {
    padding: 20px;
}

.use-case-card--compact .use-case-card__avatar {
    width: 56px;
    height: 56px;
}

.use-case-card--compact .use-case-card__title {
    font-size: 1rem;
}

.use-case-card--compact .use-case-card__section {
    padding: 12px 0;
}

.use-case-card--featured::before {
    content: '';
    position: absolute;
    top: 16px;
    right: 16px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--uc-accent);
    box-shadow: 0 0 12px var(--uc-accent);
}
</style>
