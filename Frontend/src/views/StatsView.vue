<template>
  <div class="stats-container">
    <h1>STATISTICHE ({{ activeTabLabel }})</h1>
    <div class="tabs">
      <button :class="{active: activeTab === 'eroi'}" @click="setTab('eroi')">Eroi</button>
      <button :class="{active: activeTab === 'player'}" @click="setTab('player')">Players</button>
      <button :class="{active: activeTab === 'clan'}" @click="setTab('clan')">Clans</button>
    </div>
    <div class="filters">
      <input v-model="search" placeholder="Cerca Player/Clan/Eroe..." />
      <template v-if="activeTab === 'eroi'">
        <select v-model="classe">
          <option value="" disabled selected hidden>Classe (Combo)</option>
        </select>
        <select v-model="mappa">
          <option value="" disabled selected hidden>Mappe (Combo)</option>
        </select>
        <select v-model="rank">
          <option value="" disabled selected hidden>Rank (Se Competitiva)</option>
        </select>
      </template>
      <template v-else-if="activeTab === 'player'">
        <select v-model="modalita">
          <option value="" disabled selected hidden>Modalità (Combo)</option>
        </select>
        <select v-model="mappa">
          <option value="" disabled selected hidden>Mappe (Combo)</option>
        </select>
        <select v-model="rank">
          <option value="" disabled selected hidden>Rank (Se Competitiva)</option>
        </select>
      </template>
    </div>
    <div class="table-container">
      <table v-if="activeTab === 'eroi'">
        <thead>
          <tr>
            <th>Posizione</th><th>Eroe</th><th>KD</th><th>%Vittorie</th><th>%Scelta</th><th>Partite</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, i) in filteredEroi" :key="row.id">
            <td>{{ i+1 }}</td>
            <td>{{ row.eroe }}</td>
            <td>{{ row.kd }}</td>
            <td>{{ row.vittorie }}</td>
            <td>{{ row.scelta }}</td>
            <td>{{ row.partite }}</td>
          </tr>
        </tbody>
      </table>
      <table v-else-if="activeTab === 'player'">
        <thead>
          <tr>
            <th>Posizione</th><th>Player</th><th>KD</th><th>%Vittorie</th><th>%Danni</th><th>Partite</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, i) in filteredPlayers" :key="row.id">
            <td>{{ i+1 }}</td>
            <td>{{ row.player }}</td>
            <td>{{ row.kd }}</td>
            <td>{{ row.vittorie }}</td>
            <td>{{ row.danni }}</td>
            <td>{{ row.partite }}</td>
          </tr>
        </tbody>
      </table>
      <table v-else>
        <thead>
          <tr>
            <th>Posizione</th><th>Clan</th><th>Punti</th><th>%Vittorie</th><th>%Sconfitte</th><th>Partite</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, i) in filteredClans" :key="row.id">
            <td>{{ i+1 }}</td>
            <td>{{ row.clan }}</td>
            <td>{{ row.punti }}</td>
            <td>{{ row.vittorie }}</td>
            <td>{{ row.sconfitte }}</td>
            <td>{{ row.partite }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const activeTab = ref('eroi')
const search = ref('')
const classe = ref('')
const mappa = ref('')
const rank = ref('')
const modalita = ref('')

const eroi = ref([])
const players = ref([])
const clans = ref([])

const setTab = (tab) => { activeTab.value = tab }

// Esempio di fetch dati da PHP
const fetchEroi = async () => {
  try {
    const res = await fetch('/api/stats_eroi.php')
    eroi.value = await res.json()
  } catch (e) { eroi.value = [] }
}
const fetchPlayers = async () => {
  try {
    const res = await fetch('/api/stats_player.php')
    players.value = await res.json()
  } catch (e) { players.value = [] }
}
const fetchClans = async () => {
  try {
    const res = await fetch('/api/stats_clan.php')
    clans.value = await res.json()
  } catch (e) { clans.value = [] }
}

onMounted(() => {
  fetchEroi()
  fetchPlayers()
  fetchClans()
})

// Filtri (esempio base, puoi aggiungere altri filtri)
const filteredEroi = computed(() =>
  eroi.value.filter(e => e.eroe?.toLowerCase().includes(search.value.toLowerCase()))
)
const filteredPlayers = computed(() =>
  players.value.filter(p => p.player?.toLowerCase().includes(search.value.toLowerCase()))
)
const filteredClans = computed(() =>
  clans.value.filter(c => c.clan?.toLowerCase().includes(search.value.toLowerCase()))
)

const activeTabLabel = computed(() => {
  if (activeTab.value === 'eroi') return 'Eroi'
  if (activeTab.value === 'player') return 'Player'
  return 'Clan'
})
</script>

<style scoped>
.stats-container { padding: 2rem; color: #fff; background: #071b13; min-height: 100vh; padding-top: 80px; }
.tabs { margin-bottom: 1rem; }
.tabs button { margin-right: 1rem; background: none; border: none; color: #fff; font-size: 1.2rem; border-bottom: 2px solid transparent; cursor: pointer; }
.tabs button.active { border-bottom: 2px solid #1affb2; }
.filters { margin-bottom: 1rem; }
.filters input, .filters select { margin-right: 1rem; padding: 0.4rem 0.8rem; border-radius: 6px; border: none; background: #09351e; color: #fff; }
.table-container { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; background: #0d2b1a; }
th, td { padding: 0.7rem 1rem; border: 1px solid #1affb2; }
th { background: #09351e; }
</style>
