import type { Application } from '@hotwired/stimulus'

declare global {
    interface Window {
        // The Stimulus application, set by stimulus_bootstrap.ts for the page entries to register their controllers
        app: Application
    }
}
