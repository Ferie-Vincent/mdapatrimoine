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
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50:  '#e8edf8',
                    100: '#c5d0ed',
                    200: '#9baee0',
                    300: '#708cd3',
                    400: '#4a6ec4',
                    500: '#1E3A8A',
                    600: '#1a327a',
                    700: '#152968',
                    800: '#112157',
                    900: '#0c1845',
                    950: '#070e2b',
                },
                'accent-orange': {
                    50:  '#fdf3e7',
                    100: '#f9dfc3',
                    200: '#f3c089',
                    300: '#eca04f',
                    400: '#D4812A',
                    500: '#b86b1e',
                    600: '#9c5a18',
                    700: '#7f4913',
                    800: '#63380e',
                    900: '#46280a',
                },
                'accent-yellow': {
                    50:  '#fefce8',
                    100: '#fef6c3',
                    200: '#fded6b',
                    300: '#F0D020',
                    400: '#d9bb1a',
                    500: '#b89e15',
                    600: '#967f11',
                    700: '#75630d',
                    800: '#544709',
                    900: '#3d3306',
                },
                'accent-red': {
                    50:  '#fce9e7',
                    100: '#f6c8c3',
                    200: '#ee9a91',
                    300: '#e06b5f',
                    400: '#C42618',
                    500: '#a82015',
                    600: '#8d1b11',
                    700: '#71150e',
                    800: '#56100a',
                    900: '#3a0b07',
                },
                'accent-green': {
                    50:  '#e8f5ec',
                    100: '#c3e6ce',
                    200: '#89cda0',
                    300: '#4fb472',
                    400: '#1E9B3E',
                    500: '#1a8535',
                    600: '#166f2c',
                    700: '#125924',
                    800: '#0e441b',
                    900: '#0a2e13',
                },
                sidebar: {
                    DEFAULT: '#0F1D3D',
                    light: '#152750',
                    lighter: '#1C3160',
                },
            },
        },
    },

    plugins: [forms],
};
