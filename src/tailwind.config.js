import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                wine: {
                    50: '#faf3f5',
                    100: '#f5e6ea',
                    200: '#e8c4cf',
                    300: '#d69cae',
                    400: '#bd6b86',
                    500: '#a34a68',
                    600: '#8a3552',
                    700: '#722741', // primary accent
                    800: '#5c1e35',
                    900: '#47172a',
                    950: '#2d0e1a',
                },
                cream: '#faf8f5',
                ink: '#1f1b1d',
            },
            fontFamily: {
                display: ['"Playfair Display"', 'Georgia', 'serif'],
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            letterSpacing: {
                boutique: '0.22em',
            },
        },
    },
    plugins: [],
};
