import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                // Entrées dédiées au template front (styles + scripts)
                // Exemple : à adapter une fois les fichiers du template ajoutés
                // 'resources/css/template/main.css',
                // 'resources/js/template/main.js',
            ],
            refresh: true,
        }),
    ],
});
