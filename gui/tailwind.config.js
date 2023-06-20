/** @type {import('tailwindcss').Config} */
export default {
    content: ['./src/**/*.{html,js,svelte,ts}'],
    theme: {
        extend: {},
    },
    plugins: [require('@tailwindcss/forms')],
    safelist: [{ pattern: /^col-span-/ }, { pattern: /^row-span-/ }],
};
