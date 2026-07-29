<template>
    <SiteErrorPage
        :status="config.status"
        :title="trans(config.title)"
        :heading="trans(config.heading)"
        :message="trans(config.message)"
        :description="trans(config.description)"
        :keywords="trans(config.keywords)"
        :show-image="config.showImage"
        :show-debug="config.showDebug"
        :secondary-href="secondaryHref"
        :secondary-label="secondaryLabel"
    />
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import SiteErrorPage from '@/Components/SiteErrorPage.vue'

const page = usePage()
const trans = (key) => {
    try {
        return page.props.translations?.[key] || key
    } catch (e) {
        return key
    }
}

const status = computed(() => Number(page.props.status || 500))
const appEnv = computed(() => page.props.app_env || 'production')
const locale = computed(() => page.props.locale || 'en')

const catalog = {
    400: {
        status: 400,
        title: '400 Bad Request',
        heading: 'Bad Request',
        message: 'The request could not be understood or was invalid. Please check and try again.',
        description: 'The request could not be processed due to invalid input.',
        keywords: '400 error, bad request, invalid request',
        showImage: false,
        showDebug: false,
        secondary: null,
    },
    401: {
        status: 401,
        title: '401 Unauthorized',
        heading: 'Authentication Required',
        message: 'You need to sign in to access this page.',
        description: 'Authentication is required to access this resource.',
        keywords: '401 error, unauthorized, login required',
        showImage: false,
        showDebug: false,
        secondary: 'login',
    },
    403: {
        status: 403,
        title: '403 Forbidden',
        heading: 'Access Denied',
        message: 'You do not have permission to access this page.',
        description: 'You are not allowed to access this resource.',
        keywords: '403 error, forbidden, access denied',
        showImage: false,
        showDebug: false,
        secondary: null,
    },
    404: {
        status: 404,
        title: '404 Error',
        heading: 'Oops! Page Not Found!',
        message: 'The page you are looking for does not exist. It might have been moved or deleted.',
        description: 'The page you are looking for could not be found.',
        keywords: '404 error, page not found, missing page',
        showImage: true,
        showDebug: false,
        secondary: null,
    },
    500: {
        status: 500,
        title: '500 Error',
        heading: 'Internal Server Error',
        message: "We're sorry, but something went wrong on our end. Please try again later or contact support if the problem persists.",
        description: 'An internal server error occurred. Please try again later.',
        keywords: '500 error, server error, internal error',
        showImage: false,
        showDebug: true,
        secondary: null,
    },
    503: {
        status: 503,
        title: '503 Service Unavailable',
        heading: 'Service Unavailable',
        message: 'The service is temporarily unavailable. Please try again in a few moments.',
        description: 'The service is temporarily unavailable. Please try again later.',
        keywords: '503 error, service unavailable, maintenance',
        showImage: false,
        showDebug: false,
        secondary: null,
    },
}

const config = computed(() => {
    const entry = catalog[status.value] || catalog[500]
    return {
        ...entry,
        showDebug: entry.showDebug && appEnv.value !== 'production',
    }
})

const secondaryHref = computed(() => {
    if (config.value.secondary !== 'login') {
        return ''
    }
    try {
        return route('login')
    } catch (e) {
        return `/${locale.value}/login`
    }
})

const secondaryLabel = computed(() => {
    return config.value.secondary === 'login' ? trans('Login') : ''
})
</script>
