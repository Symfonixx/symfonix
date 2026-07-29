<template>
    <nav class="main-menu main-menu-two">
        <div class="main-menu-two__wrapper">
            <div class="main-menu-two__wrapper-inner">
                <div class="main-menu-two__left">
                    <div class="main-menu-two__logo">
                        <Link :href="route('home')" class="main-menu-two__logo-link">
                            <img
                                v-if="logoSrc"
                                :src="logoSrc"
                                :alt="brandName"
                                width="180"
                                height="48"
                            >
                            <span v-else class="brand-text-logo">{{ brandName }}</span>
                        </Link>
                    </div>
                </div>
                <div class="main-menu-two__main-menu-box">
                    <a href="#" class="mobile-nav__toggler" aria-label="Open mobile menu"><i class="fa fa-bars"></i></a>
                    <MainMenuList />
                </div>
                <div class="main-menu-two__right d-none d-md-flex align-items-center">
                    <div v-if="!auth" class="main-menu-two__btn-box">
                        <Link :href="loginUrl" class="thm-btn">
                            {{ trans('Login') }}
                        </Link>
                    </div>

                    <div class="main-menu-two__search-box me-3">
                        <a
                            href="#"
                            class="main-menu-two__search searcher-toggler-box icon-search-interface-symbol"
                            aria-label="Open search"
                        ></a>
                    </div>

                    <div class="main-menu-two__nav-sidebar-icon">
                        <a class="navSidebar-button" href="#" aria-label="Open sidebar">
                            <span class="icon-dots-menu-one"></span>
                            <span class="icon-dots-menu-two"></span>
                            <span class="icon-dots-menu-three"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import {computed} from 'vue'
import {Link, usePage} from '@inertiajs/vue3'
import MainMenuList from '@/Components/MainMenuList.vue'

const page = usePage()
const trans = (key) => page.props.translations[key] || key
const settings = computed(() => page.props.settings || {})
const storage_path = computed(() => page.props.storage_path || '')
const auth = computed(() => page.props.auth)
const locale = computed(() => page.props.locale || 'en')
const brandName = computed(() => page.props.seo?.website_name || page.props.appName || 'Symfonix')
const loginUrl = computed(() => {
    try {
        return route('login')
    } catch (e) {
        return `/${locale.value}/login`
    }
})
const logoSrc = computed(() => {
    const logo = settings.value?.site_logo
    if (!logo || logo === false || logo === 'false' || logo === 'default.jpg') {
        return ''
    }
    if (/^https?:\/\//i.test(logo) || String(logo).startsWith('//') || String(logo).startsWith('/')) {
        return logo
    }
    return `${storage_path.value}${logo}`
})
</script>

<style scoped>
.main-menu-two__logo-link {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
}

.brand-text-logo {
    display: inline-block;
    font-size: 1.35rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    color: #fff;
    line-height: 1.2;
    white-space: nowrap;
}
</style>
