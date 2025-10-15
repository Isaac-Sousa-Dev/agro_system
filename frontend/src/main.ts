import { createApp } from 'vue'
import { createPinia } from 'pinia'
import PrimeVue from 'primevue/config'
import ToastService from 'primevue/toastservice';
import Aura from '@primevue/themes/aura'
import 'primeicons/primeicons.css'

import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/auth'
import mask from './directives/mask'

// Importar Tailwind por último para ter prioridade
import './assets/main.css'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.directive('mask', mask)
app.use(ToastService)
app.use(PrimeVue, {
  theme: {
    preset: Aura,
    options: {
      darkModeSelector: false,
      cssLayer: false
    }
  }
})

// Initialize auth store
const authStore = useAuthStore()
authStore.fetchUser()

app.mount('#app')
