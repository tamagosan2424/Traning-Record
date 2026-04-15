import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // Dark theme base
        'bg-slate-950', 'bg-slate-900', 'bg-slate-800', 'bg-slate-700',
        'border-slate-800', 'border-slate-700', 'border-slate-600',
        'text-white', 'text-slate-100', 'text-slate-200', 'text-slate-300',
        'text-slate-400', 'text-slate-500', 'text-slate-600',
        // Accent colors
        'text-violet-400', 'text-violet-500', 'bg-violet-500/10', 'bg-violet-600',
        'text-indigo-400', 'bg-indigo-500/10',
        'text-emerald-400', 'bg-emerald-500/10',
        'text-amber-400', 'bg-amber-500/10',
        'text-red-400', 'bg-red-400/10', 'border-red-400/20',
        // Gradient
        'bg-gradient-to-r', 'bg-gradient-to-b', 'bg-gradient-to-br',
        'from-violet-600', 'to-indigo-500', 'from-violet-500', 'to-indigo-400',
        'from-violet-500', 'from-indigo-500',
        'from-emerald-500', 'to-teal-500',
        'from-amber-500', 'to-orange-500',
        'from-indigo-500', 'to-blue-500',
        'text-transparent', 'bg-clip-text',
        // JS-generated exercise blocks
        'exercise-block', 'sets-container', 'set-num', 'prev-info', 'prev-text',
        // Layout
        'flex', 'gap-2', 'gap-3', 'items-center', 'items-start', 'justify-between',
        'w-7', 'w-20', 'shrink-0', 'ml-auto', 'font-mono',
        'space-y-1', 'space-y-2', 'mt-3', 'py-1', 'py-1.5',
        'rounded-2xl', 'rounded-xl', 'rounded-lg', 'overflow-hidden',
        'border', 'border-b', 'px-5', 'py-4', 'py-3', 'px-5',
        // Input classes (JS generated)
        'w-20', 'bg-slate-800', 'border-slate-700', 'rounded-lg', 'px-2.5', 'py-2',
        'text-sm', 'text-center', 'focus:outline-none', 'focus:ring-2',
        'focus:ring-violet-500/40', 'focus:border-violet-500', 'transition',
        // Hover states
        'hover:bg-slate-800', 'hover:bg-slate-700', 'hover:text-red-400',
        'hover:text-violet-300', 'hover:text-slate-300', 'hover:bg-red-400/10',
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
