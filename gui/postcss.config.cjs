const postcssImport = require('postcss-import');
const postcssNested = require('postcss-nested');
const postcssCustomMedia = require('postcss-custom-media');
const autoprefixer = require('autoprefixer');

module.exports = {
    plugins: [
        postcssImport(),
        postcssCustomMedia({
            importFrom: 'src/styles/vars.css',
        }),
        postcssNested,
        autoprefixer(),
    ],
};
