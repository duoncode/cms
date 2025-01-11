import path from 'path';
import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';

const devport = process.env.CMS_DEV_PORT ? parseInt(process.env.CMS_DEV_PORT, 10) : 2009;
const devhost = process.env.CMS_DEV_HOST ? process.env.CMS_DEV_HOST : 'localhost';
const appport = process.env.CMS_APP_PORT ? parseInt(process.env.CMS_APP_PORT, 10) : 1983;
const apphost = process.env.CMS_APP_HOST ? process.env.CMS_APP_HOST : 'localhost';

export default defineConfig({
	plugins: [sveltekit()],
	server: {
		port: devport,
		host: devhost,
		strictPort: true,
		proxy: {
			'/panel/api': {
				target: `http://${apphost}:${appport}`,
				secure: false,
			},
			'/assets': {
				target: `http://${apphost}:${appport}`,
				secure: false,
			},
			'/cache': {
				target: `http://${apphost}:${appport}`,
				secure: false,
			},
			'/media': {
				target: `http://${apphost}:${appport}`,
				secure: false,
			},
			'/images': {
				target: `http://${apphost}:${appport}`,
				secure: false,
			},
			'/preview': {
				target: `http://${apphost}:${appport}`,
				secure: false,
			},
			'/vendor': {
				target: `http://${apphost}:${appport}`,
				secure: false,
			},
			'/fonts': {
				target: `http://${apphost}:${appport}`,
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
