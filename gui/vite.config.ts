import path from 'path';
import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [sveltekit()],
    server: {
        port: 2009,
        strictPort: true,
        proxy: {
            '/panel/api': {
                target: 'http://localhost:1983',
                secure: false,
            },
        },
    },
    resolve: {
        alias: {
            $lib: path.resolve('./src/lib'),
            $types: path.resolve('./src/types'),
            $shell: path.resolve('./src/shell'),
        },
    },
});
