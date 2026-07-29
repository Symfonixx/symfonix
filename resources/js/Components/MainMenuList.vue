<template>
    <ul class="main-menu__list">
        <li :class="{ current: isActive('home', { exact: ['/'] }) }">
            <Link :href="route('home')">
                {{ trans('Home') }}
            </Link>
        </li>
        <li :class="{ current: isActive('about-us', { prefixes: ['/about-us'] }) }">
            <Link :href="route('about-us')">
                {{ trans('About Us') }}
            </Link>
        </li>
        <li :class="{ current: isActive(['services.index', 'services.show'], { prefixes: ['/services', '/service'] }) }">
            <Link :href="route('services.index')">
                {{ trans('Our Services') }}
            </Link>
        </li>
        <li :class="{ current: isActive(['use-cases.index', 'use-cases.show'], { prefixes: ['/use-cases', '/portfolio'] }) }">
            <Link :href="route('use-cases.index')">
                {{ trans('Case Studies') }}
            </Link>
        </li>
        <li :class="{ current: isActive(['product.index', 'product.show'], { prefixes: ['/products', '/product'] }) }">
            <Link :href="route('product.index')">
                {{ trans('Products') }}
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

        <li :class="{ current: isActive('contact-us', { prefixes: ['/contact-us'] }) }">
            <Link :href="route('contact-us')">
                {{ trans('Contact Us') }}
            </Link>
        </li>

        <li
            v-if="!auth"
            class="d-md-none"
            :class="{ current: isActive('login', { prefixes: ['/login'] }) }"
        >
            <Link :href="loginUrl">
                <i class="fas fa-sign-in-alt mx-1"></i>
                {{ trans('Login') }}
            </Link>
        </li>

        <li
            v-if="auth?.type === 'admin'"
            :class="{ active: isActive('admin.dashboard.index', { prefixes: ['/admin'] }) }"
        >
            <a :href="adminDashboardUrl">
                {{ trans('Dashboard') }}
            </a>
        </li>

        <li
            v-if="auth?.type === 'customer'"
            class="dropdown portal-account-dropdown"
        >
            <a href="#" aria-label="Customer account" @click.prevent>
                <i class="fas fa-user-circle mx-1"></i>{{ auth.name }}
            </a>
            <ul class="shadow-box portal-account-menu">
                <li
                    class="portal-menu-item"
                    :class="{ current: isActive('portal.dashboard') }"
                >
                    <Link :href="route('portal.dashboard')">
                        <i class="fas fa-th-large mx-1"></i>
                        {{ portalLabel('menu.my_dashboard') }}
                    </Link>
                </li>
                <li
                    class="portal-menu-item"
                    :class="{ current: isActive(['portal.projects.index', 'portal.projects.show'], { prefixes: ['/portal/projects'] }) }"
                >
                    <Link :href="route('portal.projects.index')">
                        <i class="fas fa-folder-open mx-1"></i>
                        {{ portalLabel('menu.projects') }}
                        <span v-if="unreadCount" class="portal-menu-badge">{{ unreadCount }}</span>
                    </Link>
                </li>
                <li
                    class="portal-menu-item"
                    :class="{ current: isActive('portal.subscriptions.index', { prefixes: ['/portal/subscriptions'] }) }"
                >
                    <Link :href="route('portal.subscriptions.index')">
                        <i class="fas fa-sync-alt mx-1"></i>
                        {{ portalLabel('menu.subscriptions') }}
                    </Link>
                </li>
                <li
                    class="portal-menu-item"
                    :class="{ current: isActive(['portal.tickets.index', 'portal.tickets.create', 'portal.tickets.show'], { prefixes: ['/portal/tickets'] }) }"
                >
                    <Link :href="route('portal.tickets.index')">
                        <i class="fas fa-life-ring mx-1"></i>
                        {{ portalLabel('menu.tickets') }}
                    </Link>
                </li>
                <li
                    class="portal-menu-item"
                    :class="{ current: isActive('portal.profile.index', { prefixes: ['/portal/profile'] }) }"
                >
                    <Link :href="route('portal.profile.index')">
                        <i class="fas fa-user-cog mx-1"></i>
                        {{ portalLabel('menu.profile') }}
                    </Link>
                </li>
                <li class="portal-menu-item portal-menu-item--logout text-danger">
                    <Link :href="route('logout')" method="post" as="a">
                        <i class="fas fa-sign-out-alt mx-1"></i>
                        {{ portalLabel('menu.logout') }}
                    </Link>
                </li>
            </ul>
        </li>

        <li class="dropdown">
            <a href="#" aria-label="Change language">
                <img
                    :src="asset_path + `images/langs/${locale}.svg`"
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
                            class="mx-1"
                            :src="asset_path + 'images/langs/ar.svg'"
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
                            class="mx-1"
                            :src="asset_path + 'images/langs/en.svg'"
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
                            class="mx-1"
                            :src="asset_path + 'images/langs/tr.svg'"
                            width="20"
                            :alt="trans('Turkish')"
                        >
                        {{ trans('Turkish') }}
                    </a>
                </li>
                <li>
                    <a
                        href="#"
                        @click.prevent="switchLocale('de')"
                        :class="{ active: locale === 'de' }"
                    >
                        <img
                            class="mx-1"
                            :src="asset_path + 'images/langs/de.svg'"
                            width="20"
                            :alt="trans('German')"
                        >
                        {{ trans('German') }}
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
const portalTranslations = computed(() => page.props.portal?.translations || {})
const unreadCount = computed(() => page.props.portal?.unread_notifications || 0)

