import { startStimulusApp } from '@symfony/reprise/stimulus'

// Registers Stimulus controllers from controllers.json and the controllers/ directory,
// both resolved at build time by the @symfony/reprise Vite plugin.
const app = startStimulusApp()

// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
window.app = app
