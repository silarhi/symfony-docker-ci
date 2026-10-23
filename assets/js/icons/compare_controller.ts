import { Controller } from '@hotwired/stimulus'
import { LucideTrash2Icon } from '@icons/index.js'
import { createIconElement, renderIcon } from '@silarhi/ux-icons-js'

// Renders the icon in JavaScript and compares it with the HTML ux_icon() rendered for the same attributes.
// The Twig output comes as a value, not as markup: the browser would serialize parsed markup its own way.
export default class extends Controller {
    static targets = ['preview', 'javascript', 'result']
    static values = { twig: String }
    declare readonly previewTargets: HTMLElement[]
    declare readonly javascriptTarget: HTMLElement
    declare readonly resultTarget: HTMLElement
    declare readonly twigValue: string

    connect() {
        this.previewTargets.forEach((preview) =>
            preview.replaceChildren(
                createIconElement(LucideTrash2Icon, { class: 'text-danger', 'aria-label': 'Delete' })
            )
        )

        const html = renderIcon(LucideTrash2Icon, { class: 'text-danger', 'aria-label': 'Delete' })
        const identical = html === this.twigValue

        this.javascriptTarget.textContent = html
        this.resultTarget.textContent = identical ? 'Identical, byte for byte' : 'Different'
        this.resultTarget.classList.add(identical ? 'text-bg-success' : 'text-bg-danger')
    }
}
