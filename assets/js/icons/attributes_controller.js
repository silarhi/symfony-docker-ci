import { Controller } from '@hotwired/stimulus'
import { LucideHeartIcon } from '@icons/index.js'
import { resolveIcon } from '@silarhi/ux-icons-js'

const isActive = false

// Attributes given in JavaScript, and what ux_icon() rules make of them
const EXAMPLES = [
    ['{}', {}],
    ["{ 'aria-label': 'Like' }", { 'aria-label': 'Like' }],
    [
        "{ class: ['btn-icon', isActive && 'active', 'text-danger'] }",
        { class: ['btn-icon', isActive && 'active', 'text-danger'] },
    ],
    ["{ width: '2em', height: null }", { width: '2em', height: null }],
    ["{ 'data-config': { id: 1 }, 'data-liked': true }", { 'data-config': { id: 1 }, 'data-liked': true }],
    ["{ style: { color: 'red', margin: null } }", { style: { color: 'red', margin: null } }],
]

// Left out of the output: the same on every row
const HIDDEN = ['xmlns', 'viewBox']

// Fills the table with the attributes resolveIcon() computes, in the browser
export default class extends Controller {
    static targets = ['body']

    connect() {
        this.bodyTarget.replaceChildren(
            ...EXAMPLES.map(([code, attributes]) => {
                const resolved = Object.entries(resolveIcon(LucideHeartIcon, attributes))
                    .filter(([name]) => !HIDDEN.includes(name))
                    .map(([name, value]) => `${name}="${value}"`)
                    .join(' ')

                const row = document.createElement('tr')
                for (const text of [code, resolved]) {
                    const cell = row.insertCell()
                    const content = cell.appendChild(document.createElement('code'))
                    content.textContent = text
                }

                return row
            })
        )
    }
}
