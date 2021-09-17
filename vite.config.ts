import {defineConfig} from 'laravel-vite'
import vue            from '@vitejs/plugin-vue'
// @ts-ignore
import tailwind       from 'tailwindcss'
import autoprefixer   from 'autoprefixer'

export default defineConfig()
    .withPlugin(vue)
    .withPostCSS([
        tailwind,
        autoprefixer
    ])