import { startStimulusApp } from '@symfony/reprise/stimulus'

// Registers Stimulus controllers from controllers.json and the controllers/ directory,
// both resolved at build time by the @symfony/reprise Vite plugin. A controller marked
// /* stimulusFetch: 'lazy' */ is a chunk of its own, loaded when its element first appears.
startStimulusApp()
