import { defineConfig } from 'vite';
import { svelte } from '@sveltejs/vite-plugin-svelte';

export default defineConfig({
    plugins: [svelte()],
    base: '', // use relative paths
    server: {
        port: 2009,
        strictPort: true,
        proxy: {
            '/panel/api': {
                target: 'http://localhost:1983',
                secure: false,
            }
        },
    },
});
