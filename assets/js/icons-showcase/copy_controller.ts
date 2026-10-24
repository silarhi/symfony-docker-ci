import { Controller } from '@hotwired/stimulus'
import { createIconElement } from '@silarhi/ux-icons-js'
import { LucideCheckIcon, LucideCopyIcon, LucideTriangleAlertIcon } from '@/js/icons'

// A copy button: Twig renders the first icon with ux_icon(), JavaScript swaps it after the click.
// createIconElement() builds the <svg> with the DOM API, no HTML is parsed: no innerHTML needed.
export default class extends Controller {
    static targets = ['icon', 'input']
    static values = { text: String }
    declare readonly iconTarget: HTMLElement
    declare readonly inputTarget: HTMLInputElement
    declare readonly textValue: string

    #timeout?: ReturnType<typeof setTimeout>

    async copy() {
        try {
            await navigator.clipboard.writeText(this.textValue)
        } catch {
            // Denied permission, unfocused document, or no Clipboard API outside a secure context (plain http)
            this.#copyFailed()
            return
        }

        this.#showIcon(createIconElement(LucideCheckIcon, { class: 'text-success' }))
    }

    disconnect() {
        clearTimeout(this.#timeout)
    }

    // Shows the given icon for 2 seconds, then the copy icon again
    #showIcon(icon: SVGSVGElement) {
        this.iconTarget.replaceChildren(icon)
        clearTimeout(this.#timeout)
        this.#timeout = setTimeout(() => this.iconTarget.replaceChildren(createIconElement(LucideCopyIcon)), 2000)
    }

    // The command is selected, so Ctrl/Cmd+C copies it by hand, and the icon says why
    #copyFailed() {
        this.inputTarget.focus()
        this.inputTarget.select()
        this.#showIcon(createIconElement(LucideTriangleAlertIcon, { class: 'text-danger' }))
    }
}
