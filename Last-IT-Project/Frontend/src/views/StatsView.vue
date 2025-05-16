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
          <option value="" disabled selected hidden>Classe</option>
        </select>
        <select v-model="mappa">
          <option value="" disabled selected hidden>Mappe</option>
        </select>
        <select v-model="rank">
          <option value="" disabled selected hidden>Rank</option>
        </select>
      </template>
      <template v-else-if="activeTab === 'player'">
        <select v-model="modalita">
          <option value="" disabled selected hidden>Modalità</option>
        </select>
        <select v-model="mappa">
          <option value="" disabled selected hidden>Mappe</option>
        </select>
        <select v-model="rank">
          <option value="" disabled selected hidden>Rank</option>
        </select>
      </template>
    </div>
    <div class="table-container">
      <DataTable v-if="activeTab === 'eroi'" :value="eroi">
        <Column field="posizione" header="Posizione" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.posizione }}
          </template>
        </Column>
        <Column field="eroe" header="Eroe" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.eroe }}
          </template>
        </Column>
        <Column field="kd" header="KD" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.kd }}
          </template>
        </Column>
        <Column field="vittorie" header="%Vittorie" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.vittorie }}
          </template>
        </Column>
        <Column field="scelta" header="%Scelta" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.scelta }}
          </template>
        </Column>
        <Column field="partite" header="Partite" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.partite }}
          </template>
        </Column>
      </DataTable>
      <DataTable v-else-if="activeTab === 'player'" :value="players">
        <Column field="posizione" header="Posizione" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.index + 1 }}
          </template>
        </Column>
        <Column field="player" header="Player" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.player }}
          </template>
        </Column>
        <Column field="kd" header="KD" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.kd }}
          </template>
        </Column>
        <Column field="vittorie" header="%Vittorie" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.vittorie }}
          </template>
        </Column>
        <Column field="danni" header="%Danni" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.danni }}
          </template>
        </Column>
        <Column field="partite" header="Partite" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.partite }}
          </template>
        </Column>
      </DataTable>
      <DataTable v-else :value="clans">
        <Column field="posizione" header="Posizione" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.index + 1 }}
          </template>
        </Column>
        <Column field="clan" header="Clan" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.clan }}
          </template>
        </Column>
        <Column field="punti" header="Punti" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.punti }}
          </template>
        </Column>
        <Column field="vittorie" header="%Vittorie" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.vittorie }}
          </template>
        </Column>
        <Column field="sconfitte" header="%Sconfitte" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.sconfitte }}
          </template>
        </Column>
        <Column field="partite" header="Partite" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.partite }}
          </template>
        </Column>
      </DataTable>
    </div>
  </div>
</template>

<script setup>
import { Column, DataTable } from 'primevue'
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

/* Stile coerente anche per PrimeVue DataTable */
::v-deep(.p-datatable) {
  width: 100%;
  background: #0d2b1a;
  border-radius: 0.5rem;
  border: none;
  color: #fff;
}
::v-deep(.p-datatable-thead > tr > th) {
  background: #09351e;
  color: #fff;
  padding: 0.7rem 1rem;
  border: 1px solid #1affb2;
  font-weight: bold;
}
::v-deep(.p-datatable-tbody > tr > td) {
  background: #0d2b1a;
  color: #fff;
  padding: 0.7rem 1rem;
  border: 1px solid #1affb2;
}
::v-deep(.p-datatable-tbody > tr:hover) {
  background: #145c3a;
}
</style>
