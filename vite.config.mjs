import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

export default defineConfig({
    css: {
        postcss: {
            plugins: [],
        },
    },
    plugins: [
        laravel({
            input: ['resources/spa/main.tsx'],
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/spa'),
        },
    },
    server: {
        host: '127.0.0.1',
        port: 5173,
        strictPort: false,
        hmr: {
            host: '127.0.0.1',
            port: 5173,
        },
    },
});
