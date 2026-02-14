<template>
    <Head>
        <title>{{blog.title}}</title>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'"/>
    </Head>
    <app-layout>
        <section class="page-header">
            <div class="page-header__bg" :style="{ backgroundImage: `url(${asset_path}images/contact-header-bg.jpg)`}">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans("Blog Details") }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li>
                                <Link :href="route('home')"><i class="fas fa-home"></i>{{ trans("Home") }}</Link>
                            </li>
                            <li><span  :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li>{{ trans("Blog Details") }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!--Blog Details Start-->
        <section class="blog-details">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 col-lg-7">
                        <div class="blog-details__left">
                            <div class="blog-details__img">
                                <img :src="blog.image_link" :alt="blog.title">
                            </div>
                            <div class="blog-details__single-content">

                                <ul class="blog-details__meta list-unstyled">
                                    <li>
                                        <Link :href="route('blogs.show', blog.slug)"><span
                                            class="far fa-calendar-alt"></span>{{ blog.created_at_formatted }}
                                        </Link>
                                    </li>

                                    <li  >
                                        <Link >
                                            <span class="far fa-clock"></span>{{ blog.reading_time }}
                                            {{ trans('min read') }}
                                        </Link>
                                    </li>

                                </ul>
                                <h3 class="blog-details__title">
                                    <Link :href="route('blogs.show', blog.slug)">{{ blog.title }}</Link>
                                </h3>
                                <div class="blog-details__text" v-html="blog.content"></div>
                            </div>

                            <div v-if="blog.keywords" class="blog-details__tag-and-share">
                                <div class="blog-details__tag">
                                    <h3 class="blog-details__tag-title">{{ trans("Keywords") }}:</h3>
                                    <ul class="blog-details__tag-list list-unstyled">
                                        <li v-for="(keyword, index) in getKeywords(blog.keywords)" :key="index">
                                            <Link :href="route('blogs.index', { search: keyword.trim() })">
                                                {{ keyword.trim() }}
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                                <div class="blog-details__share-box">
                                    <h3 class="blog-details__share-title">{{ trans("Share On:") }}</h3>
                                    <div class="blog-details__share">
                                        <a :href="getShareUrl('facebook')" target="_blank"><span
                                            class="icon-facebook"></span></a>
                                        <a :href="getShareUrl('twitter')" target="_blank"><span
                                            class="fab fa-twitter"></span></a>
                                        <a :href="getShareUrl('linkedin')" target="_blank"><span
                                            class="icon-linkedin"></span></a>
                                    </div>
                                </div>
                            </div>

                            <div v-if="previousPost || nextPost" class="blog-details__prev-and-next">
                                <div v-if="previousPost" class="blog-details__prev-box">
                                    <div class="blog-details__prev-img">
                                        <img :src="previousPost.image_link" :alt="previousPost.title">
                                    </div>
                                    <div class="blog-details__prev-content">
                                        <div class="blog-details__prev-arrow">
                                            <span class="icon-left-arrow"></span>
                                            <Link :href="route('blogs.show', previousPost.slug)">{{
                                                    trans("Prev Blog")
                                                }}
                                            </Link>
                                        </div>
                                        <h4 class="blog-details__prev-title">{{ previousPost.title }}</h4>
                                    </div>
                                </div>
                                <div v-if="nextPost" class="blog-details__next-box">
                                    <div class="blog-details__next-content">
                                        <div class="blog-details__next-arrow">
                                            <Link :href="route('blogs.show', nextPost.slug)">{{
                                                    trans("Next Blog")
                                                }}
                                            </Link>
                                            <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow `"></span>
                                        </div>
                                        <h4 class="blog-details__next-title">{{ nextPost.title }}</h4>
                                    </div>
                                    <div class="blog-details__next-img">
                                        <img :src="nextPost.image_link" :alt="nextPost.title">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-5">
                        <div class="sidebar">
                            <!--Start Sidebar Single-->
                            <div class="sidebar__single sidebar__search">
                                <div class="sidebar__title-box">
                                    <div class="sidebar__title-shape"></div>
                                    <h3 class="sidebar__title">{{ trans("Search") }} </h3>
                                </div>
                                <p class="sidebar__search-text">{{
                                        trans("Search blogs to discover a vast world of online content on countless topics.")
                                    }}</p>
                                <form @submit.prevent="handleSearch" class="sidebar__search-form">
                                    <input type="search" v-model="searchQuery" :placeholder="trans('Search Blogs')">
                                    <button type="submit"><i class="fa fa-search"></i></button>
                                </form>
                            </div>
                            <!--End Sidebar Single-->
                            <!--Start Sidebar Single-->
                            <div class="sidebar__single sidebar__category">
                                <div class="sidebar__title-box">
                                    <div class="sidebar__title-shape"></div>
                                    <h3 class="sidebar__title">{{ trans("Category") }} </h3>
                                </div>
                                <ul class="sidebar__category-list list-unstyled">
                                    <li v-for="category in categories" :key="category.id">
                                        <Link :href="route('blogs.index', { category: category.slug })">{{
                                                category.name
                                            }} <span>({{ category.blogs_count }})</span></Link>
                                    </li>
                                </ul>
                            </div>
                            <!--End Sidebar Single-->
                            <!--Start Sidebar Single-->
                            <div class="sidebar__single sidebar__post">
                                <div class="sidebar__title-box">
                                    <div class="sidebar__title-shape"></div>
                                    <h3 class="sidebar__title">{{ trans("Recent Post") }} </h3>
                                </div>
                                <ul class="sidebar__post-list list-unstyled">
                                    <li v-for="recentPost in recentPosts" :key="recentPost.id">
                                        <div class="sidebar__post-image">
                                            <img :src="recentPost.image_link" :alt="recentPost.title">
                                        </div>
                                        <div class="sidebar__post-content">
                                            <p class="sidebar__post-date"><span
                                                class="icon-calendar"></span>{{ recentPost.created_at }}</p>
                                            <h3 class="sidebar__post-title">
                                                <Link :href="route('blogs.show', recentPost.slug)">{{
                                                        recentPost.title
                                                    }}
                                                </Link>
                                            </h3>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <!--End Sidebar Single-->
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--Blog Details End-->
        <section class="mt-5">
            <div v-if="relatedBlogs && relatedBlogs.length > 0" class="container">
                <div class="related-blogs mt-20 mt-xs-10">
                    <h4 class="mb-5">{{ trans("Related Blogs") }}:</h4>
                    <div class="row">
                        <div v-for="relatedBlog in relatedBlogs" :key="relatedBlog.id" class="col-md-4 mb-30">
                            <HomeBlogCard
                                :post="relatedBlog"
                                variant="featured"
                                :locale="locale"
                                :asset-path="asset_path"
                                :image-fallback-index="1"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <CtaTwo />

    </app-layout>
</template>

<script setup>
import {computed, ref} from 'vue'
import {usePage, Link, router, Head} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/App.vue'
import HomeBlogCard from '@/Components/HomeBlogCard.vue'
import CtaTwo from '@/Components/CtaTwo.vue'

const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale || 'en')
const blog = computed(() => page.props.blog)
const relatedBlogs = computed(() => page.props.relatedBlogs || [])
const categories = computed(() => page.props.categories || [])
const recentPosts = computed(() => page.props.recentPosts || [])
const previousPost = computed(() => page.props.previousPost)
const nextPost = computed(() => page.props.nextPost)

const searchQuery = ref('')

const getKeywords = (keywords) => {
    if (!keywords) return []
    if (typeof keywords === 'string') {
        return keywords.split(',').slice(0, 3)
    }
    return []
}

const getShareUrl = (platform) => {
    const url = encodeURIComponent(window.location.href)
    const title = encodeURIComponent(blog.value.title)

    switch (platform) {
        case 'twitter':
            return `https://twitter.com/intent/tweet?url=${url}&text=${title}`
        case 'facebook':
            return `https://www.facebook.com/sharer/sharer.php?u=${url}`
        case 'linkedin':
            return `https://www.linkedin.com/shareArticle?mini=true&url=${url}&title=${title}`
        case 'pinterest':
            return `https://pinterest.com/pin/create/button/?url=${url}&description=${title}`
        default:
            return '#'
    }
}

const handleSearch = () => {
    if (searchQuery.value) {
        router.get(route('blogs.index'), {search: searchQuery.value})
    }
}
</script>

<script>
export default {
    components: {
        AppLayout, HomeBlogCard, CtaTwo
    }
};
</script>








