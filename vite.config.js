import {defineConfig} from 'vite'
import liveReload from 'vite-plugin-live-reload'

const {resolve} = require('path')
const fs = require('fs')

export default defineConfig({

    plugins: [
        liveReload(__dirname + '/**/*.php')
    ],

    root: '',
    base: process.env.NODE_ENV === 'development'
        ? '/'
        : '/dist/',

    build: {
        outDir: resolve(__dirname, './dist'),
        rollupOptions: {
            input: {
                main: resolve(__dirname + '/main.js')
            },
            output: {
                entryFileNames: `assets/[name].js`,
                chunkFileNames: `assets/[name].js`,
                assetFileNames: `assets/[name].[ext]`
            }
        },

        // minifying switch
        minify: true,
        write: true
    },

    server: {
        cors: true,
        strictPort: true,
        port: 3000,
        https: false,

        hmr: {
            host: 'localhost',
        },

    },

    resolve: {
        alias: {}
    }
})

