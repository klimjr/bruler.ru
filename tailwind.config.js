const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    theme: {
        extend: {
            fontFamily: {
                sans: ['Manrope', ...defaultTheme.fontFamily.sans],
                'almeria': ['Almeria', ...defaultTheme.fontFamily.sans],
            },
            screens: {
                "phone": {"max": "768px"},
                "desktop": {"max": "1536px"},
                "tablet": {"max": "992px"},
                "4xl": "1920px",

                "max-2xl": {max: "1700px"},
                'max-xl': {max: "1365px"},
                'max-lg': {max: "1023px"},
                'max-md': {max: "767px"},
                'max-sm': {max: "479px"},
            },
            colors: {
                'grey-100': 'var(--grey-100)',
                'grey-200': 'var(--grey-200)',
                'grey-300': 'var(--grey-300)',
                'red': 'var(--red)',
                'red-600': 'var(--red)',
                'color-111': '#999999',
                'green': 'var(--green)',
                'green-100': 'var(--green-100)',
                primary: '#000000',
                secondary: '#AAAAAA',
                gray: {
                    DEFAULT: '#4B4B4B',
                    50: '#F9FAFB',
                    100: '#F3F4F6',
                    200: '#E5E7EB',
                    300: '#D1D5DB',
                    400: '#9CA3AF',
                    500: '#6B7280',
                    600: '#4B5563',
                    700: '#374151',
                    800: '#1F2937',
                    900: '#111827',
                },
                white: '#FFFFFF',
                black: 'var(--black)',
                'black-opacity': 'var(--black-opacity)',
            },
            height: {
                'full-banner': 'var(--heightFullBanner)',
                'full-banner-running': 'var(--heightFullBannerRunning)',
            },
            boxShadow: {
                'header': 'var(--headerShadow)',
            },
        },
    },
    variants: {
        extend: {
            backgroundColor: ['active'],
        }
    },
    content: [
        './app/**/*.php',
        './resources/**/*.html',
        './resources/**/*.scss',
        './resources/**/*.js',
        './resources/**/*.jsx',
        './resources/**/*.ts',
        './resources/**/*.tsx',
        './resources/**/*.php',
        './resources/**/*.vue',
        './resources/**/*.twig',
        './app/Filament/**/*.php',
        './vendor/filament/**/*.blade.php', // Add Filament views
        './resources/views/filament/**/*.blade.php', // Add your Filament custom views
        './resources/views/livewire/**/*.blade.php', // Add your Livewire views
    ],
    safelist: [
        'bg-black',
        'bg-white',
        'bg-blue',
        'bg-gray',
        'bg-[#41424C]',
        'bg-[#c3b091]',
        'bg-[#BBC4BF]',
        'bg-[#ADB34F]',
        'bg-[#008DC5]',
        'bg-[#323137]', // blue graphite (Графитовый)
        'bg-[#A3B4C4]', // celestial blue (Небесно-голубой)
        'bg-[#5C634F]', // four leaf clover (Хаки)
        'bg-[#BBC4BF]', // (Типо серый)
        'bg-[#0000FF]',
        'bg-[#FFFF00]',

        'bg-[#00FF00]',
        'bg-[#000080]',
        'bg-[#6B2A21]',
        'bg-[#50C878]',
        'bg-[#DB7093]'
    ],
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
