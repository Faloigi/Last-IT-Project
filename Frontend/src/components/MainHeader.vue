<script setup lang="tsx">
import { RouterLink, useRouter } from 'vue-router';
import { userSession, logoutUser } from '@/main';
import { ref } from 'vue'

const router = useRouter();
const showMenu = ref(false)

function handleLogout() {
  showMenu.value = false
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
      <RouterLink to="/clans" class="nav-btn">Clan</RouterLink>
      <template v-if="userSession">
        <div class="user-menu-wrapper" tabindex="0" @click="showMenu = !showMenu" @blur="showMenu = false">
          <svg class="user-icon" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 16-4 16 0" /></svg>
          <div v-if="showMenu" class="user-dropdown">
            <div class="user-dropdown-info">
              <b>{{ userSession.ute_username }}</b>
              <span class="user-role">[{{ userSession.ute_ruolo }}]</span>
            </div>
            <button class="dropdown-logout" @click.stop="handleLogout">Logout</button>
          </div>
        </div>
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
  align-items: center;
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
.user-menu-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  cursor: pointer;
  outline: none;
}
.user-icon {
  width: 38px;
  height: 38px;
  fill: #19d86b;
  background: #09351e;
  border-radius: 50%;
  padding: 6px;
  transition: background 0.2s;
}
.user-menu-wrapper:focus .user-icon,
.user-menu-wrapper:hover .user-icon {
  background: #19d86b22;
}
.user-dropdown {
  position: absolute;
  right: 0;
  top: 48px;
  background: #09351e;
  border-radius: 12px;
  box-shadow: 0 2px 12px #0005;
  min-width: 180px;
  padding: 1rem 1.2rem;
  z-index: 1000;
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
  animation: fadeIn 0.18s;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px);}
  to { opacity: 1; transform: translateY(0);}
}
.user-dropdown-info {
  color: #fff;
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}
.user-role {
  color: #19d86b;
  font-size: 1rem;
  margin-left: 0.2rem;
}
.dropdown-logout {
  background: #19d86b;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 0.5rem 1rem;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
}
.dropdown-logout:hover {
  background: #13b85a;
}
</style>
