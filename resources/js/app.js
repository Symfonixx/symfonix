import 'bootstrap';
import 'toastr';

import {createApp, h} from 'vue';
import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';

// Claim mobile-nav toggle before theme script.js binds (prevents open/close flicker).
if (typeof window !== 'undefined') {
    window.__symfonixMobileNavBound = true;
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
        const vue = createApp({render: () => h(App, props)})
            .use(plugin).mixin({methods: {route}})
            .mount(el);

        // Hide server-rendered GEO crawl fallback once the SPA has hydrated.
        document.getElementById('geo-crawl-fallback')?.classList.add('is-hydrated');

        return vue;
    },
});
