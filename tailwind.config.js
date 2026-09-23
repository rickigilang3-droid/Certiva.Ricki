import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0px) scale(1)' },
                    '50%': { transform: 'translateY(-16px) scale(1.04)' },
                },
                floatSlow: {
                    '0%, 100%': { transform: 'translate(0px, 0px) scale(1)' },
                    '33%': { transform: 'translate(25px, -20px) scale(1.08)' },
                    '66%': { transform: 'translate(-15px, 15px) scale(0.96)' },
                },
                floatReverse: {
                    '0%, 100%': { transform: 'translate(0px, 0px) scale(1)' },
                    '50%': { transform: 'translate(-20px, 20px) scale(1.05)' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeInDown: {
                    '0%': { opacity: '0', transform: 'translateY(-14px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                pulseGlow: {
                    '0%, 100%': { opacity: '0.5', transform: 'scale(1)' },
                    '50%': { opacity: '0.9', transform: 'scale(1.06)' },
                },
            },
            animation: {
                float: 'float 6s ease-in-out infinite',
                'float-slow': 'floatSlow 12s ease-in-out infinite',
                'float-reverse': 'floatReverse 9s ease-in-out infinite',
                'fade-in-up': 'fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                'fade-in-down': 'fadeInDown 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                'pulse-glow': 'pulseGlow 3s ease-in-out infinite',
            },
        },
    },

    plugins: [forms],
};
