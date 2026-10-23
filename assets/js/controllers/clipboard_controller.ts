import { Controller } from '@hotwired/stimulus'

// Copies the text value, and says so in the label target for 2 seconds.
export default class extends Controller {
    static targets = ['label']
    static values = { text: String }
    declare readonly labelTarget: HTMLElement
    declare readonly textValue: string

    #label = ''
    #timeout?: ReturnType<typeof setTimeout>

    connect() {
        this.#label = this.labelTarget.textContent ?? ''
    }

    disconnect() {
        clearTimeout(this.#timeout)
    }

    async copy() {
        try {
            await navigator.clipboard.writeText(this.textValue)
            this.#show('Copied')
        } catch {
            // Denied permission, or no Clipboard API outside a secure context (plain http)
            this.#show('Select it')
        }
    }

    #show(text: string) {
        this.labelTarget.textContent = text
        clearTimeout(this.#timeout)
        this.#timeout = setTimeout(() => {
            this.labelTarget.textContent = this.#label
        }, 2000)
    }
}
