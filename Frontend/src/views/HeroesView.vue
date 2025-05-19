<template>
  <div class="heroes-container">
    <h1>Eroi</h1>
    <div class="filters">
      <input v-model="search" placeholder="Cerca Player/Clan/Eroe..." />
      <Dropdown v-model="classe" :options="classeOptionsDropdown" optionLabel="name" optionValue="value" placeholder="Tutte le classi" class="p-dropdown-green" />
      <Dropdown v-model="difficolta" :options="difficoltaOptionsDropdown" optionLabel="name" optionValue="value" placeholder="Tutte le difficoltà" class="p-dropdown-green" />
    </div>
    <div class="heroes-grid">
      <div v-for="hero in filteredHeroes" :key="hero.id" class="hero-card">
        <div class="hero-card-header">{{ hero.nome }}</div>
        <div class="hero-card-img">
          <img v-if="hero.img" :src="hero.img" :alt="hero.nome" />
          <span v-else>Immagine</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import Dropdown from 'primevue/dropdown'

const search = ref('')
const classe = ref('')
const difficolta = ref('')

const heroes = ref([])

// Classi e difficoltà dinamiche dai dati
const classiDisponibili = computed(() => {
  const set = new Set()
  heroes.value.forEach(h => h.classe && set.add(h.classe))
  return Array.from(set)
})
const difficoltaDisponibili = computed(() => {
  const set = new Set()
  heroes.value.forEach(h => h.difficolta && set.add(h.difficolta))
  return Array.from(set)
})

const classeOptionsDropdown = computed(() => [{ name: 'Tutte le classi', value: '' }, ...classiDisponibili.value.map(c => ({ name: c, value: c }))])
const difficoltaOptionsDropdown = computed(() => [{ name: 'Tutte le difficoltà', value: '' }, ...difficoltaDisponibili.value.map(d => ({ name: d, value: d }))])

// Fetch dati eroi da PHP
const fetchHeroes = async () => {
  try {
    const res = await fetch('/api/heroes.php')
    heroes.value = await res.json()
  } catch (e) {
    heroes.value = []
  }
}

onMounted(() => {
  fetchHeroes()
})

// Filtri
const filteredHeroes = computed(() =>
  heroes.value.filter(hero =>
    (!search.value || hero.nome.toLowerCase().includes(search.value.toLowerCase())) &&
    (!classe.value || hero.classe === classe.value) &&
    (!difficolta.value || hero.difficolta === difficolta.value)
  )
)
</script>

<style scoped>
.heroes-container {
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
.heroes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 2.5rem;
}
.hero-card {
  background: #09351e;
  border-radius: 2rem;
  box-shadow: 0 2px 12px #0005;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-height: 320px;
  transition: transform 0.15s;
}
.hero-card:hover {
  transform: translateY(-8px) scale(1.03);
  box-shadow: 0 6px 24px #0007;
}
.hero-card-header {
  width: 100%;
  background: #06341d;
  border-radius: 2rem 2rem 0 0;
  text-align: center;
  font-size: 2rem;
  font-weight: bold;
  padding: 1.2rem 0 1rem 0;
  margin-bottom: 1.5rem;
}
.hero-card-img {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
}
.hero-card-img img {
  max-width: 80%;
  max-height: 180px;
  border-radius: 1.2rem;
  object-fit: contain;
  background: #0d2b1a;
}
.hero-card-img span {
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
</style>
