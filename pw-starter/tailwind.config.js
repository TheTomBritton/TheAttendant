/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './site/templates/**/*.php',
        './site/assets/src/**/*.{js,css}',
    ],
    theme: {
        extend: {
            // Project-specific customisations go here
            // colours, fonts, spacing, etc.
        },
    },
    plugins: [],
};
