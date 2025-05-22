<script setup lang="tsx">
import { RouterLink, useRouter } from 'vue-router';
import { userSession, logoutUser } from '@/main';

const router = useRouter();

function handleLogout() {
  logoutUser();
  router.push('/login');
}
</script>

<template>
  <header class="main-header">
    <div class="logo-container">
      <!-- Sostituisci src con il percorso reale del logo se disponibile -->
      <RouterLink to="/">
        <img src="/Logo.png" alt="Logo" class="logo" />
      </RouterLink>
    </div>
    <nav class="nav-buttons">
      <RouterLink to="/stats" class="nav-btn">Statistiche</RouterLink>
      <RouterLink to="/eroi" class="nav-btn">Eroi</RouterLink>
      <template v-if="userSession">
        <span class="user-info">Ciao, {{ userSession.ute_username }} <span class="user-role">[{{ userSession.ute_ruolo }}]</span></span>
        <button class="nav-btn" @click="handleLogout">Logout</button>
      </template>
      <template v-else>
        <RouterLink to="/login" class="nav-btn">Login</RouterLink>
      </template>
    </nav>
  </header>
</template>

<style scoped>
.main-header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 60px;
  background: #022b1a;
  display: flex;
  align-items: center;
  justify-content: space-between;
  z-index: 100;
  padding: 0 2rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.logo-container {
  display: flex;
  align-items: center;
}
.logo {
  height: 48px;
  width: 48px;
  border-radius: 50%;
  object-fit: cover;
}
.nav-buttons {
  display: flex;
  gap: 17rem;
  margin: 0 auto;
}
.nav-btn {
  background: #19d86b;
  color: #0a2e1a;
  border: none;
  border-radius: 12px;
  padding: 0.5rem 1.5rem;
  font-size: 1.3rem;
  font-family: inherit;
  cursor: pointer;
  transition: background 0.2s;
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
}
.nav-btn:hover {
  background: #13b85a;
}
.user-info {
  color: #fff;
  font-size: 1.1rem;
  margin-right: 1.2rem;
  font-weight: 500;
  display: flex;
  align-items: center;
}
.user-role {
  color: #19d86b;
  margin-left: 0.3rem;
  font-size: 1.1rem;
}
</style>
