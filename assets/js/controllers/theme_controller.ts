import { Controller } from '@hotwired/stimulus'

const STORAGE_KEY = 'theme'

// The light / dark switch of the navbar, like silarhi.fr: the system theme until the visitor picks one,
// then their choice, saved. The inline script of base.html.twig applies it before the first paint;
// CSS swaps the moon and sun icons from the data-bs-theme attribute.
export default class extends Controller<HTMLButtonElement> {
    #media = matchMedia('(prefers-color-scheme: dark)')

    connect() {
        this.#media.addEventListener('change', this.#followSystem)
        this.#syncPressed()
    }

    disconnect() {
        this.#media.removeEventListener('change', this.#followSystem)
    }

    toggle() {
        const theme = this.#isDark() ? 'light' : 'dark'
        try {
            localStorage.setItem(STORAGE_KEY, theme)
        } catch {
            // Storage blocked (private mode): the choice lasts until the next full page load
        }
        this.#apply(theme)
    }

    // A system change only applies while the visitor has not picked a theme
    #followSystem = (event: MediaQueryListEvent) => {
        try {
            if (localStorage.getItem(STORAGE_KEY)) {
                return
            }
        } catch {}
        this.#apply(event.matches ? 'dark' : 'light')
    }

    #apply(theme: 'light' | 'dark') {
        // No fade of every color at once: only the toggle icons animate
        const style = document.createElement('style')
        style.textContent = '*:not(.theme-icon), *::before, *::after { transition: none !important }'
        document.head.append(style)
        document.documentElement.setAttribute('data-bs-theme', theme)
        // Forces the new styles before transitions come back
        void getComputedStyle(document.body).opacity
        requestAnimationFrame(() => style.remove())

        this.#syncPressed()
    }

    #isDark() {
        return 'dark' === document.documentElement.getAttribute('data-bs-theme')
    }

    #syncPressed() {
        this.element.setAttribute('aria-pressed', String(this.#isDark()))
    }
}
