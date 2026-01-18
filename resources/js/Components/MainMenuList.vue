<template>
    <ul class="main-menu__list">
        <li :class="{ current: isActive('home') }">
            <Link :href="route('home')">
                {{ trans('Home') }}
            </Link>
        </li>
        <li :class="{ current: isActive('about-us') }">
            <Link :href="route('about-us')">
                {{ trans('About Us') }}
            </Link>
        </li>
        <li :class="{ current: isActive(['services.index', 'services.show']) }">
            <Link :href="route('services.index')">
                {{ trans('Our Services') }}
            </Link>
        </li>
        <li :class="{ current: isActive(['blogs.index', 'blogs.show']) }">
            <Link :href="route('blogs.index')">
                {{ trans('Blogs') }}
            </Link>
        </li>
        <li class="dropdown" :class="headerPages.length === 0 ? 'd-none' : ''">
            <Link :href="headerPages.length ? route('page.view', headerPages[0].slug) : '#'">
                {{ trans('Pages') }}
            </Link>
            <ul class="shadow-box">
                <li v-for="page in headerPages" :key="page.id">
                    <Link :href="route('page.view', page.slug)">
                        {{ page.title[locale] }}
                    </Link>
                </li>
            </ul>
        </li>

        <li :class="{ current: isActive('contact-us') }">
            <Link :href="route('contact-us')">
                {{ trans('Contact Us') }}
            </Link>
        </li>

        <li v-if="auth?.type === 'admin'" :class="{ active: isActive('admin.dashboard.index') }">
            <Link :href="route('admin.dashboard.index')">
                {{ trans('Dashboard') }}
            </Link>
        </li>

        <li>
            <a href="#">
                <img
                    :src="asset_path + `images/langs/${locale}.png`"
                    width="20"
                    alt="locale"
                >
            </a>
            <ul class="sub-menu">
                <li>
                    <a
                        href="#"
                        @click.prevent="switchLocale('ar')"
                        :class="{ active: locale === 'ar' }"
                    >
                        <img
                            class="me-1"
                            :src="asset_path + 'images/langs/ar.png'"
                            width="20"
                            alt="locale"
                        >
                        {{ trans('Arabic') }}
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        @click.prevent="switchLocale('en')"
                        :class="{ active: locale === 'en' }"
                    >
                        <img
                            class="me-1"
                            :src="asset_path + 'images/langs/en.png'"
                            width="20"
                            alt="locale"
                        >
                        {{ trans('English') }}
                    </a>
                </li>
                <li>
                    <a
                        href="#"
                        @click.prevent="switchLocale('tr')"
                        :class="{ active: locale === 'tr' }"
                    >
                        <img
                            class="me-1"
                            :src="asset_path + 'images/langs/tr.png'"
                            width="20"
                            alt="locale"
                        >
                        {{ trans('Turkish') }}
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</template>

<script setup>
import {computed} from 'vue'
import {Link, usePage} from '@inertiajs/vue3'

const page = usePage()
const trans = (key) => page.props.translations[key] || key
const locale = computed(() => page.props.locale)
const headerPages = computed(() => page.props.headerPages || [])
const auth = computed(() => page.props.auth)
const asset_path = computed(() => page.props.asset_path || '')

const isActive = (routeName) => {
    const routeNames = Array.isArray(routeName) ? routeName : [routeName]
    try {
        return routeNames.some((name) => route().current(name))
    } catch (e) {
        return false
    }
}

const switchLocale = (newLocale) => {
    const currentPath = window.location.pathname
    const oldLocaleSegment = `/${locale.value}`
    const newLocaleSegment = `/${newLocale}`
    window.location.href = currentPath.replace(oldLocaleSegment, newLocaleSegment)
}
</script>
