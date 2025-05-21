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
        <Dropdown v-model="classe" :options="classeOptions" optionLabel="name" optionValue="value" placeholder="Classe" class="p-dropdown-green" />
        <Dropdown v-model="mappa" :options="mappaOptions" optionLabel="name" optionValue="value" placeholder="Mappe" class="p-dropdown-green" />
        <Dropdown v-model="rank" :options="rankOptions" optionLabel="name" optionValue="value" placeholder="Rank" class="p-dropdown-green" />
      </template>
      <template v-else-if="activeTab === 'player'">
        <Dropdown v-model="modalita" :options="modalitaOptions" optionLabel="name" optionValue="value" placeholder="Modalità" class="p-dropdown-green" />
        <Dropdown v-model="mappa" :options="mappaOptions" optionLabel="name" optionValue="value" placeholder="Mappe" class="p-dropdown-green" />
        <Dropdown v-model="rank" :options="rankOptions" optionLabel="name" optionValue="value" placeholder="Rank" class="p-dropdown-green" />
      </template>
    </div>
    <div class="table-container">
      <DataTable v-if="activeTab === 'eroi'" :value="filteredEroi">
        <Column field="posizione" header="Posizione" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.index + 1 }}
          </template>
        </Column>
        <Column field="eroe" header="Eroe" :sortable="true">
          <template #body="slotProps">
            <router-link :to="`/eroe/${slotProps.data.eroe}`">{{ slotProps.data.eroe }}</router-link>
          </template>
        </Column>
        <Column field="classe" header="Classe" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.classe }}
          </template>
        </Column>
        <Column field="difficolta" header="Difficoltà" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.difficolta }}
          </template>
        </Column>
        <Column field="kd" header="KD" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.kd }}
          </template>
        </Column>
        <Column field="vittorie" header="%Vittorie" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.vittorie }}%
          </template>
        </Column>
        <Column field="scelta" header="%Scelta" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.scelta }}%
          </template>
        </Column>
        <Column field="partite" header="Partite" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.partite }}
          </template>
        </Column>
      </DataTable>
      <DataTable v-else-if="activeTab === 'player'" :value="filteredPlayers">
        <Column field="posizione" header="Posizione">
          <template #body="slotProps">
            {{ slotProps.index + 1 }}
          </template>
        </Column>
        <Column field="username" header="Player" :sortable="true">
          <template #body="slotProps">
            <router-link :to="`/player/${slotProps.data.username}`">{{ slotProps.data.username }}</router-link>
          </template>
        </Column>
        <Column field="kd" header="KD" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.kd !== undefined ? Number(slotProps.data.kd).toFixed(2) : '-' }}
          </template>
        </Column>
        <Column field="winrate" header="%Vittorie" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.winrate !== undefined ? Number(slotProps.data.winrate).toFixed(1) + '%' : '-' }}
          </template>
        </Column>
        <Column field="danni" header="Danni" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.danni !== undefined ? Number(slotProps.data.danni).toFixed(2) : '-' }}
          </template>
        </Column>
        <Column field="partite_giocate" header="Partite" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.partite_giocate }}
          </template>
        </Column>
      </DataTable>
      <DataTable v-else :value="clans">
        <Column field="posizione" header="Posizione">
          <template #body="slotProps">
            {{ slotProps.index + 1 }}
          </template>
        </Column>
        <Column field="nome" header="Clan" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.nome }}
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
        <Column field="partecipanti" header="Partecipanti" :sortable="true">
          <template #body="slotProps">
            {{ slotProps.data.partecipanti }}
          </template>
        </Column>
      </DataTable>
    </div>
  </div>
</template>

<script setup>
import { Column, DataTable } from 'primevue'
import Dropdown from 'primevue/dropdown'
import { ref, computed, onMounted, watch } from 'vue'

const activeTab = ref('eroi')

const eroi = ref([])
const players = ref([])
const clans = ref([])
const mappe = ref([])
const ranks = ref([])

const classe = ref(null)
const mappa = ref('')
const rank = ref('')
const modalita = ref('')
const search = ref('')

const modalitaList = ref([])

