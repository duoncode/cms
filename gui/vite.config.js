import { defineConfig } from 'vite';
import { svelte } from '@sveltejs/vite-plugin-svelte';

export default defineConfig({
    plugins: [svelte()],
    server: {
        port: 2009,
        strictPort: true,
        proxy: {
            '/conia': 'http://localhost:1983',
        },
    },
});
