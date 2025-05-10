<script setup lang="tsx">
import { ref } from 'vue';
import InputText from 'primevue/inputtext';
import Card from 'primevue/card';

// Variabili reattive per i testi dinamici
const numPlayers = ref('Num Players'); // Sostituisci con dati reali in futuro
const numGames = ref('Num Games');     // Sostituisci con dati reali in futuro
const descrizione = ref('Descrizione'); // Sostituisci con dati reali in futuro
const search = ref('');
</script>

<template>
  <div class="home-container">
    <h1 class="main-title">
      <span class="hoverable">BIG</span>
      <span style="margin-left:0.7rem"></span>
      <span class="hoverable">BLACK</span>
      <span style="margin-left:0.7rem"></span>
      <span class="hoverable">DEATH</span>
      <span style="margin-left:0.7rem"></span>
      <span class="hoverable">STATS</span>
      <span style="margin-left:0.7rem"></span>
    </h1>
    <div class="searchbar-wrapper">
      <span class="p-input-icon-left searchbar-icon-wrapper">
        <InputText
          v-model="search"
          class="searchbar"
          placeholder="Cerca Player/Clan/Eroe..."
        />
      </span>
    </div>
    <div class="stats-row">
      <div class="stat-text">{{ numPlayers }}</div>
      <div class="stat-text">{{ numGames }}</div>
    </div>
    <Card class="descrizione-box">
      <template #content>
        <span>{{ descrizione }}</span>
      </template>
    </Card>
  </div>
</template>

<style scoped>
/* Rimosso l'import dei CSS PrimeVue e PrimeIcons, da importare globalmente in main.js/main.ts */
.home-container {
  min-height: 100vh;
  background: #02190f;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100vw;
  box-sizing: border-box;
}
.main-title {
  font-size: clamp(2.5rem, 6vw, 5rem);
  font-weight: 900;
  color: #fff;
  letter-spacing: 0.12em;
  text-align: center;
  margin-bottom: 2.5rem;
  font-family: 'Montserrat', 'Arial Black', Arial, sans-serif;
  text-shadow: 0 4px 24px #000a, 0 1px 0 #19d86b;
  line-height: 1.1;
  word-break: keep-all;
  white-space: pre-line;
  width: 100vw;
  max-width: 100vw;
  box-sizing: border-box;
}
.searchbar-wrapper {
  display: flex;
  justify-content: center;
  width: 100vw;
  margin-bottom: 2.5rem;
}
.searchbar {
  width: 100%;
  font-size: 1.3rem;
  background-color: #00371D;
}
.stats-row {
  display: flex;
  justify-content: center;
  gap: 8rem;
  width: 100vw;
  margin-bottom: 2.5rem;
}
.stat-text {
  color: #fff;
  font-size: 1.3rem;
  font-family: inherit;
  text-align: center;
}
.descrizione-box {
  width: 95vw;
  max-width: 1200px;
  min-height: 220px;
  background: #333;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  font-size: 1.3rem;
  margin-bottom: 2rem;
  box-shadow: 0 2px 12px #0005;
}
.hoverable {
  display: inline-block;
  transition: color 0.2s, transform 0.2s;
  cursor: pointer;
}
.hoverable:hover {
  color: #19d86b;
  transform: scale(1.13) rotate(-2deg);
}
</style>
