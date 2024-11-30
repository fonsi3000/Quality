import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue()
    ],
    server: {
        host: '0.0.0.0',
        hmr: {
            host: process.env.VITE_HMR_HOST || 'localhost',
            protocol: 'ws'
        },
        watch: {
            usePolling: true
        },
        port: 5173
    }
});