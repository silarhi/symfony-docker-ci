/*
 * Renders the social preview of labs.silarhi.fr (assets/images/og-image.jpg, 1200×630, og:image) from scripts/og-image.html with
 * headless Chrome. Run `pnpm og-image` after changing the template, and commit it. Chrome is looked up at its macOS location; set
 * CHROME_PATH to use another binary.
 */
import { execFileSync } from 'node:child_process'
import { mkdirSync, statSync } from 'node:fs'
import { dirname, resolve } from 'node:path'
import { fileURLToPath, pathToFileURL } from 'node:url'

const rootDir = resolve(dirname(fileURLToPath(import.meta.url)), '..')
const template = pathToFileURL(resolve(rootDir, 'scripts/og-image.html')).href
const chrome = process.env.CHROME_PATH || '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'

const images = [
    { output: 'assets/images/og-image.jpg', width: 1200, height: 630, hash: '' },
]

for (const { output, width, height, hash } of images) {
    const path = resolve(rootDir, output)
    mkdirSync(dirname(path), { recursive: true })
    execFileSync(
        chrome,
        [
            '--headless',
            '--hide-scrollbars',
            // The template loads the logo from the repository through a file:// URL
            '--allow-file-access-from-files',
            '--force-device-scale-factor=1',
            `--window-size=${width},${height}`,
            // Leaves time for the web fonts to load before the capture
            '--virtual-time-budget=5000',
            `--screenshot=${path}`,
            template + hash,
        ],
        { stdio: 'ignore' }
    )
    console.log(`${output}: ${(statSync(path).size / 1024).toFixed(1)} KB`)
}
