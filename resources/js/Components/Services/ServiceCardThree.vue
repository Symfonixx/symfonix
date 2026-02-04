<template>
    <div class="services-three__single">
        <div class="services-three__media">
            <img v-if="image" :src="image" :alt="title" class="services-three__image">
            <div v-else class="services-three__image-placeholder">
                <span class="icon-technical-support"></span>
            </div>
        </div>
        <h3 class="services-three__title">
            <Link :href="link">{{ title }}</Link>
        </h3>
        <p v-if="shortDescription" class="services-three__text">{{ shortDescription }}</p>
        <p v-if="readingTime" class="services-three__meta">
            <span class="far fa-clock mx-1"></span>{{ readingTime }} {{ readingTimeLabel }}
        </p>
        <ul v-if="safeHighlights.length" class="list-unstyled services-three__list">
            <li v-for="(item, index) in safeHighlights" :key="index">
                <div class="icon">
                    <span class="icon-tick-inside-circle"></span>
                </div>
                <div class="text">
                    <p>{{ item }}</p>
                </div>
            </li>
        </ul>
        <Link :href="link" class="services-three__btn">
            {{ buttonText }}
            <span :class="`icon-${isRtl ? 'left' : 'right'}-arrow-1`"></span>
        </Link>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    title: { type: String, required: true },
    description: { type: String, default: '' },
    highlights: { type: Array, default: () => [] },
    link: { type: String, required: true },
    image: { type: String, default: '' },
    buttonLabel: { type: String, default: 'Read More' },
    isRtl: { type: Boolean, default: false },
    readingTime: { type: [Number, String], default: 0 },
    readingTimeLabel: { type: String, default: 'min read' },
})

const parseMaybeJson = (value) => {
    if (typeof value !== 'string') {
        return value
    }
    const trimmed = value.trim()
    if (!trimmed.startsWith('{') && !trimmed.startsWith('[')) {
        return value
    }
    try {
        return JSON.parse(trimmed)
    } catch (e) {
        try {
            return JSON.parse(trimmed.replace(/'/g, '"'))
        } catch (err) {
            return value
        }
    }
}

const normalizeHighlights = (items) => {
    if (!items) {
        return []
    }
    const rawItems = Array.isArray(items) ? items : [items]
    return rawItems
        .map((item) => parseMaybeJson(item))
        .flatMap((item) => {
            if (Array.isArray(item)) {
                return item
            }
            return [item]
        })
        .map((item) => {
            if (typeof item === 'string') {
                return item
            }
            if (item && typeof item === 'object') {
                if (item.value) {
                    return item.value
                }
                if (item.label) {
                    return item.label
                }
                return JSON.stringify(item)
            }
            return ''
        })
        .map((item) => String(item).replace(/^\s+|\s+$/g, ''))
        .filter(Boolean)
}

const safeHighlights = computed(() => {
    const normalized = normalizeHighlights(props.highlights)
    return normalized.slice(0, 3)
})

const buttonText = computed(() => {
    if (props.buttonLabel && props.buttonLabel !== 'Read More') {
        return props.buttonLabel
    }
    const labelTitle = String(props.title || '').trim()
    if (!labelTitle) {
        return 'Read More'
    }
    return `Read More about ${labelTitle}`
})

const shortDescription = computed(() => {
    if (!props.description) {
        return ''
    }
    const text = String(props.description).replace(/\s+/g, ' ').trim()
    if (text.length <= 140) {
        return text
    }
    return `${text.slice(0, 140)}...`
})
</script>

<style scoped>
.services-three__media {
    margin-bottom: 20px;
}

.services-three__image {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 18px;
}

.services-three__image-placeholder {
    width: 100%;
    height: 160px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
}

.services-three__meta {
    margin: 0 0 12px;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.75);
}
</style>
