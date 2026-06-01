import { Controller } from '@hotwired/stimulus'
import React from 'react'
import { createRoot } from 'react-dom/client'
import App from '../component/App'

class IndexController extends Controller {
    #root
    connect() {
        if (document.documentElement.hasAttribute('data-turbo-preview')) {
            return
        }
        this.#root = createRoot(this.element)
        this.#root.render(<App />)
    }

    disconnect() {
        this.#root?.unmount()
    }
}

window.app.register('index', IndexController)
