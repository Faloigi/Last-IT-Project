<script setup lang="tsx">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Registrazione
const regUsername = ref('')
const regEmail = ref('')
const regPassword = ref('')
const regError = ref('')
const regSuccess = ref('')

// Login
const loginUsername = ref('')
const loginPassword = ref('')
const loginError = ref('')

// Gestione sessione utente
type UserSession = { ute_id: number, ute_username: string, ute_ruolo: string }
function setUserSession(user: UserSession) {
  localStorage.setItem('user', JSON.stringify(user))
}

// Registrazione
async function handleRegister() {
  regError.value = ''
  regSuccess.value = ''
  if (!regUsername.value || !regEmail.value || !regPassword.value) {
    regError.value = 'Tutti i campi sono obbligatori'
    return
  }
  const res = await fetch('http://localhost/BigBlackDeath/backend/Utenti/registrazione.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username: regUsername.value, email: regEmail.value, password: regPassword.value })
  })
  const data = await res.json()
  if (data.success) {
    regSuccess.value = 'Registrazione avvenuta! Ora puoi accedere.'
    // Reset campi
    regUsername.value = ''
    regEmail.value = ''
    regPassword.value = ''
  } else {
    regError.value = data.message || 'Errore nella registrazione'
  }
}

// Login
async function handleLogin() {
  loginError.value = ''
  if (!loginUsername.value || !loginPassword.value) {
    loginError.value = 'Tutti i campi sono obbligatori'
    return
  }
  const res = await fetch('http://localhost/BigBlackDeath/backend/Utenti/login.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username: loginUsername.value, password: loginPassword.value })
  })
  const data = await res.json()
  if (data.success) {
    setUserSession(data.user)
    router.push('/')
  } else {
    loginError.value = data.message || 'Credenziali non valide'
  }
}
</script>

<template>
  <div class="auth-container">
    <div class="auth-card">
      <h2 class="auth-title">Registrati</h2>
      <input class="auth-input" type="text" placeholder="Username" v-model="regUsername" />
      <input class="auth-input" type="email" placeholder="Email" v-model="regEmail" />
      <input class="auth-input" type="password" placeholder="Password" v-model="regPassword" />
      <div v-if="regError" style="color:#ff4d4d; margin-bottom:1rem;">{{ regError }}</div>
      <div v-if="regSuccess" style="color:#19d86b; margin-bottom:1rem;">{{ regSuccess }}</div>
      <div class="spacer"></div>
      <button class="auth-btn" @click="handleRegister">Registrati</button>
    </div>
    <div class="divider">o</div>
    <div class="auth-card">
      <h2 class="auth-title">Login</h2>
      <input class="auth-input" type="text" placeholder="Username" v-model="loginUsername" />
      <input class="auth-input" type="password" placeholder="Password" v-model="loginPassword" />
      <div v-if="loginError" style="color:#ff4d4d; margin-bottom:1rem;">{{ loginError }}</div>
      <div class="spacer"></div>
      <button class="auth-btn" @click="handleLogin">Accedi</button>
    </div>
  </div>
</template>

<style scoped>
.auth-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #02190f;
  gap: 2rem;
}
.auth-card {
  background: #323232;
  border-radius: 70px 70px 70px 70px;
  padding: 2.5rem 3.5rem 2.5rem 3.5rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  width: 520px;
  min-width: 520px;
  max-width: 520px;
  height: 600px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.18);
}
.auth-title {
  color: #fff;
  font-size: 2.5rem;
  font-weight: bold;
  margin-bottom: 2.2rem;
  letter-spacing: 1px;
  text-align: center;
  font-family: 'Montserrat', Arial, sans-serif;
}
.auth-input {
  width: 80%;
  padding: 0.7rem 1.2rem;
  margin-bottom: 1.2rem;
  margin-left: auto;
  margin-right: auto;
  display: block;
  border: none;
  border-radius: 18px;
  background: #05442c;
  color: #fff;
  font-size: 2rem;
  font-family: 'Montserrat', Arial, sans-serif;
  outline: none;
  transition: background 0.2s;
}
.auth-input::placeholder {
  color: #b6e2c6;
  opacity: 1;
  font-size: 2rem;
  letter-spacing: 1px;
}
.auth-btn {
  background: #19d86b;
  color: #fff;
  border: none;
  border-radius: 16px;
  padding: 0.6rem 2.5rem;
  font-size: 2.2rem;
  font-family: 'Montserrat', Arial, sans-serif;
  font-weight: 500;
  cursor: pointer;
  margin-top: 2.2rem;
  margin-bottom: 0.5rem;
  margin-left: auto;
  margin-right: auto;
  display: block;
  width: 80%;
  transition: background 0.2s, color 0.2s;
  box-shadow: 0 2px 8px rgba(25,216,107,0.08);
}
.auth-btn:hover {
  background: #13b85a;
  color: #eafff3;
}
.divider {
  color: #eafff3;
  font-size: 2rem;
  font-weight: 500;
  margin: 0 1.5rem;
  align-self: center;
}
@media (max-width: 900px) {
  .auth-container {
    flex-direction: column;
    gap: 1.5rem;
  }
  .divider {
    margin: 1.5rem 0;
  }
}
</style>
