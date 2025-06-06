import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // JavaScript files
                'resources/js/vendors.js',

                // SASS files
                'resources/sass/vendors.scss',
                'resources/sass/katniss.scss',
                'resources/sass/app.scss',

                'resources/js/katniss.js',
                'resources/js/ResizeSensor.js',
                'resources/js/main.js',
                'resources/js/pages/centipede.js',

                // TypeScript files
                'resources/js/pages/nash.ts',
            ],
            refresh: true,
        }),
        // Copy images directory from resources to public
        viteStaticCopy({
            targets: [
                {
                    src: 'resources/images',
                    dest: './'
                }
            ]
        }),
    ],
    resolve: {
        extensions: ['.js', '.jsx', '.ts', '.tsx'],
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    // TODO: グローバル変数への登録を解消する
                    vendor: ['jquery'],
                },
            },
        },
    },
});
