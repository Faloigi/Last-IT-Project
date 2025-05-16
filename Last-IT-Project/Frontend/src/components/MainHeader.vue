<script setup lang="tsx">
import { RouterLink } from 'vue-router';
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
      <RouterLink to="/player" class="nav-btn">Player</RouterLink>
      <RouterLink to="/stats" class="nav-btn">Statistiche</RouterLink>
      <RouterLink to="/heroes" class="nav-btn">Eroi</RouterLink>
      <RouterLink to="/login" class="nav-btn">Login</RouterLink>
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
</style>
