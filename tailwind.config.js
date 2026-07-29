import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Manrope', ...defaultTheme.fontFamily.sans],
                display: ['Cormorant Garamond', ...defaultTheme.fontFamily.serif],
            },
            borderRadius: {
                DEFAULT: '0px',
                sm: '2px',
                md: '2px',
                lg: '2px',
                xl: '2px',
                '2xl': '2px',
                '3xl': '2px',
                full: '9999px',
            },
            boxShadow: {
                sm: 'none',
                DEFAULT: 'none',
                md: 'none',
                lg: 'none',
                xl: 'none',
                '2xl': 'none',
                inner: 'none',
                none: 'none',
            },
        },
    },

    plugins: [forms],
};
