import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/web/app.css',
                'resources/js/web/app.js',
                'resources/css/admin/admin.css',
                'resources/js/admin/admin.js',
                'resources/css/portals/client.css',
                'resources/js/portals/client.js',
                'resources/css/portals/student.css',
                'resources/js/portals/student.js',
            ],
            refresh: true,
        }),
    ],
});
