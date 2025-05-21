<template>
  <div class="partita-container" v-if="loading === false">
    <h1>Partita #{{ partitaId }}</h1>
    <div v-if="fetchError" class="error-message">
      Partita non trovata o errore di rete.
    </div>
    <div v-else-if="partita">
      <div class="partita-info">
        <div class="info-box">
          <div><b>Modalità:</b> {{ partita.modalita }}</div>
          <div><b>Mappa:</b> {{ partita.mappa }}</div>
          <div><b>Data:</b> {{ formatDate(partita.data) }}</div>
        </div>
      </div>
      <div class="partecipanti-title">Partecipanti</div>
      <div class="teams-container">
        <div class="team team-win">
          <div class="team-title">Vincitori</div>
          <div class="team-list">
            <div v-for="p in teamWin" :key="p.username" class="partecipante-card">
              <div class="partecipante-hero-img">
                <img v-if="p.eroe_img" :src="p.eroe_img" :alt="p.eroe" />
                <div v-else class="img-placeholder">{{ p.eroe?.charAt(0) }}</div>
              </div>
              <div class="partecipante-info">
                <router-link :to="`/player/${p.username}`" class="partecipante-username">{{ p.username }}</router-link>
                <div class="partecipante-eroe">
                  <router-link :to="`/eroe/${p.eroe}`">{{ p.eroe }}</router-link>
                </div>
                <div class="partecipante-kda">{{ p.uccisioni }}/{{ p.morti }} <span class="kda-label">K/D</span></div>
                <div class="partecipante-danni">Danni: {{ p.danni }}</div>
                <div class="partecipante-cure">Cure: {{ p.cure }}</div>
                <div class="partecipante-risultato win">Vittoria</div>
              </div>
            </div>
          </div>
        </div>
        <div class="team team-lose">
          <div class="team-title">Sconfitti</div>
          <div class="team-list">
            <div v-for="p in teamLose" :key="p.username" class="partecipante-card">
              <div class="partecipante-hero-img">
                <img v-if="p.eroe_img" :src="p.eroe_img" :alt="p.eroe" />
                <div v-else class="img-placeholder">{{ p.eroe?.charAt(0) }}</div>
              </div>
              <div class="partecipante-info">
                <router-link :to="`/player/${p.username}`" class="partecipante-username">{{ p.username }}</router-link>
                <div class="partecipante-eroe">
                  <router-link :to="`/eroe/${p.eroe}`">{{ p.eroe }}</router-link>
                </div>
                <div class="partecipante-kda">{{ p.uccisioni }}/{{ p.morti }} <span class="kda-label">K/D</span></div>
                <div class="partecipante-danni">Danni: {{ p.danni }}</div>
                <div class="partecipante-cure">Cure: {{ p.cure }}</div>
                <div class="partecipante-risultato lose">Sconfitta</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="loading">Caricamento...</div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const partitaId = route.params.id
const partita = ref(null)
const fetchError = ref(false)
const loading = ref(true)

const teamWin = computed(() => partita.value?.partecipanti?.filter(p => p.risultato === 'Vinto') || [])
const teamLose = computed(() => partita.value?.partecipanti?.filter(p => p.risultato === 'Perso') || [])

function formatDate(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleString()
}

async function fetchPartita() {
  fetchError.value = false
  loading.value = true
  try {
    const res = await fetch(`http://localhost/BigBlackDeath/backend/Partita/getPartita.php?id=${partitaId}`)
    if (!res.ok) throw new Error('Errore fetch')
    partita.value = await res.json()
    loading.value = false
  } catch (e) {
    fetchError.value = true
    partita.value = null
    loading.value = false
  }
}

onMounted(() => {
  fetchPartita()
})
</script>

<style scoped>
.partita-container {
  padding: 2rem;
  color: #fff;
  background: #071b13;
  min-height: 100vh;
  padding-top: 80px;
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
.partita-info {
  display: flex;
  justify-content: center;
  margin-bottom: 2rem;
}
.info-box {
  background: #09351e;
  border-radius: 1.2rem;
  padding: 1.5rem 2.5rem;
  font-size: 1.3rem;
  box-shadow: 0 2px 12px #0005;
  display: flex;
  gap: 2.5rem;
}
.partecipanti-title {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 1.2rem;
  text-align: center;
}
.teams-container {
  display: flex;
  gap: 4rem;
  justify-content: center;
  margin-top: 2rem;
}
.team {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.team-title {
  font-size: 1.7rem;
  font-weight: bold;
  margin-bottom: 1.2rem;
  color: #fff;
  text-align: center;
}
.team-list {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  width: 100%;
  align-items: center;
}
.partecipante-card {
  background: #09351e;
  border-radius: 1.2rem;
  box-shadow: 0 2px 12px #0005;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  padding: 1.2rem 1.5rem;
  min-height: 180px;
  width: 400px;
  transition: transform 0.15s;
}
.partecipante-card:hover {
  transform: translateY(-6px) scale(1.03);
  box-shadow: 0 6px 24px #0007;
}
.partecipante-hero-img {
  width: 80px;
  height: 80px;
  border-radius: 12px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #0d2b1a;
}
.partecipante-hero-img img {
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
.partecipante-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}
.partecipante-username {
  font-size: 1.3rem;
  font-weight: bold;
  color: #19d86b;
  text-decoration: none;
}
.partecipante-username:hover {
  text-decoration: underline;
}
.partecipante-eroe {
  font-size: 1.1rem;
  color: #fff;
}
.partecipante-eroe a {
  color: #fff;
  text-decoration: underline;
}
.partecipante-kda {
  font-size: 1.2rem;
  color: #b6e2c6;
}
.kda-label {
  font-size: 1rem;
  color: #b6e2c6;
  margin-left: 0.5rem;
  font-weight: 500;
}
.partecipante-danni, .partecipante-cure {
  font-size: 1rem;
  color: #b6e2c6;
}
.partecipante-risultato {
  font-size: 1.1rem;
  font-weight: bold;
  margin-top: 0.5rem;
  padding: 0.2rem 1.1rem;
  border-radius: 8px;
  display: inline-block;
}
.partecipante-risultato.win {
  background: #19d86b;
  color: #0a2e1a;
}
.partecipante-risultato.lose {
  background: #e74c3c;
  color: #fff;
}
.loading {
  color: #fff;
  text-align: center;
  font-size: 2rem;
  margin-top: 5rem;
}
.error-message {
  color: #e74c3c;
  background: #2c1818;
  padding: 1rem 2rem;
  border-radius: 1rem;
  text-align: center;
  margin: 2rem auto;
  max-width: 600px;
  font-size: 1.3rem;
}
</style>
