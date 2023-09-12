import path from 'path';
import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';

const devport = 1983;
const host = process.env.CONIA_DEV_HOST ? process.env.CONIA_DEV_HOST : 'localhost';

export default defineConfig({
    plugins: [sveltekit()],
    server: {
        port: 2009,
        host: '0.0.0.0',
        strictPort: true,
        proxy: {
            '/panel/api': {
                target: `http://${host}:${devport}`,
                secure: false,
            },
            '/assets': {
                target: `http://${host}:${devport}`,
                secure: false,
            },
            '/cache': {
                target: `http://${host}:${devport}`,
                secure: false,
            },
            '/media': {
                target: `http://${host}:${devport}`,
                secure: false,
            },
            '/images': {
                target: `http://${host}:${devport}`,
                secure: false,
            },
            '/preview': {
                target: `http://${host}:${devport}`,
                secure: false,
            },
            '/vendor': {
                target: `http://${host}:${devport}`,
                secure: false,
            },
            '/fonts': {
                target: `http://${host}:${devport}`,
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
