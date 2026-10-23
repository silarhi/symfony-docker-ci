import { Controller } from '@hotwired/stimulus'

// Shows or hides the password of the input target. Twig renders both icons: the toggle only swaps
// which one is hidden, so no icon is loaded in JavaScript.
export default class extends Controller {
    static targets = ['input', 'show', 'hide']
    declare readonly inputTarget: HTMLInputElement
    declare readonly showTarget: HTMLElement
    declare readonly hideTarget: HTMLElement

    toggle(event: Event) {
        const visible = 'password' === this.inputTarget.type
        this.inputTarget.type = visible ? 'text' : 'password'
        this.showTarget.classList.toggle('d-none', visible)
        this.hideTarget.classList.toggle('d-none', !visible)
        ;(event.currentTarget as HTMLElement).setAttribute('aria-pressed', String(visible))
    }
}
