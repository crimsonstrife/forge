/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo'
import './kanban.js'
// Bootstrap (JS and CSS)
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

// Web Awesome styles
import '@awesome.me/webawesome/dist/styles/themes/default.css'

// Cherry-pick the WA components
import '@awesome.me/webawesome/dist/components/button/button.js'
import '@awesome.me/webawesome/dist/components/icon/icon.js'
import '@awesome.me/webawesome/dist/components/input/input.js'
import '@awesome.me/webawesome/dist/components/select/select.js'
import '@awesome.me/webawesome/dist/components/option/option.js'
import '@awesome.me/webawesome/dist/components/avatar/avatar.js'
import '@awesome.me/webawesome/dist/components/checkbox/checkbox.js'
import '@awesome.me/webawesome/dist/components/badge/badge.js'
import '@awesome.me/webawesome/dist/components/dropdown/dropdown.js'
import '@awesome.me/webawesome/dist/components/progress-bar/progress-bar.js'
import '@awesome.me/webawesome/dist/components/textarea/textarea.js'
import '@awesome.me/webawesome/dist/components/tab-group/tab-group.js'
import '@awesome.me/webawesome/dist/components/tab-panel/tab-panel.js'
import '@awesome.me/webawesome/dist/components/tab/tab.js'
import '@awesome.me/webawesome/dist/components/scroller/scroller.js'

// Tell WA where to find its assets (icons, etc.)
import { setBasePath } from '@awesome.me/webawesome/dist/webawesome.js'
setBasePath('/vendor/webawesome')
