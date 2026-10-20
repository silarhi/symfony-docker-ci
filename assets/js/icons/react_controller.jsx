import { Controller } from '@hotwired/stimulus'
import { createRoot } from 'react-dom/client'
import Rating from '@/js/icons/Rating'
import StatusBoard from '@/js/icons/StatusBoard'
import Trash from '@/js/icons/Trash'

const COMPONENTS = { Rating, StatusBoard, Trash }

// Mounts one of the React examples of the showcase, with its props
export default class extends Controller {
    static values = { component: String, props: Object }

    #root

    connect() {
        if (document.documentElement.hasAttribute('data-turbo-preview')) {
            return
        }
        const Component = COMPONENTS[this.componentValue]
        this.#root = createRoot(this.element)
        this.#root.render(<Component {...this.propsValue} />)
    }

    disconnect() {
        this.#root?.unmount()
    }
}
