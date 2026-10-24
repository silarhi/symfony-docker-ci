import AttributesController from '@/js/icons-showcase/attributes_controller'
import CompareController from '@/js/icons-showcase/compare_controller'
import CopyController from '@/js/icons-showcase/copy_controller'
import ReactController from '@/js/icons-showcase/react_controller'

// The controllers of the /icons showcase, registered by its own entry so the other pages do not load the icons
window.app.register('icons-attributes', AttributesController)
window.app.register('icons-compare', CompareController)
window.app.register('icons-copy', CopyController)
window.app.register('icons-react', ReactController)
