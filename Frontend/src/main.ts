import './assets/main.css'

import 'primeicons/primeicons.css';
import 'primeflex/primeflex.css';
import { createApp, ref, computed } from 'vue'
import App from './App.vue'
import router from './router'
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';

// --- Gestione sessione utente centralizzata ---
const USER_SESSION_KEY = 'user'
export type UserSession = {
  ute_id: number
  ute_username: string
  ute_ruolo: string
}
function getUserFromStorage(): UserSession | null {
  const raw = localStorage.getItem(USER_SESSION_KEY)
  return raw ? JSON.parse(raw) : null
}
const userRef = ref<UserSession | null>(getUserFromStorage())
export const userSession = computed({
  get() {
    return userRef.value
  },
  set(user: UserSession | null) {
    if (user) {
      userRef.value = user
      localStorage.setItem(USER_SESSION_KEY, JSON.stringify(user))
    } else {
      userRef.value = null
      localStorage.removeItem(USER_SESSION_KEY)
    }
  },
})
export function loginUser(user: UserSession) {
  userSession.value = user
}
export function logoutUser() {
  userSession.value = null
}
window.addEventListener('storage', () => {
  userRef.value = getUserFromStorage()
})
// --- Fine gestione sessione utente ---

const app = createApp(App)

app.use(router)
app.use(PrimeVue, {
    theme: {
        preset: Aura
    }
});

app.mount('#app')
