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
        <li :class="{ current: isActive(['services.index', 'services.show'], { prefixes: ['/services', '/service'] }) }">
            <Link :href="route('services.index')">
                {{ trans('Our Services') }}
            </Link>
        </li>
        <li :class="{ current: isActive(['blogs.index', 'blogs.show'], { prefixes: ['/blogs', '/blog'] }) }">
            <Link :href="route('blogs.index')">
                {{ trans('Blogs') }}
            </Link>
        </li>
        <li
            class="dropdown"
            :class="[
                headerPages.length === 0 ? 'd-none' : '',
                { current: isActive('page.view', { prefixes: ['/p'] }) }
            ]"
        >
            <Link :href="headerPages.length ? route('page.view', headerPages[0].slug) : '#'">
                {{ trans('Pages') }}
            </Link>
            <ul class="shadow-box">
                <li v-for="page in headerPages" :key="page.id" :class="{ current: isPageActive(page) }">
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
            <a :href="route('admin.dashboard.index')">
                {{ trans('Dashboard') }}
            </a>
        </li>

        <li class="dropdown">
            <a href="#" aria-label="Change language">
                <img
                    :src="asset_path + `images/langs/${locale}.png`"
                    width="20"
                    :alt="trans('Current language')"
                >
            </a>
            <ul class="shadow-box">
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
                            :alt="trans('Arabic')"
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
                            :alt="trans('English')"
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
                            :alt="trans('Turkish')"
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

const normalizePath = (path) => {
    if (!path) return ''
    const withoutQuery = path.split('?')[0]
    if (withoutQuery === '/') return '/'
    return withoutQuery.replace(/\/+$/, '')
}

const getPathFromUrl = (url) => {
    if (!url) return ''
    try {
        return new URL(url, window.location.origin).pathname
    } catch (e) {
        return url
    }
}

const expandPrefixes = (prefixes = []) => {
    const localePrefix = locale.value ? `/${locale.value}` : ''
    return prefixes.flatMap((prefix) => {
        const normalized = prefix.startsWith('/') ? prefix : `/${prefix}`
        if (!localePrefix) {
            return [normalized]
        }
        return [normalized, `${localePrefix}${normalized}`]
    })
}

const isActive = (routeName, options = {}) => {
    const routeNames = Array.isArray(routeName) ? routeName : [routeName]
    const prefixes = expandPrefixes(options.prefixes || [])
    try {
        if (routeNames.some((name) => route().current(name))) {
            return true
        }
    } catch (e) {
        // Fall back to path matching when Ziggy isn't available
    }
    if (!prefixes.length) {
        return false
    }
    const currentPath = normalizePath(page.url)
    return prefixes.some((prefix) => {
        const normalized = normalizePath(prefix)
        return currentPath === normalized || currentPath.startsWith(`${normalized}/`)
    })
}

const isCurrentUrl = (targetUrl) => {
    const targetPath = normalizePath(getPathFromUrl(targetUrl))
    const currentPath = normalizePath(page.url)
    return currentPath === targetPath
}

const isPageActive = (pageItem) => {
    if (!pageItem || !pageItem.slug) return false
    try {
        return isCurrentUrl(route('page.view', pageItem.slug))
    } catch (e) {
        return false
    }
}

const switchLocale = (newLocale) => {
    const currentPath = window.location.pathname
    const currentLocale = locale.value
    
    // Remove current locale from path if it exists
    let pathWithoutLocale = currentPath
    if (currentLocale && currentPath.startsWith(`/${currentLocale}`)) {
        pathWithoutLocale = currentPath.substring(`/${currentLocale}`.length) || '/'
    }
    
    // Ensure path starts with /
    if (!pathWithoutLocale.startsWith('/')) {
        pathWithoutLocale = '/' + pathWithoutLocale
    }
    
    // Build new URL with new locale
    const newPath = `/${newLocale}${pathWithoutLocale === '/' ? '' : pathWithoutLocale}`
    
    // Preserve query string and hash if present
    const queryString = window.location.search
    const hash = window.location.hash
    
    window.location.href = newPath + queryString + hash
}
</script>
