import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/datatables.js',
                'resources/js/attendance-camera.js',
                'resources/js/attendance-check-in.js',
                'resources/js/crud-form-modal.js',
                'resources/js/work-location-gps.js',
                'resources/js/shift-calendar.js',
                'resources/js/ui-preferences.js',
                'resources/js/wa-scan.js',
            ],
            refresh: true,
        }),
    ],
});
