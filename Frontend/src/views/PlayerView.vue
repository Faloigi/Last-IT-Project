<template>
  <div class="player-container">
    <div v-if="isAdmin" class="admin-buttons">
      <button class="admin-btn">Crea Eroe</button>
      <button class="admin-btn">Modifica Eroe</button>
      <button class="admin-btn">Elimina Eroe</button>
      <button class="admin-btn">Crea Player</button>
      <button class="admin-btn">Modifica Player</button>
      <button class="admin-btn">Elimina Player</button>
      <button class="admin-btn">Crea Clan</button>
      <button class="admin-btn">Modifica Clan</button>
      <button class="admin-btn">Elimina Clan</button>
      <button class="admin-btn">Crea Partita</button>
      <button class="admin-btn">Modifica Partita</button>
      <button class="admin-btn">Elimina Partita</button>
    </div>
    <h1>{{ username }}
      <router-link v-if="clanName" :to="`/clan/${clanName}`">
        <span>({{ clanName }})</span>
      </router-link>
      </h1>
    <div v-if="fetchError" class="error-message">
      Player non trovato o errore di rete.
    </div>
    <div v-else-if="player">
      <div class="player-layout">
        <!-- Colonna sinistra -->
        <div class="player-sidebar">
          <div class="rank-btn">
            <img v-if="player.rank_img" :src="getRankImgSrc(player.rank_img)" class="rank-img" />
            <span v-else>{{ player.rank || 'Rank' }}</span>
          </div>
          <div class="eroi-giocati">
            <div class="sidebar-title">Eroi Giocati</div>
            <div v-for="eroe in player.eroiGiocati" :key="eroe.ero_id" class="eroe-giocato-card">
              <router-link :to="`/eroe/${eroe.nome || eroe.name || 'Eroe'}`">
                <template v-if="eroe.img">
                  <img :src="getHeroImgSrc(eroe.img)" :alt="eroe.nome || eroe.name || 'Eroe'" class="eroe-img-sidebar" />
                </template>
                <span>{{ eroe.nome || eroe.name || 'Eroe' }}</span>
              </router-link>
            </div>
          </div>
        </div>
        <!-- Colonna centrale -->
        <div class="player-main">
          <div class="main-title">Partite Giocate</div>
          <div v-if="filteredMatches.length === 0" class="no-matches">
            Nessuna partita trovata con i filtri attuali.
          </div>
          <div v-else>
            <router-link
              v-for="match in filteredMatches"
              :key="match.par_id"
              :to="`/partita/${match.par_id}`"
              class="match-card-link"
            >
              <div class="match-card-opgg" :class="{ win: match.risultato === 'Vinto', lose: match.risultato === 'Perso' }">
                <!-- Immagine eroe giocato -->
                <div class="match-hero-img">
                  <img v-if="match.eroe_img" :src="getHeroImgSrc(match.eroe_img)" :alt="match.eroe" />
                  <div v-else class="img-placeholder">{{ match.eroe?.charAt(0) }}</div>
                </div>
                <!-- Centro: risultato, KDA, dettagli -->
                <div class="match-center">
                  <div class="match-result" :class="{ win: match.risultato === 'Vinto', lose: match.risultato === 'Perso' }">
                    {{ match.risultato === 'Vinto' ? 'Vittoria' : 'Sconfitta' }}
                  </div>
                  <div class="match-kd">
                    {{ (match.morti && match.morti > 0) ? (match.uccisioni / match.morti).toFixed(1) : match.uccisioni }}
                    <span class="kd-label">K/D</span>
                  </div>
                  <div class="match-kd-details">
                    {{ match.uccisioni }}/{{ match.morti }}
                  </div>
                  <div class="match-meta">
                    <span>{{ match.modalita }}</span> - <span>{{ match.mappa }}</span> <span v-if="match.data">- {{ new Date(match.data).toLocaleDateString() }}</span>
                  </div>
                </div>
                <!-- Lista partecipanti -->
                <div class="match-players">
                  <div v-for="p in match.partecipanti" :key="p" class="player-name" :class="{ highlight: p === player.username }">{{ p }}</div>
                </div>
              </div>
            </router-link>
          </div>
        </div>
        <!-- Colonna destra -->
        <div class="player-filters">
          <input v-model="searchHero" placeholder="Cerca per Eroe" />
          <Dropdown v-model="modalita" :options="modalitaOptions" optionLabel="name" optionValue="value" placeholder="Tutte le modalità" class="p-dropdown-green" />
          <Dropdown v-model="mappa" :options="mappaOptions" optionLabel="name" optionValue="value" placeholder="Tutte le mappe" class="p-dropdown-green" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import Dropdown from 'primevue/dropdown'

const player = ref(null)
const fetchError = ref(false)
const route = useRoute()
const username = computed(() => route.params.username || '')

const searchHero = ref('')
const modalita = ref('')
const mappa = ref('')
const clanName = ref("")
const isAdmin = ref(false)

// Personalizza il messaggio "No available options"
const emptyMessageTemplate = (options) => {
  return {
    props: options,
    template: `<div class="custom-empty-message">No available options</div>`
  }
}

async function getPlayerData() {
  fetchError.value = false
  try {
    const res = await fetch('http://localhost/BigBlackDeath/backend/Player/getPlayerData.php?username=' + username.value)
    const data = await res.json()
    if (!data || !data.username) {
      fetchError.value = true
      player.value = null
    } else {
      player.value = data
    }
  } catch (e) {
    fetchError.value = true
    player.value = null
  }
}

