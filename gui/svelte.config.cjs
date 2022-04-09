const sveltePreprocess = require('svelte-preprocess');

const prod = process.env.BUILD_ENV === 'production';

module.exports = {
    preprocess: [
        sveltePreprocess({
            sourceMap: !prod,
            postcss: true,
        }),
    ],
};
