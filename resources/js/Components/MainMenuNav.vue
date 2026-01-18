<template>
    <nav class="main-menu main-menu-two">
        <div class="main-menu-two__wrapper">
            <div class="main-menu-two__wrapper-inner">
                <div class="main-menu-two__left">
                    <div class="main-menu-two__logo">
                        <Link :href="route('home')">
                            <img :src="storage_path + settings.site_logo" alt="logo">
                        </Link>
                    </div>
                </div>
                <div class="main-menu-two__main-menu-box">
                    <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                    <MainMenuList />
                </div>
                <div class="main-menu-two__right d-none d-md-flex align-items-center">
                    <div class="main-menu-two__search-box me-3">
                        <a
                            href="#"
                            class="main-menu-two__search searcher-toggler-box icon-search-interface-symbol"
                        ></a>
                    </div>

                    <div class="main-menu-two__nav-sidebar-icon">
                        <a class="navSidebar-button" href="#">
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
const settings = computed(() => page.props.settings)
const storage_path = computed(() => page.props.storage_path)
</script>
