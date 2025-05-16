<template>
  <div class="player-container">
    <h1>Player</h1>
    <div class="player-layout">
      <!-- Colonna sinistra -->
      <div class="player-sidebar">
        <button class="rank-btn">{{ playerData.rank || 'Rank' }}</button>
        <div class="heroes-played">
          <div class="sidebar-title">Eroi Giocati</div>
          <div v-for="hero in playerData.heroesPlayed" :key="hero.id" class="hero-played-card">
            <span>{{ hero.nome }}</span>
          </div>
        </div>
      </div>
      <!-- Colonna centrale -->
      <div class="player-main">
        <div class="main-title">Partite Giocate</div>
        <div v-for="match in filteredMatches" :key="match.id" class="match-card">
          <div class="match-title">{{ match.nome || 'PARTITA' }}</div>
          <!-- Puoi aggiungere dettagli della partita qui -->
        </div>
      </div>
      <!-- Colonna destra -->
      <div class="player-filters">
        <input v-model="searchHero" placeholder="Cerca per Eroe" />
        <select v-model="modalita">
          <option value="">Tutte le modalità</option>
          <option v-for="m in modalitaDisponibili" :key="m" :value="m">{{ m }}</option>
        </select>
        <select v-model="mappa">
          <option value="">Tutte le mappe</option>
          <option v-for="m in mappeDisponibili" :key="m" :value="m">{{ m }}</option>
        </select>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const playerData = ref({
  rank: '',
  heroesPlayed: [],
  matches: []
})

const searchHero = ref('')
const modalita = ref('')
const mappa = ref('')

// Popola dinamicamente le modalità e le mappe disponibili
const modalitaDisponibili = computed(() => {
  const set = new Set()
  playerData.value.matches.forEach(m => m.modalita && set.add(m.modalita))
  return Array.from(set)
})
const mappeDisponibili = computed(() => {
  const set = new Set()
  playerData.value.matches.forEach(m => m.mappa && set.add(m.mappa))
  return Array.from(set)
})

// Fetch dati player da PHP
const fetchPlayerData = async () => {
  try {
    const res = await fetch('/api/player.php')
    playerData.value = await res.json()
  } catch (e) {
    playerData.value = { rank: '', heroesPlayed: [], matches: [] }
  }
}

onMounted(() => {
  fetchPlayerData()
})

// Filtra le partite in base ai filtri selezionati
const filteredMatches = computed(() =>
  playerData.value.matches.filter(match =>
    (!searchHero.value || match.eroe?.toLowerCase().includes(searchHero.value.toLowerCase())) &&
    (!modalita.value || match.modalita === modalita.value) &&
    (!mappa.value || match.mappa === mappa.value)
  )
)
</script>

<style scoped>
.player-container {
  padding: 2rem;
  color: #fff;
  background: #071b13;
  min-height: 100vh;
  padding-top: 80px;
}
h1 {
  font-size: 2.5rem;
  margin-bottom: 1.5rem;
}
.player-layout {
  display: flex;
  gap: 2.5rem;
  align-items: flex-start;
}
.player-sidebar {
  min-width: 220px;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}
.rank-btn {
  background: #09351e;
  color: #fff;
  font-size: 1.5rem;
  border: none;
  border-radius: 12px;
  padding: 1rem 2.5rem;
  font-weight: bold;
  margin-bottom: 1rem;
  cursor: pointer;
}
.heroes-played {
  background: #09351e;
  border-radius: 10px;
  padding: 1rem 0.5rem;
}
.sidebar-title {
  font-size: 1.3rem;
  font-weight: bold;
  margin-bottom: 1rem;
  text-align: center;
}
.hero-played-card {
  background: #0d2b1a;
  border-radius: 8px;
  margin: 0.5rem 0;
  padding: 0.7rem 1rem;
  text-align: center;
  font-size: 1.1rem;
}
.player-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}
.main-title {
  background: #09351e;
  border-radius: 10px 10px 0 0;
  font-size: 2.3rem;
  font-weight: bold;
  text-align: center;
  padding: 1.2rem 0;
  margin-bottom: 1.2rem;
}
.match-card {
  background: #09351e;
  border-radius: 12px;
  padding: 2.2rem 0;
  margin-bottom: 1.2rem;
  text-align: center;
  font-size: 2.5rem;
  font-weight: bold;
  min-height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.player-filters {
  min-width: 220px;
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}
.player-filters input, .player-filters select {
  padding: 0.7rem 1.2rem;
  border-radius: 10px;
  border: none;
  background: #09351e;
  color: #fff;
  font-size: 1.1rem;
}
@media (max-width: 1100px) {
  .player-layout {
    flex-direction: column;
    gap: 2rem;
  }
  .player-sidebar, .player-filters {
    min-width: unset;
    width: 100%;
    flex-direction: row;
    gap: 1rem;
    justify-content: center;
  }
  .player-main {
    width: 100%;
  }
}
</style>
