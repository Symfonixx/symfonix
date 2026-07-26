import { createSSRApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import { renderToString } from '@vue/server-renderer'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        resolve: (name) => {
            const modules = name.split('::')

            if (modules.length > 1) {
                return resolvePageComponent(
                    `../../Modules/${modules[0]}/resources/assets/js/Pages/${modules[1]}.vue`,
                    import.meta.glob('../../Modules/**/resources/assets/js/Pages/**/*.vue', {
                        eager: true,
                    }),
                )
            }

            return resolvePageComponent(
                `./Pages/${name}.vue`,
                import.meta.glob('./Pages/**/*.vue', { eager: true }),
            )
        },
        setup({ App, props, plugin }) {
            const ziggyProps = props.initialPage?.props?.ziggy || page.props?.ziggy || {}
            const ziggyConfig = {
                ...ziggyProps,
                location: new URL(ziggyProps.location || 'http://localhost'),
            }

            return createSSRApp({
                render: () => h(App, props),
            })
                .use(plugin)
                .use(ZiggyVue, ziggyConfig)
        },
    }),
)
