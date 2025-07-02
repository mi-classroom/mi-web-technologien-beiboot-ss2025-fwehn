import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ['class'],
    mode: 'jit',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{vue,js,ts,jsx,tsx}',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Spline Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
            primary: {
                DEFAULT: '#2ABDBD',
                hover: '#24a7a7', // etwas dunkler für Hover
            },
            secondary: {
                DEFAULT: '#FF4F5B',
                hover: '#e64550', // ebenfalls dunkler
            },
                warm: {
                    light: '#EBE9E0',
                    medium: '#A4A195',
                    dark: '#4C4B45',
                },
                dark: {
                    DEFAULT: '#666666'
                },
                darker: {
                    DEFAULT: '#333333'
                },
                darkest: {
                    DEFAULT: '#000000'
                },
                darken: {
                    DEFAULT: '#000000CC'
                },
                light: {
                    DEFAULT: '#F4F4F4'
                },
                lightest: {
                    DEFAULT: '#FFFFFF'
                },
                lighten: {
                    DEFAULT: '#FFFFFFCC'
                }
            },
            spacing: {
                '3xs': '5px',
                'xxs': '10px',
                'xs': '12xp',
                's': '16px',
                'm': '20px',
                'l': '25px',
                'xl': '32px',
                'xxl': '40px',
            },
            borderWidth: {
                'xs': '0.5px',
                's': '1px',
                'm': '2px'
            }
        },
    },
    plugins: [require('tailwindcss-animate')],
};
