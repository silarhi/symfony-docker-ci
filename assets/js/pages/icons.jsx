import AttributesController from '@/js/icons/attributes_controller'
import CompareController from '@/js/icons/compare_controller'
import CopyController from '@/js/icons/copy_controller'
import ReactController from '@/js/icons/react_controller'

// The controllers of the /icons showcase, registered by its own entry so the other pages do not load the icons
window.app.register('icons-attributes', AttributesController)
window.app.register('icons-compare', CompareController)
window.app.register('icons-copy', CopyController)
window.app.register('icons-react', ReactController)
