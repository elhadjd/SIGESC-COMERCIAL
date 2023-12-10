import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa'

export default defineConfig({
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: `@import "./resources/css/mixins.scss";`
            },
        },
    },
    plugins: [
        VitePWA({
            registerType: 'auto',
            includeAssets: ['favicon.ico', 'robots.txt', 'icons/**'],
            manifest: {
              name: 'My Vue PWA',
              short_name: 'VuePWA',
              theme_color: '#ffffff',
              icons: [
                {
                  src: '/icons/android-chrome-512x512.png',
                  sizes: '192x192',
                  type: 'image/png',
                },
                {
                  src: '/icons/android-chrome-512x512.png',
                  sizes: '512x512',
                  type: 'image/png',
                },
              ],
            },
        }),
        laravel({
            input: 'resources/js/app.js',
            // ssr: 'resources/js/ssr.js',
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
    ],
    build: {
        chunkSizeWarningLimit: 2000
    },
});
