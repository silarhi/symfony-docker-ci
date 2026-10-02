import { fileURLToPath } from 'node:url'
import purgeCSSPlugin from '@fullhuman/postcss-purgecss'
import Symfony from '@symfony/reprise/vite'
import react from '@vitejs/plugin-react'
import { defineConfig } from 'vite'

const assets = fileURLToPath(new URL('./assets', import.meta.url))

export default defineConfig(({ mode }) => {
    const isDev = mode === 'development'

    return {
        plugins: [
            react(),
            Symfony({
                // Registers controllers.json + assets/js/controllers/ behind "virtual:symfony/controllers".
                // controllersDir is explicit: Reprise would otherwise default to assets/controllers/.
                stimulus: {
                    controllersJson: 'assets/controllers.json',
                    controllersDir: 'assets/js/controllers',
                },
                // Replaces Encore's copyFiles(): files land in public/build/images/ and are
                // registered in manifest.json, so asset('build/images/…') keeps resolving.
                copy: [
                    {
                        from: 'assets/images',
                        to: 'images',
                    },
                ],
            }),
        ],

        css: {
            postcss: {
                plugins: [
                    // Drops the Tabler selectors no template or component uses. Runs in every mode so
                    // `yarn watch` shows exactly what ships. Classes added only at runtime must be safelisted.
                    purgeCSSPlugin({
                        content: ['templates/**/*.twig', 'assets/js/**/*.{js,jsx}'],
                        safelist: [
                            // Toggled by Tabler's collapse plugin (mobile navbar)
                            'show',
                            'collapsing',
                            'collapsed',
                        ],
                    }),
                ],
            },
        },

        resolve: {
            alias: {
                '@': assets,
            },
        },

        build: {
            // Encore's addEntry() equivalent. Reprise turns each key into an
            // entrypoints.json entry consumed by reprise_entry_*_tags().
            rollupOptions: {
                input: {
                    app: `${assets}/js/app.js`,
                    index: `${assets}/js/pages/index.jsx`,
                },
            },

            // Dev build profile, used by `yarn dev` and `yarn watch`.
            // Vite minifies and omits sourcemaps in build mode whatever the --mode,
            // so restore Encore's enableSourceMaps(!isProduction) behaviour explicitly.
            ...(isDev && {
                sourcemap: true,
                minify: false,
            }),
        },
    }
})
