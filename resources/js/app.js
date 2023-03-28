import './bootstrap'
import {createApp} from 'vue'
import App from '../App.vue'
import router from './router.js'
import '../css/tailwind.css'
import '../css/app.css'

import Notifications from '@kyvg/vue3-notification'

createApp(App).use(router).use(Notifications, { name: "alert" }).mount("#app")