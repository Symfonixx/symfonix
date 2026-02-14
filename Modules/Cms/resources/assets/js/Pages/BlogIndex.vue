<template>
    <Head>
        <link rel="stylesheet" :href="asset_path + 'site/css/module-css/page-header.css'"/>
        <title>{{ metaTitle }}</title>
        <meta name="description" :content="metaDescription">
        <meta name="keywords" :content="metaKeywords">
        <meta name="robots" :content="metaRobots">
        <link v-if="metaCanonical" rel="canonical" :href="metaCanonical">
        <meta property="og:title" :content="metaTitle">
        <meta property="og:description" :content="metaDescription">
        <meta v-if="metaImage" property="og:image" :content="metaImage">
        <meta v-if="metaCanonical" property="og:url" :content="metaCanonical">
        <meta property="og:type" content="website">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" :content="metaTitle">
        <meta name="twitter:description" :content="metaDescription">
        <meta v-if="metaImage" name="twitter:image" :content="metaImage">
    </Head>
    <app-layout>
        <div class="page-header">
            <div class="page-header__bg"
                 :style="{ backgroundImage: `url(${asset_path}images/backgrounds/blogs-bg.jpg)`}">
            </div>
            <div class="container">
                <div class="page-header__inner">
                    <h2>{{ trans("Our Blogs") }}</h2>
                    <div class="thm-breadcrumb__box">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li>
                                <Link :href="route('home')"><i class="fas fa-home"></i>{{ trans("Home") }}</Link>
                            </li>
                            <li><span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></li>
                            <li>{{ trans("Blogs") }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!--Blog Page Start-->
        <section class="blog-page mt-25 pb-90">
            <div class="container">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <div class="section-title__tagline-shape-1"></div>
                        <span class="section-title__tagline">{{ trans("News & Blog") }}</span>
                        <div class="section-title__tagline-shape-2"></div>
                    </div>
                    <h2 class="section-title__title title-animation"
                        v-html="trans('How We\'ve <span>Empowered Businesses</span><br><span> with Innovative</span>Tech Solutions')"></h2>
                </div>
                <div class="row">
                    <template v-if="blogs.data && blogs.data.length > 0">
                        <!-- Blog One Single Start -->
                        <div v-for="blog in blogs.data" :key="blog.id" class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp"
                             data-wow-delay="100ms">


                            <HomeBlogCard
                                :post="blog"
                                variant="featured"
                                :locale="locale"
                                :asset-path="asset_path"
                                :image-fallback-index="1"
                            />
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <h3 class="text-muted"> {{ trans("No blogs found") }} <i
                                    class="fa fa-xmark text-danger"></i></h3>
                            </div>
                        </div>
                    </template>

                    <div v-if="blogs.last_page > 1" class="blog-page__pagination">
                        <ul class="pg-pagination list-unstyled">
                            <li v-if="blogs.prev_page_url" class="prev">
                                <Link :href="blogs.prev_page_url" aria-label="prev"><span
                                    class="icon-left-arrow-1"></span></Link>
                            </li>
                            <template v-for="(link, index) in blogs.links" :key="index">
                                <li v-if="link.url && index > 0 && index < blogs.links.length - 1"
                                    :class="['count', link.active ? 'active' : '']">
                                    <Link :href="link.url">{{ link.label }}</Link>
                                </li>
                            </template>
                            <li v-if="blogs.next_page_url" class="next">
                                <Link :href="blogs.next_page_url" aria-label="Next"><span
                                    :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow-1`"></span></Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!--Blog Page End-->

        <CtaTwo />

    </app-layout>
</template>

<script setup>
import {computed} from 'vue'
import {usePage, Link, Head} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/App.vue'
import HomeBlogCard from '@/Components/HomeBlogCard.vue'
import CtaTwo from '@/Components/CtaTwo.vue'

const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const seo = computed(() => page.props.seo)
const settings = computed(() => page.props.settings || {})
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale || 'en')
const blogs = computed(() => page.props.blogs)
const meta = computed(() => page.props.meta || {})

const metaTitle = computed(() => {
    return   `${trans("Blogs")} | ${seo.value.website_name || ''}`.trim()
})
const metaDescription = computed(() => {
    return meta.value.description
        || trans('Explore our latest blogs, insights, and technology updates.')
        || seo.value.website_desc
        || ''
})
const metaKeywords = computed(() => {
    return meta.value.keywords
        || trans('blogs, news, insights, technology trends')
        || seo.value.website_keywords
        || ''
})
const metaImage = computed(() => {
    return meta.value?.og?.image || meta.value?.twitter?.image || settings.value?.meta_img || ''
})
const metaCanonical = computed(() => meta.value.canonical || '')
const metaRobots = computed(() => meta.value.robots || 'index, follow')
</script>

<script>
export default {
    components: {
        AppLayout, HomeBlogCard, CtaTwo
    }
};
</script>


