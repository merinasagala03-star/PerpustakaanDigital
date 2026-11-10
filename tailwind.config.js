import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // Kelas badge dirangkai dinamis di Blade (badge--{{ $status }}), jadi harus di-safelist agar tidak ter-purge.
    safelist: ['badge--diajukan', 'badge--dipinjam', 'badge--dikembalikan', 'badge--ditolak', 'badge--role'],
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
        },
    },

    plugins: [forms],
};
