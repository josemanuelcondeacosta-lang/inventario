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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Space Grotesk"', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Superficies y textos — cambian con el tema
                fondo:      'rgb(var(--c-fondo) / <alpha-value>)',
                superficie: 'rgb(var(--c-superficie) / <alpha-value>)',
                elevado:    'rgb(var(--c-elevado) / <alpha-value>)',
                borde:      'rgb(var(--c-borde) / <alpha-value>)',
                texto:      'rgb(var(--c-texto) / <alpha-value>)',
                suave:      'rgb(var(--c-suave) / <alpha-value>)',
                tenue:      'rgb(var(--c-tenue) / <alpha-value>)',

                // Acentos — también se ajustan por tema
                brand: {
                    DEFAULT: 'rgb(var(--c-brand) / <alpha-value>)',
                    fuerte:  'rgb(var(--c-brand-fuerte) / <alpha-value>)',
                    suave:   'rgb(var(--c-brand-suave) / <alpha-value>)',
                    texto:   'rgb(var(--c-brand-texto) / <alpha-value>)',
                },
                alerta: {
                    DEFAULT: 'rgb(var(--c-alerta) / <alpha-value>)',
                    suave:   'rgb(var(--c-alerta-suave) / <alpha-value>)',
                },
                aviso: {
                    DEFAULT: 'rgb(var(--c-aviso) / <alpha-value>)',
                    suave:   'rgb(var(--c-aviso-suave) / <alpha-value>)',
                },
                // Barra lateral: oscura en ambos temas
                nav: {
                    fondo: 'rgb(var(--c-nav-fondo) / <alpha-value>)',
                    texto: 'rgb(var(--c-nav-texto) / <alpha-value>)',
                },
            },
            boxShadow: {
                soft: 'var(--s-soft)',
                card: 'var(--s-card)',
                dropdown: 'var(--s-dropdown)',
                glow: '0 0 0 1px rgb(var(--c-brand) / 0.25), 0 6px 20px -6px rgb(var(--c-brand) / 0.35)',
            },
        },
    },

    plugins: [forms],
};