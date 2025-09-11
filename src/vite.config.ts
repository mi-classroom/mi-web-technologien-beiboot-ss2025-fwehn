import vue from '@vitejs/plugin-vue';
import autoprefixer from 'autoprefixer';
import laravel from 'laravel-vite-plugin';
import i18n from 'laravel-vue-i18n/vite';
import { resolve } from 'node:path';
import path from 'path';
import tailwindcss from 'tailwindcss';
import { defineConfig, loadEnv } from 'vite';

export default ({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return defineConfig({
        server: {
            // host: '0.0.0.0',
            allowedHosts: true,
            cors: {
                origin: /.*/,
            },
            hmr: {
                host: env.VITE_HMR_HOST || 'localhost',
            },
        },
        plugins: [
            laravel({
                input: ['resources/js/app.ts'],
                ssr: 'resources/js/ssr.ts',
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            i18n(),
        ],
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './resources/js'),
                'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
            },
        },
        css: {
            postcss: {
                plugins: [tailwindcss, autoprefixer],
            },
        },
    });
};
