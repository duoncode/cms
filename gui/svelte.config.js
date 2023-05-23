import sveltePreprocess from 'svelte-preprocess'

const prod = process.env.BUILD_ENV === 'production';

export default {
    preprocess: [
        sveltePreprocess({
            sourceMap: !prod,
            postcss: true,
        }),
    ],
};
