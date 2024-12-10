import { createApp } from 'vue'
import { createPinia } from 'pinia'

// Vuetify
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css'
import { aliases, mdi } from 'vuetify/iconsets/mdi'
import 'vuetify/styles'

import VueSweetalert2 from 'vue-sweetalert2'
// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';

const vuetify = createVuetify({
    components,
    directives,
    icons: {
      defaultSet: 'mdi',
      aliases,
      sets: {
        mdi,
      },
    },
  })

import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import App from './App.vue'
import router from './router'
/* import axios from 'axios' */

const app = createApp(App)
const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)
/* app.config.globalProperties.$axios = axiosInstance; */
app.use(pinia)
app.use(router)
app.use(vuetify)
app.use(VueSweetalert2)
window.Swal =  app.config.globalProperties.$swal;
/* import Swal from 'sweetalert2'
Swal.fire('message') */
/* app.config.globalProperties.$axios = axios;
window.axios = axios 
axios.defaults.baseURL = 'http://localhost:9000/api/' 
axios.post('api/products'); */
app.mount('#app')
