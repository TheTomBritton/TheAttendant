/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './site/templates/**/*.php',
        './site/assets/src/**/*.{js,css}',
    ],
    theme: {
        extend: {
            colors: {
                surface: {
                    DEFAULT: '#141416',   // Main background
                    50: '#1a1a1e',        // Slightly lighter
                    100: '#1f1f24',       // Cards / raised elements
                    200: '#28282e',       // Borders, subtle dividers
                    300: '#35353d',       // Hover states
                    400: '#4a4a55',       // Muted elements
                },
                accent: {
                    DEFAULT: '#d4a039',   // Primary amber/gold
                    light: '#e4b860',     // Hover / lighter variant
                    dark: '#b8872e',      // Active / pressed
                    muted: 'rgba(212, 160, 57, 0.15)', // Subtle backgrounds
                },
                text: {
                    DEFAULT: '#e8e6e1',   // Primary body text
                    muted: '#9a9a9a',     // Secondary / meta text
                    heading: '#f5f3ef',   // Headings — slightly brighter
                },
            },
            fontFamily: {
                sans: [
                    'Inter', 'system-ui', '-apple-system', 'BlinkMacSystemFont',
                    '"Segoe UI"', 'Roboto', '"Helvetica Neue"', 'Arial', 'sans-serif',
                ],
            },
        },
    },
    plugins: [],
};
