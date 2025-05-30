<template>
  <div class="eroi-container">
    <h1>Eroi</h1>
    <div class="filters">
      <input v-model="search" placeholder="Cerca Eroe..." />
      <Dropdown v-model="classe" :options="classeOptionsDropdown" optionLabel="name" optionValue="value" placeholder="Tutte le classi" class="p-dropdown-green" />
      <Dropdown v-model="difficolta" :options="difficoltaOptionsDropdown" optionLabel="name" optionValue="value" placeholder="Tutte le difficoltà" class="p-dropdown-green" />
    </div>
    <div class="eroi-grid">
      <div
        v-for="eroe in filteredEroi"
        :key="eroe.id || eroe.ero_id"
        class="eroe-card-link"
      >
        <div class="eroe-card">
          <router-link :to="`/eroe/${eroe.eroe}`" class="eroe-card-header-link">
            <div class="eroe-card-header">{{ eroe.eroe }}</div>
          </router-link>
          <div class="eroe-card-img">
            <img v-if="eroe.img" :src="getHeroImgSrc(eroe.img)" :alt="eroe.eroe" />
            <span v-else>Immagine</span>
          </div>
          <div class="eroe-card-info">
            <div class="eroe-card-row"><b>Classe:</b> {{ eroe.classe || '-' }}</div>
            <div class="eroe-card-row"><b>Difficoltà:</b> {{ eroe.difficolta || '-' }}</div>
            <div class="eroe-card-row"><b>Partite:</b> {{ eroe.partite_giocate || eroe.partite || '-' }}</div>
            <div class="eroe-card-row"><b>% Vittorie:</b> {{ eroe.vittorie ? eroe.vittorie + '%' : '-' }}</div>
          </div>
          <div v-if="isAdmin" class="admin-buttons-card">
            <button class="admin-btn-card" @click="$router.push(`/eroi/modifica/${encodeURIComponent(eroe.eroe)}`)">Modifica</button>
            <button class="admin-btn-card" @click="handleDeleteEroe(eroe)">Elimina</button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="isAdmin" class="admin-buttons admin-buttons-bottom">
      <router-link to="admin/eroi/crea" class="admin-btn">Crea Eroe</router-link>
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
const isAdmin = ref(false)

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

function mapEroe(raw) {
  return {
    eroe: raw.eroe || raw.ero_nome || raw.nome,
    classe: raw.classe || raw.cla_nome,
    difficolta: raw.difficolta || raw.ero_difficolta,
    img: raw.img || raw.ero_image || raw.image,
    partite: raw.partite_giocate || raw.partite,
    vittorie: raw.vittorie,
    id: raw.id || raw.ero_id
  }
}

const fetchEroi = async () => {
  try {
    const res = await fetch('http://localhost/BigBlackDeath/backend/Eroi/getStatsEroe.php')
    const data = await res.json()
    eroi.value = Array.isArray(data) ? data.map(mapEroe) : []
  } catch (e) {
    eroi.value = []
  }
}

onMounted(() => {
  fetchEroi()
  const user = localStorage.getItem('user')
  if (user) {
    try {
      const parsed = JSON.parse(user)
      isAdmin.value = parsed.ute_ruolo === 'admin'
    } catch {}
  }
})

// Filtri
const filteredEroi = computed(() =>
  eroi.value.filter(eroe =>
    (!search.value || eroe.eroe.toLowerCase().includes(search.value.toLowerCase())) &&
    (!classe.value || eroe.classe === classe.value) &&
    (!difficolta.value || eroe.difficolta === difficolta.value)
  )
)

function getHeroImgSrc(img) {
  if (!img) return ''
  if (img.startsWith('/images/heroes/')) return img
  return `/images/heroes/${img}`
}

function handleDeleteEroe(eroe) {
  if (!confirm(`Sei sicuro di voler eliminare ${eroe.eroe}?`)) return
  fetch('http://localhost/BigBlackDeath/backend/Eroi/deleteEroe.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: eroe.id })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) fetchEroi()
      else alert(data.error || 'Errore eliminazione')
    })
    .catch(() => alert('Errore eliminazione'))
}
</script>

<style scoped>
.eroi-container {
  padding: 2rem;
  color: #fff;
  background: #071b13;
  min-height: 100vh;
  padding-top: 80px;
  overflow-x: hidden;
  box-sizing: border-box;
  max-width: 100vw;
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
  min-height: 400px;
  transition: transform 0.15s;
  cursor: pointer;
  padding-bottom: 1.5rem;
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
  display: block;
  margin: 0 auto;
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
.admin-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 2rem;
  justify-content: center;
}
.admin-btn {
  background: #19d86b;
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 0.7rem 1.5rem;
  font-size: 1.1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
}
.admin-btn:hover {
  background: #13b85a;
}
.admin-buttons-card {
  display: flex;
  gap: 0.7rem;
  justify-content: center;
  margin-top: 1.2rem;
}
.admin-btn-card {
  background: #e74c3c;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 0.5rem 1.2rem;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
}
.admin-btn-card:first-child {
  background: #f1c40f;
  color: #222;
}
.admin-btn-card:hover {
  filter: brightness(1.1);
}
.admin-buttons-bottom {
  margin-top: 2.5rem;
  justify-content: center !important;
  display: flex;
  width: 100%;
}
</style>
