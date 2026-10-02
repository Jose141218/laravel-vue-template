/// <reference types="vitest/config" />
import inertia from '@inertiajs/vite';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig } from 'vite';
import vueDevTools from 'vite-plugin-vue-devtools';

if (process.env.VITEST) {
    process.env.LARAVEL_BYPASS_ENV_CHECK = '1';
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600, 700],
                }),
            ],
        }),
        inertia({ ssr: false }),
        tailwindcss(),
        vueDevTools({
            appendTo: 'resources/js/app.ts',
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    define: {
        __VUE_PROD_DEVTOOLS__: true,
    },
    test: {
        environment: 'happy-dom',
        globals: true,
        include: ['resources/js/**/*.{test,spec}.{js,ts}'],
    },
});
