import 'bootstrap';
import 'toastr';

import {createApp, h} from 'vue';
import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {route as ziggyRoute, ZiggyVue} from '../../vendor/tightenco/ziggy';

// Claim mobile-nav toggle before theme script.js binds (prevents open/close flicker).
if (typeof window !== 'undefined') {
    window.__symfonixMobileNavBound = true;
}

function installZiggy(app, pageProps) {
    const ziggyProps = pageProps?.ziggy || {};
    const ziggyConfig = {
        ...ziggyProps,
        location: new URL(ziggyProps.location || window.location.href),
    };

    // Expose globals so <script setup> bare route() calls keep working.
    window.Ziggy = ziggyConfig;
    window.route = (name, params, absolute, config = ziggyConfig) =>
        ziggyRoute(name, params, absolute, config);

    app.use(ZiggyVue, ziggyConfig);

    return ziggyConfig;
}

createInertiaApp({
    resolve: (name) => {
        const modules = name.split("::");
        if (modules.length > 1) {
            return resolvePageComponent(
                `../../Modules/${modules[0]}/resources/assets/js/Pages/${modules[1]}.vue`,
                import.meta.glob('../../Modules/**/resources/assets/js/Pages/**/*.vue')
            );
        } else {
            return resolvePageComponent(
                `./Pages/${name}.vue`,
                import.meta.glob('./Pages/**/*.vue')
            );
        }
    },
    setup({el, App, props, plugin}) {
        const vueApp = createApp({render: () => h(App, props)}).use(plugin);

        installZiggy(vueApp, props.initialPage?.props);

        const vue = vueApp.mount(el);

        // Hide server-rendered GEO crawl fallback once the SPA has hydrated.
        document.getElementById('geo-crawl-fallback')?.classList.add('is-hydrated');

        return vue;
    },
});
