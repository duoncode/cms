import path from 'path';
import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';

const devport = 1983;

export default defineConfig({
    plugins: [sveltekit()],
    server: {
        port: 2009,
        strictPort: true,
        proxy: {
            '/panel/api': {
                target: `http://localhost:${devport}`,
                secure: false,
            },
            '/assets': {
                target: `http://localhost:${devport}`,
                secure: false,
            },
            '/cache': {
                target: `http://localhost:${devport}`,
                secure: false,
            },
            '/media': {
                target: `http://localhost:${devport}`,
                secure: false,
            },
            '/images': {
                target: `http://localhost:${devport}`,
                secure: false,
            },
            '/preview': {
                target: `http://localhost:${devport}`,
                secure: false,
            },
            '/vendor': {
                target: `http://localhost:${devport}`,
                secure: false,
            },
            '/fonts': {
                target: `http://localhost:${devport}`,
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
