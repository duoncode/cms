import adapter from '@sveltejs/adapter-static';
import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

const paths = process.env.NODE_ENV === 'development' ? { base: '/cms' } : { relative: true };

/** @type {import('@sveltejs/kit').Config} */
const config = {
	preprocess: vitePreprocess(),
	kit: {
		paths,
		adapter: adapter({
			pages: 'build',
			assets: 'build',
			fallback: 'index.html',
		}),
		prerender: { entries: [] },
		alias: {
			$lib: './src/lib',
			$types: './src/types',
			$shell: './src/shell',
		},
	},
};

export default config;
