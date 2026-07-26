import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig(async ({ mode }) => {
    const plugins = [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        vue(),
    ]

    if (mode === 'development') {
        const { default: vueDevTools } = await import('vite-plugin-vue-devtools')
        plugins.push(vueDevTools())
    }

    return {
        plugins,
        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources/js'),
                'ziggy-js': path.resolve(__dirname, 'vendor/tightenco/ziggy'),
            },
        },
        build: {
            cssCodeSplit: true,
            sourcemap: false,
            rollupOptions: {
                output: {
                    manualChunks(id) {
                        if (id.includes('node_modules')) {
                            if (id.includes('@inertiajs') || id.includes('/vue')) {
                                return 'vendor-vue'
                            }
                            if (id.includes('bootstrap') || id.includes('toastr')) {
                                return 'vendor-ui'
                            }
                        }
                    },
                },
            },
        },
    }
})
