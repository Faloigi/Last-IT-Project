<template>
  <div class="eroi-container">
    <h1>Eroi</h1>
    <div class="filters">
      <input v-model="search" placeholder="Cerca Player/Clan/Eroe..." />
      <Dropdown v-model="classe" :options="classeOptionsDropdown" optionLabel="name" optionValue="value" placeholder="Tutte le classi" class="p-dropdown-green" />
      <Dropdown v-model="difficolta" :options="difficoltaOptionsDropdown" optionLabel="name" optionValue="value" placeholder="Tutte le difficoltà" class="p-dropdown-green" />
    </div>
    <div class="eroi-grid">
      <router-link
        v-for="eroe in filteredEroi"
        :key="eroe.id || eroe.ero_id"
        :to="`/eroe/${eroe.eroe}`"
        class="eroe-card-link"
      >
        <div class="eroe-card">
          <div class="eroe-card-header">{{ eroe.eroe }}</div>
          <div class="eroe-card-img">
            <img v-if="eroe.img" :src="eroe.img" :alt="eroe.eroe" />
            <span v-else>Immagine</span>
          </div>
          <div class="eroe-card-info">
            <div class="eroe-card-row"><b>Classe:</b> {{ eroe.classe || '-' }}</div>
            <div class="eroe-card-row"><b>Difficoltà:</b> {{ eroe.difficolta || '-' }}</div>
            <div class="eroe-card-row"><b>Partite:</b> {{ eroe.partite_giocate || eroe.partite || '-' }}</div>
            <div class="eroe-card-row"><b>% Vittorie:</b> {{ eroe.vittorie ? eroe.vittorie + '%' : '-' }}</div>
          </div>
        </div>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import Dropdown from 'primevue/dropdown'

const search = ref('')
const classe = ref('')
const difficolta = ref('')

const eroi = ref([])

// Classi e difficoltà dinamiche dai dati
const classiDisponibili = computed(() => {
  const set = new Set()
  eroi.value.forEach(e => e.classe && set.add(e.classe))
  return Array.from(set)
})
const difficoltaDisponibili = computed(() => {
  const set = new Set()
  eroi.value.forEach(e => e.difficolta && set.add(e.difficolta))
  return Array.from(set)
})

const classeOptionsDropdown = computed(() => [{ name: 'Tutte le classi', value: '' }, ...classiDisponibili.value.map(c => ({ name: c, value: c }))])
const difficoltaOptionsDropdown = computed(() => [{ name: 'Tutte le difficoltà', value: '' }, ...difficoltaDisponibili.value.map(d => ({ name: d, value: d }))])

// Fetch dati eroi da PHP
const fetchEroi = async () => {
  try {
    const res = await fetch('http://localhost/BigBlackDeath/backend/Hero/getStatsEroe.php')
    eroi.value = await res.json()
  } catch (e) {
    eroi.value = []
  }
}

onMounted(() => {
  fetchEroi()
})

// Filtri
const filteredEroi = computed(() =>
  eroi.value.filter(eroe =>
    (!search.value || eroe.eroe.toLowerCase().includes(search.value.toLowerCase())) &&
    (!classe.value || eroe.classe === classe.value) &&
    (!difficolta.value || eroe.difficolta === difficolta.value)
  )
)
</script>

<style scoped>
.eroi-container {
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
.filters {
  margin-bottom: 2rem;
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
}
.filters input, .filters select {
  padding: 0.6rem 1.2rem;
  border-radius: 10px;
  border: none;
  background: #09351e;
  color: #fff;
  font-size: 1.1rem;
}
.eroi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 2.5rem;
}
.eroe-card {
  background: #09351e;
  border-radius: 2rem;
  box-shadow: 0 2px 12px #0005;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-height: 320px;
  transition: transform 0.15s;
  cursor: pointer;
}
.eroe-card:hover {
  transform: translateY(-8px) scale(1.03);
  box-shadow: 0 6px 24px #0007;
}
.eroe-card-header {
  width: 100%;
  background: #06341d;
  border-radius: 2rem 2rem 0 0;
  text-align: center;
  font-size: 2rem;
  font-weight: bold;
  padding: 1.2rem 0 1rem 0;
  margin-bottom: 1.5rem;
}
.eroe-card-img {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
}
.eroe-card-img img {
  max-width: 80%;
  max-height: 180px;
  border-radius: 1.2rem;
  object-fit: contain;
  background: #0d2b1a;
}
.eroe-card-img span {
  font-size: 2rem;
  color: #fff;
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
.eroe-card-link {
  text-decoration: none;
  color: inherit;
  transition: filter 0.15s;
}
.eroe-card-link:hover {
  filter: brightness(1.15) drop-shadow(0 0 8px #19d86b88);
}
.eroe-card-info {
  width: 100%;
  padding: 1rem 1.2rem 1.2rem 1.2rem;
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  font-size: 1.1rem;
  color: #b6e2c6;
}
.eroe-card-row {
  margin-bottom: 0.2rem;
}
</style>