const modalitaDisponibili = computed(() => {
  const set = new Set()
  if (players.value) {
    players.value.forEach(p => p.modalita && set.add(p.modalita))
  }
  return Array.from(set)
})
const mappaDisponibili = computed(() => {
  const set = new Set()
  if (players.value) {
    players.value.forEach(p => p.mappa && set.add(p.mappa))
  }
  return Array.from(set)
})
const rankDisponibili = computed(() => {
  const set = new Set()
  if (players.value) {
    players.value.forEach(p => p.rank && set.add(p.rank))
  }
  return Array.from(set)
})
const modalitaOptions = computed(() => {
  const options = modalitaList.value.map(m => ({ name: m.mod_nome, value: m.mod_nome }))
  options.unshift({ name: 'Tutte le modalità', value: '' })
  return options
})
const mappaOptions = computed(() => {
  const options = mappe.value.map(m => ({ name: m.map_nome, value: m.map_nome }))
  options.unshift({ name: 'Tutte le mappe', value: '' })
  return options
})
const rankOptions = computed(() => {
  const options = ranks.value.map(r => ({ name: r.ran_nome, value: r.ran_nome }))
  options.unshift({ name: 'Tutti i rank', value: '' })
  return options
})

const filteredPlayers = computed(() => {
  let filtered = players.value
  if (activeTab.value === 'player' && search.value) filtered = filtered.filter(p => p.username?.toLowerCase().includes(search.value.toLowerCase()))
  return filtered
})

const classeOptions = ref([{ name: 'Tutte le classi', value: '' }])

async function getStatsPlayer(){
  const res = await fetch('http://localhost/BigBlackDeath/backend/Player/getStatsPlayer.php')
  players.value = await res.json()
}

async function getStatsClan(){
  const res = await fetch('http://localhost/BigBlackDeath/backend/Clan/getStatsClan.php')
  clans.value = await res.json()
}

async function fetchMappe() {
  const res = await fetch('http://localhost/BigBlackDeath/backend/Mappe/getMappe.php')
  mappe.value = await res.json()
}

async function fetchModalita() {
  const res = await fetch('http://localhost/BigBlackDeath/backend/Modalita/getModalita.php')
  modalitaList.value = await res.json()
}

async function fetchRanks() {
  const res = await fetch('http://localhost/BigBlackDeath/backend/Ranks/getRanks.php')
  ranks.value = await res.json()
}

async function fetchClassi() {
  const res = await fetch('http://localhost/BigBlackDeath/backend/Hero/getClassi.php')
  const data = await res.json()
  classeOptions.value = [{ name: 'Tutte le classi', value: '' }, ...data.map(c => ({ name: c.cla_nome, value: c.cla_nome }))]
}

async function fetchFilteredPlayers() {
  if (activeTab.value !== 'player') return;
  const params = new URLSearchParams()
  if (modalita.value) params.append('modalita', modalita.value)
  if (mappa.value) params.append('mappa', mappa.value)
  if (rank.value) params.append('rank', rank.value)
  const res = await fetch(`http://localhost/BigBlackDeath/backend/Player/getFilteredStatsPlayer.php?${params.toString()}`)
  filteredPlayers.value = await res.json()
}

async function fetchStatsHero() {
  const params = new URLSearchParams()
  if (classe.value) params.append('classe', classe.value)
  if (mappa.value) params.append('mappa', mappa.value)
  if (rank.value) params.append('rank', rank.value)
  // NB: questi filtri sono placeholder, l'endpoint attuale non li supporta ancora
  const res = await fetch(`http://localhost/BigBlackDeath/backend/Hero/getStatsHero.php?${params.toString()}`)
  eroi.value = await res.json()
}

watch([classe, mappa, rank, activeTab], () => {
  if (activeTab.value === 'eroi') fetchStatsHero()
  if (activeTab.value === 'player') fetchFilteredPlayers()
}, { immediate: true })

const setTab = (tab) => {
  activeTab.value = tab
  // Reset filtri quando cambio tab
  search.value = ''
  modalita.value = ''
  mappa.value = ''
  rank.value = ''
  classe.value = null
}

onMounted(() => {
  getStatsPlayer()
  getStatsClan()
  fetchMappe()
  fetchModalita()
  fetchRanks()
  fetchStatsHero()
  fetchClassi()
})

