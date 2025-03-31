import 'dotenv/config'
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import {resolve} from "path";

export default defineConfig({
  resolve: {
    alias: {
      '@': resolve(__dirname, 'resources/js'),
      '@images': resolve(__dirname, 'resources/images'),
      '@fonts': resolve(__dirname, 'resources/fonts'),
    },
  },
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ]
})
