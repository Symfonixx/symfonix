import { createSSRApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import { renderToString } from '@vue/server-renderer'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

// NOTE:
// This file mirrors the client-side setup in `resources/js/app.js`,
// but is tailored for server-side rendering.

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        resolve: (name) => {
            const modules = name.split('::')

            // Support for module-style pages, e.g. "Services::ServiceIndex"
            if (modules.length > 1) {
                return resolvePageComponent(
                    `../../Modules/${modules[0]}/resources/assets/js/Pages/${modules[1]}.vue`,
                    import.meta.glob('../../Modules/**/resources/assets/js/Pages/**/*.vue', {
                        eager: true,
                    }),
                )
            }

            // Default app pages
            return resolvePageComponent(
                `./Pages/${name}.vue`,
                import.meta.glob('./Pages/**/*.vue', { eager: true }),
            )
        },
        setup({ App, props, plugin }) {
            // On the server we use createSSRApp instead of createApp
            return createSSRApp({
                render: () => h(App, props),
            }).use(plugin)
        },
    }),
)












