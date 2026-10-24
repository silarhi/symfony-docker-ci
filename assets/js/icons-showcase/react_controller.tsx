import { Controller } from '@hotwired/stimulus'
import type { ReactNode } from 'react'
import { createRoot, type Root } from 'react-dom/client'
import Rating from '@/js/icons-showcase/Rating'
import StatusBoard from '@/js/icons-showcase/StatusBoard'
import Trash from '@/js/icons-showcase/Trash'

// The props come from Twig as a JSON object: each example reads the ones it takes
const COMPONENTS: Record<string, (props: Record<string, unknown>) => ReactNode> = {
    Rating: ({ max }) => <Rating max={typeof max === 'number' ? max : undefined} />,
    StatusBoard: ({ url }) => <StatusBoard url={String(url)} />,
    Trash: () => <Trash />,
}

// Mounts one of the React examples of the showcase, with its props
export default class extends Controller {
    static values = { component: String, props: Object }
    declare readonly componentValue: string
    declare readonly propsValue: Record<string, unknown>

    #root?: Root

    connect() {
        if (document.documentElement.hasAttribute('data-turbo-preview')) {
            return
        }
        const render = COMPONENTS[this.componentValue]
        if (undefined === render) {
            throw new Error(`Unknown React example "${this.componentValue}".`)
        }
        this.#root = createRoot(this.element)
        this.#root.render(render(this.propsValue))
    }

    disconnect() {
        this.#root?.unmount()
    }
}