async function getClanName(){
  fetchError.value = false
  try{
    if (!player.value || !player.value.clan_id) return;
    const res = await fetch('http://localhost/BigBlackDeath/backend/Clan/getClanName.php?id=' + player.value.clan_id)
    const data = await res.json()
    clanName.value = data?.nome || ''
  } catch (e) {
    clanName.value = ''
  }
}
// Popola dinamicamente le modalità e le mappe disponibili
const modalitaDisponibili = computed(() => {
  const set = new Set()
  if (player.value.matches) {
    player.value.matches.forEach(m => m.modalita && set.add(m.modalita))
  }
  return Array.from(set)
})

// Query SQL: SELECT mod_id, mod_nome FROM modalita
const modalitaOptions = computed(() => {
  const options = modalitaDisponibili.value.map(m => ({ name: m, value: m }))
  options.unshift({ name: 'Tutte le modalità', value: '' })
  return options
})

const mappeDisponibili = computed(() => {
  const set = new Set()
  if (player.value.matches) {
    player.value.matches.forEach(m => m.mappa && set.add(m.mappa))
  }
  return Array.from(set)
})

// Query SQL: SELECT map_id, map_nome, map_image FROM mappe
const mappaOptions = computed(() => {
  const options = mappeDisponibili.value.map(m => ({ name: m, value: m }))
  options.unshift({ name: 'Tutte le mappe', value: '' })
  return options
})

// Filtra le partite in base ai filtri selezionati
const filteredMatches = computed(() => {
  if (!player.value || !player.value.matches) return []
  return player.value.matches.filter(match =>
    (!searchHero.value || match.eroe?.toLowerCase().includes(searchHero.value.toLowerCase())) &&
    (!modalita.value || match.modalita === modalita.value) &&
    (!mappa.value || match.mappa === mappa.value)
  )
})

onMounted(() => {
  getPlayerData()
  const user = localStorage.getItem('user')
  if (user) {
    try {
      const parsed = JSON.parse(user)
      isAdmin.value = parsed.ute_ruolo === 'admin'
    } catch {}
  }
})

watch(player, (newVal) => {
  if (newVal && newVal.clan_id) {
    getClanName()
  }
})

function getRankImgSrc(img) {
  if (!img) return ''
  if (img.startsWith('/images/ranks/')) return img
  return `/images/ranks/${img}`
}

function getHeroImgSrc(img) {
  if (!img) return ''
  if (img.startsWith('/images/heroes/')) return img
  return `/images/heroes/${img}`
}
</script>

<style scoped>
.player-container {
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
  text-align: center;
  margin-top: 1.5rem;
  font-weight: bold;
  color: #fff;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-family: 'Poppins', sans-serif;
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
  border-radius: 18px;
  padding: 0.7rem 0;
  font-weight: bold;
  margin-bottom: 1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 90px;
  min-width: 120px;
}
.rank-img {
  width: 128px;
  height: 128px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0d2b1a 60%, #19d86b22 100%);
  box-shadow: 0 4px 24px #000a, 0 0 0 10px #19d86b33, 0 0 24px 4px #19d86b44;
  padding: 16px;
  object-fit: contain;
  display: block;
  margin: 0 auto;
}
.eroi-giocati {
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
.eroe-giocato-card {
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
  width: 1000px;
  margin-left: auto;
  margin-right: auto;
}
.match-card-opgg {
  background: #09351e;
  border-radius: 14px;
  margin-bottom: 1.2rem;
  display: flex;
  align-items: center;
  padding: 1.2rem 2rem;
  gap: 2.5rem;
  box-shadow: 0 2px 12px #0005;
  transition: background 0.2s;
  width: 1000px;
  margin-left: auto;
  margin-right: auto;
}
.match-card-opgg.win {
  background: #09351e;
}
.match-card-opgg.lose {
  background: #2c1818;
}
.match-hero-img {
  width: 72px;
  height: 72px;
  border-radius: 12px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #0d2b1a;
}
.match-hero-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 12px;
}
.img-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  color: #fff;
  background: #145c3a;
  border-radius: 12px;
}
.match-center {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 0.3rem;
}
.match-result {
  font-size: 1.3rem;
  font-weight: bold;
  margin-bottom: 0.2rem;
  color: #fff;
  padding: 0.2rem 1.1rem;
  border-radius: 8px;
  display: inline-block;
}
.match-result.win {
  background: #19d86b;
  color: #0a2e1a;
}
.match-result.lose {
  background: #e74c3c;
  color: #fff;
}
.match-kd {
  font-size: 2.5rem;
  font-weight: bold;
  color: #fff;
  margin-bottom: 0.1rem;
}
.match-kd-details {
  font-size: 1.3rem;
  color: #b6e2c6;
  margin-bottom: 0.2rem;
}
.match-meta {
  font-size: 1rem;
  color: #b6e2c6;
  margin-top: 0.2rem;
}
.match-players {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
  min-width: 120px;
  align-items: flex-end;
}
.player-name {
  font-size: 1.1rem;
  color: #fff;
  padding: 0.1rem 0.5rem;
  border-radius: 6px;
}
.player-name.highlight {
  background: #19d86b;
  color: #0a2e1a;
  font-weight: bold;
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
.p-dropdown-green {
  width: 100%;
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

.kd-label {
  font-size: 1.1rem;
  color: #b6e2c6;
  margin-left: 0.7rem;
  font-weight: 500;
}

.match-card-link {
  text-decoration: none;
  color: inherit;
  display: block;
  transition: filter 0.15s;
}
.match-card-link:hover {
  filter: brightness(1.15) drop-shadow(0 0 8px #19d86b88);
}

.eroe-img-sidebar {
  width: 40px;
  height: 40px;
  object-fit: contain;
  border-radius: 8px;
  margin-right: 0.5rem;
  vertical-align: middle;
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
</style>
