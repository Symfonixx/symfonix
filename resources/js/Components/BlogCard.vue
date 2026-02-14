<template>
    <div class="blog-one__single">
        <div class="blog-one__img">
            <Link :href="postUrl">
                <img :src="blog.image_link" :alt="blog.title" @error="handleImageError">
            </Link>
            <div v-if="showCategory && blog.category" class="blog-one__tags">
                <span>{{ blog.category.name }}</span>
            </div>
        </div>
        <div class="blog-one__content">
            <ul class="blog-one__meta list-unstyled">
                <li v-if="dateText">
                    <Link :href="postUrl"><span class="far fa-calendar-alt"></span>{{ dateText }}</Link>
                </li>
                <li v-if="showComments">
                    <Link :href="postUrl"><span class="fal fa-comments"></span>{{ commentsCount }} {{ trans("Comments") }}</Link>
                </li>
            </ul>
            <h3 class="blog-one__title"><Link :href="postUrl">{{ blog.title }}</Link></h3>
            <p v-if="showDescription" class="blog-one__text">{{ blog.description }}</p>
            <div v-if="showReadMore" class="blog-one__btn-box">
                <Link :href="postUrl" class="thm-btn">
                    {{ trans("Read More") }}
                    <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow `"></span>
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
    blog: {
        type: Object,
        required: true
    },
    locale: {
        type: String,
        default: 'en'
    },
    showDescription: {
        type: Boolean,
        default: true
    },
    showReadMore: {
        type: Boolean,
        default: true
    },
    showComments: {
        type: Boolean,
        default: true
    },
    showCategory: {
        type: Boolean,
        default: true
    },
    dateOverride: {
        type: String,
        default: ''
    }
})

const page = usePage()
const trans = (key) => page.props.translations?.[key] || key

const postUrl = computed(() => {
    if (!props.blog || !props.blog.slug) {
        return '#'
    }

    try {
        return route('blogs.show', props.blog.slug)
    } catch (e) {
        return '#'
    }
})

const dateText = computed(() => {
    if (props.dateOverride) {
        return props.dateOverride
    }

    const blog = props.blog || {}
    if (blog.created_at_formatted) {
        return blog.created_at_formatted
    }

    const monthDay = [blog.created_at_month, blog.created_at_day].filter(Boolean).join(' ')
    if (monthDay) {
        return monthDay
    }

    return blog.created_at || ''
})

const commentsCount = computed(() => {
    if (props.blog && typeof props.blog.comments_count !== 'undefined') {
        return props.blog.comments_count || 0
    }

    return 0
})

const handleImageError = (event) => {
    // If image fails to load, use fallback
    const assetPath = page.props.asset_path || ''
    const fallbackSrc = `${assetPath}site/images/blog/blog-2-1.jpg`
    if (event.target.src !== fallbackSrc && !event.target.src.includes('blog-2-')) {
        event.target.src = fallbackSrc
    }
}
</script>
