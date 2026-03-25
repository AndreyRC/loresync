import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#4F46E5',
                'primary-hover': '#4338CA',
                accent: '#8B5CF6',
                interactive: '#22D3EE',
                'app-bg': '#0F172A',
                surface: '#1E293B',
                border: '#334155',
                success: '#22C55E',
                error: '#EF4444',
                warning: '#F59E0B',
            },
        },
    },

    plugins: [forms],
};