// Rendo la searchbar contestuale
const filteredEroi = computed(() => {
  let filtered = eroi.value
  if (classe.value) filtered = filtered.filter(e => e.classe === classe.value)
  if (mappa.value) filtered = filtered.filter(e => e.mappa === mappa.value)
  if (rank.value) filtered = filtered.filter(e => e.rank === rank.value)
  if (activeTab.value === 'eroi' && search.value) filtered = filtered.filter(e => e.eroe?.toLowerCase().includes(search.value.toLowerCase()))
  if (classe.value || mappa.value || rank.value) {
    filtered = [...filtered].sort((a, b) => b.vittorie - a.vittorie)
  } else {
    filtered = [...filtered].sort((a, b) => b.partite - a.partite)
  }
  return filtered
})

const filteredClans = computed(() => {
  let filtered = clans.value
  if (activeTab.value === 'clan' && search.value) filtered = filtered.filter(c => c.nome?.toLowerCase().includes(search.value.toLowerCase()))
  return filtered
})

const activeTabLabel = computed(() => {
  if (activeTab.value === 'eroi') return 'Eroi'
  if (activeTab.value === 'player') return 'Player'
  return 'Clan'
})

// Personalizza il messaggio "No available options"
const emptyMessageTemplate = (options) => {
  return {
    props: options,
    template: `<div class="custom-empty-message">No available options</div>`
  }
}
</script>

<style scoped>
.stats-container { padding: 2rem; color: #fff; background: #071b13; min-height: 100vh; padding-top: 80px; }
.tabs { margin-bottom: 1rem; }
.tabs button { margin-right: 1rem; background: none; border: none; color: #fff; font-size: 1.2rem; border-bottom: 2px solid transparent; cursor: pointer; }
.tabs button.active { border-bottom: 2px solid #1affb2; }
.filters {
  margin-bottom: 1rem;
  display: flex;
  gap: 1rem;
}
.filters input, .filters select, .filters .p-dropdown {
  margin-right: 0;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
  border: none;
  background: #09351e;
  color: #fff;
}
::v-deep(.filters .p-dropdown .p-dropdown-label) {
  color: #fff;
}
::v-deep(.filters .p-dropdown .p-dropdown-label.p-placeholder) {
  color: #b6e2c6;
  opacity: 1;
}
::v-deep(.filters .p-dropdown) {
  background: #09351e !important;
  border: none !important;
}
::v-deep(.filters .p-dropdown .p-dropdown-trigger) {
  color: #fff;
}
::v-deep(.filters .p-dropdown-panel) {
  background: #09351e;
  color: #fff;
}
::v-deep(.filters .p-dropdown-item) {
  color: #fff;
}
::v-deep(.filters .p-dropdown-item.p-highlight) {
  background: #145c3a;
  color: #1affb2;
}
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

::v-deep(.p-dropdown) {
  background: #09351e !important;
  border: none !important;
  color: #fff !important;
  margin-right: 0;
  padding: 0;
  border-radius: 6px;
}

::v-deep(.p-dropdown-panel) {
  background-color: #09351e !important;
  border-color: #1affb2 !important;
}

::v-deep(.p-dropdown-items) {
  background-color: #09351e !important;
}

::v-deep(.p-dropdown-empty-message) {
  background-color: #09351e !important;
  color: white !important;
}

::v-deep(.custom-empty-message) {
  background-color: #09351e !important;
  color: white !important;
  padding: 0.5rem;
}

/* Override di elementi specifici del panel */
:deep(.green-dropdown-panel) {
  background-color: #09351e !important;
  color: white !important;
}

:deep(.green-dropdown-panel *) {
  background-color: #09351e !important;
  color: white !important;
}

.p-dropdown-green {
  width: 220px;
  max-width: 100%;
  padding: 0;
  border-radius: 10px;
  border: none;
  background: #09351e;
  color: #fff;
  font-size: 1.1rem;
}
:deep(.p-dropdown-green .p-dropdown-label) {
  padding: 0.7rem 1.2rem;
  color: #fff;
  background: #09351e;
}
:deep(.p-dropdown-green .p-dropdown-trigger) {
  background: #09351e;
  color: #fff;
}
:deep(.p-dropdown-panel .p-dropdown-items-wrapper) {
  background: #09351e;
}
:deep(.p-dropdown-panel .p-dropdown-item) {
  color: #fff;
  background: #09351e;
}
:deep(.p-dropdown-panel .p-dropdown-item:hover) {
  background: #145c3a;
}
:deep(.p-dropdown-panel .p-dropdown-empty-message) {
  background: #09351e;
  color: #fff;
}
</style>
