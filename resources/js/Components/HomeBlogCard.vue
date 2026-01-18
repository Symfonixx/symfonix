<template>
    <div v-if="variant === 'featured'" class="blog-two__single">
        <div class="blog-two__img">
            <Link :href="postUrl">
                <img :src="imageSrc" :alt="translateField(post.title)">
            </Link>
            <div class="blog-two__tags"  >
                <span >{{ translateField(post.category.name) }}</span>
            </div>
        </div>
        <div class="blog-two__content">

            <ul class="blog-two__meta list-unstyled">
                <li v-if="post.created_at">
                    <Link :href="postUrl">
                        <span class="far fa-calendar-alt"></span>{{ post.created_at }}
                    </Link>
                </li>

            </ul>
            <h3 class="blog-two__title">
                <Link :href="postUrl">
                    {{ truncate(translateField(post.title) , 60) }}
                </Link>
            </h3>
            <p class="blog-two__text">
                {{ truncate(translateField(post.description) , 160) }}
            </p>
            <div class="blog-two__btn-box">
                <Link :href="postUrl" class="thm-btn">
                    {{ trans('Read More') }}
                    <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow `"></span>
                </Link>
            </div>
        </div>
    </div>
    <div
        v-else
        class="blog-two__single-two wow"
        :class="animationClass"
        :data-wow-delay="animationDelay"
    >
        <div class="blog-two__img-two">
            <Link :href="postUrl">
                <img :src="imageSrc" :alt="translateField(post.title)">
            </Link>
        </div>
        <div class="blog-two__content-two">

            <div class="blog-two__tags-two"  >
                <span >{{ translateField(post.category.name) }}</span>
            </div>
            <h3 class="blog-two__title-two">
                <Link :href="postUrl">
                    {{ truncate(translateField(post.title) , 60) }}
                </Link>
            </h3>
            <ul class="blog-two__meta-two list-unstyled">
                <li v-if="post.created_at">
                    <Link :href="postUrl">
                        <span class="far fa-calendar-alt"></span>{{ post.created_at }}
                    </Link>
                </li>

            </ul>
            <div class="blog-two__btn-box-two">
                <Link :href="postUrl" class="thm-btn">
                    {{ trans('Read More') }}
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
    post: {
        type: Object,
        required: true
    },
    variant: {
        type: String,
        default: 'compact'
    },
    locale: {
        type: String,
        default: 'en'
    },
    assetPath: {
        type: String,
        default: ''
    },
    imageFallbackIndex: {
        type: Number,
        default: 1
    },
    avatarSeed: {
        type: Number,
        default: 0
    },
    animationClass: {
        type: String,
        default: ''
    },
    animationDelay: {
        type: String,
        default: ''
    }
})

const page = usePage()
const trans = (key) => page.props.translations?.[key] || key

const postUrl = computed(() => {
    if (!props.post || !props.post.slug) {
        return '#'
    }

    try {
        return route('blogs.show', props.post.slug)
    } catch (e) {
        return '#'
    }
})

const imageSrc = computed(() => {
    const link = props.post?.image_link || ''
    if (!link || link.includes('/images/blank.png')) {
        return `${props.assetPath}site/images/blog/blog-2-${props.imageFallbackIndex}.jpg`
    }

    return link
})

const translateField = (value) => {
    if (!value) {
        return ''
    }

    if (typeof value === 'string') {
        return value
    }

    const loc = props.locale
    if (typeof value === 'object' && value !== null) {
        if (value[loc]) {
            return value[loc]
        }
    }

    return ''
}


const userAvatar = computed(() => {
    const n = ((Number(props.avatarSeed) || 0) % 4) + 1
    return `${props.assetPath}site/images/blog/blog-two-user-${n}.jpg`
})



const truncate = (text, length) => {
    if (!text) return ''
    return text.length > length ? text.substring(0, length) + '...' : text
}
</script>
