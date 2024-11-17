module.exports = {
    root: true,
    globals: {
        __FIVEORBS_CONFIG__: 'readonly',
    },
    extends: [
        'eslint:recommended',
        'plugin:@typescript-eslint/recommended',
        'plugin:svelte/recommended',
        'prettier',
    ],
    parser: '@typescript-eslint/parser',
    plugins: ['@typescript-eslint'],
    parserOptions: {
        sourceType: 'module',
        ecmaVersion: 2020,
        extraFileExtensions: ['.svelte'],
    },
    env: {
        browser: true,
        es2017: true,
        node: true,
    },
    overrides: [
        {
            files: ['*.svelte'],
            parser: 'svelte-eslint-parser',
            parserOptions: {
                parser: '@typescript-eslint/parser',
            },
        },
    ],
    settings: {
        'import/resolver': {
            typescript: {},
            alias: {
                map: [
                    ['$lib', './src/lib'],
                    ['$shell', './src/shell'],
                ],
            },
        },
    },
};
