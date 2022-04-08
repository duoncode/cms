const postcssImport = require('postcss-import');
const postcssNested = require('postcss-nested');
const postcssCustomMedia = require('postcss-custom-media');
const autoprefixer = require('autoprefixer');

module.exports = {
    plugins: [
        postcssImport(),
        postcssNested(),
        postcssCustomMedia({
            importFrom: 'src/styles/vars.css',
        }),
        autoprefixer(),
    ],
};
