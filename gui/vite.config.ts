import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [sveltekit()],
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
