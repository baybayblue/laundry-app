import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/assets/scss/style.scss',
                // add your JS files you want compiled by Vite here if needed
                'resources/assets/js/main.js',
                'resources/assets/js/sidebar.js',
                'resources/assets/js/chart.js'
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~bootstrap': resolve(__dirname, 'node_modules/bootstrap'),
            '@tabler/icons-webfont': resolve(__dirname, 'node_modules/@tabler/icons-webfont'),
        }
    }
});