const portalLabel = (key) => {
    const parts = key.split('.')
    let value = portalTranslations.value

    for (const part of parts) {
        value = value?.[part]
    }

    if (typeof value === 'string') {
        return value
    }

    const fallbacks = {
        'menu.my_dashboard': 'My Dashboard',
        'menu.projects': 'My Projects',
        'menu.subscriptions': 'My Subscriptions',
        'menu.tickets': 'My Tickets',
        'menu.profile': 'My Profile',
        'menu.logout': 'Logout',
    }

    return fallbacks[key] || key
}

const localizedPath = (path = '') => {
    const normalized = path.startsWith('/') ? path : `/${path}`
    const localePrefix = locale.value ? `/${locale.value}` : ''

    if (!localePrefix) {
        return normalized === '/' ? '/' : normalized
    }

    if (normalized === '/') {
        return localePrefix
    }

    return `${localePrefix}${normalized}`
}

const safeRoute = (name, fallbackPath = '/', params = undefined) => {
    try {
        return params !== undefined ? route(name, params) : route(name)
    } catch (e) {
        return localizedPath(fallbackPath)
    }
}

const loginUrl = computed(() => safeRoute('login', '/login'))
const adminDashboardUrl = computed(() => localizedPath('/admin/dashboard'))

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
    const exactPaths = expandPrefixes(options.exact || [])
    const currentPath = normalizePath(page.url)
    const hasPathOptions = exactPaths.length > 0 || prefixes.length > 0

    // Prefer Inertia page.url — Ziggy location can stay on the first page across SPA visits.
    if (exactPaths.some((path) => currentPath === normalizePath(path))) {
        return true
    }

    if (prefixes.some((prefix) => {
        const normalized = normalizePath(prefix)
        return currentPath === normalized || currentPath.startsWith(`${normalized}/`)
    })) {
        return true
    }

    if (hasPathOptions) {
        return false
    }

    try {
        return routeNames.some((name) => route().current(name))
    } catch (e) {
        return false
    }
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

<style scoped>
.main-menu__list a:focus,
.main-menu__list a:active {
    outline: none;
}

.main-menu__list a:focus-visible {
    outline: 2px solid var(--techguru-base, #5CB0E9);
    outline-offset: 4px;
}
</style>
