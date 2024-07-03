import { Controller } from '@hotwired/stimulus';
import React from 'react';
import App from '../component/App';
import {createRoot} from 'react-dom/client';

class IndexController extends Controller {
    #root;
    connect() {
        this.#root = createRoot(this.element);
        this.#root.render(<App/>);
    }

    disconnect() {
        this.#root.unmount();
    }
}

window.app.register('index', IndexController);
