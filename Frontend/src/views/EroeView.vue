<template>
  <div class="hero-container" v-if="loading === false">
    <h1>{{ eroe?.eroe || nome }}</h1>
    <div v-if="fetchErrore" class="error-message">
      Eroe non trovato o errore di rete.
    </div>
    <div v-else-if="eroe">
      <div class="hero-info-box">
        <div class="hero-img-box">
          <img v-if="eroe.img" :src="eroe.img" :alt="eroe.eroe" />
          <span v-else class="img-placeholder">Immagine</span>
        </div>
        <div class="hero-details">
          <div><b>Classe:</b> {{ eroe.classe || '-' }}</div>
          <div><b>Difficoltà:</b> {{ eroe.difficolta || '-' }}</div>
          <div><b>Partite giocate:</b> {{ eroe.partite || '-' }}</div>
          <div><b>KDA:</b> {{ eroe.kd || '-' }}</div>
          <div><b>% Vittorie:</b> {{ eroe.vittorie ? eroe.vittorie + '%' : '-' }}</div>
          <div><b>Danni medi:</b> {{ eroe.danni || '-' }}</div>
        </div>
      </div>
      <div class="hero-matches-title">Migliori partite con {{ eroe.eroe }}</div>
      <div class="hero-matches-list">
        <div v-for="match in eroe.matches" :key="match.par_id" class="hero-match-card">
          <router-link :to="`/partita/${match.par_id}`" class="hero-match-link">
            <div class="hero-match-info">
              <div class="hero-match-meta">
                <span class="hero-match-player">{{ match.username }}</span>
                <span class="hero-match-date">{{ formatDate(match.data) }}</span>
                <span class="hero-match-modalita">{{ match.modalita }}</span>
                <span class="hero-match-mappa">{{ match.mappa }}</span>
              </div>
              <div class="hero-match-kda">{{ match.uccisioni }}/{{ match.morti }} <span class="kda-label">K/D</span></div>
              <div class="hero-match-danni">Danni: {{ match.danni }}</div>
              <div class="hero-match-cure">Cure: {{ match.cure }}</div>
              <div class="hero-match-risultato" :class="{ win: match.risultato === 'Vinto', lose: match.risultato === 'Perso' }">
                {{ match.risultato === 'Vinto' ? 'Vittoria' : 'Sconfitta' }}
              </div>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="loading">Caricamento...</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const nome = route.params.nome
const eroe = ref(null)
const fetchErrore = ref(false)
const loading = ref(true)

function formatDate(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleString()
}

async function fetchEroe() {
  fetchErrore.value = false
  loading.value = true
  try {
    const res = await fetch(`http://localhost/BigBlackDeath/backend/Eroi/getEroe.php?nome=${encodeURIComponent(nome)}`)
    if (!res.ok) throw new Error('Errore fetch')
    eroe.value = await res.json()
    loading.value = false
  } catch (e) {
    fetchErrore.value = true
    eroe.value = null
    loading.value = false
  }
}

onMounted(() => {
  fetchEroe()
})
</script>

<style scoped>
.hero-container {
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
.hero-info-box {
  display: flex;
  gap: 2.5rem;
  align-items: center;
  justify-content: center;
  margin-bottom: 2.5rem;
}
.hero-img-box {
  width: 180px;
  height: 180px;
  border-radius: 1.5rem;
  background: #0d2b1a;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.hero-img-box img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 1.5rem;
  display: block;
  margin: 0 auto;
}
.img-placeholder {
  font-size: 2rem;
  color: #fff;
}
.hero-details {
  font-size: 1.3rem;
  color: #b6e2c6;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.hero-matches-title {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 1.2rem;
  text-align: center;
  margin-top: 2.5rem;
}
.hero-matches-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
  gap: 2rem;
  justify-items: center;
}
.hero-match-card {
  background: #09351e;
  border-radius: 1.2rem;
  box-shadow: 0 2px 12px #0005;
  transition: transform 0.15s;
  width: 340px;
  min-height: 210px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  overflow: hidden;
}
.hero-match-card:hover {
  transform: translateY(-6px) scale(1.03);
  box-shadow: 0 6px 24px #0007;
}
.hero-match-link {
  text-decoration: none;
  color: inherit;
  display: block;
  padding: 1.2rem 1.5rem;
  height: 100%;
}
.hero-match-info {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  height: 100%;
}
.hero-match-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1.2rem;
  font-size: 1.1rem;
  color: #b6e2c6;
  margin-bottom: 0.2rem;
  width: 100%;
}
.hero-match-meta span {
  max-width: 120px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  display: inline-block;
  vertical-align: middle;
}
.hero-match-player {
  font-weight: bold;
  color: #19d86b;
}
.hero-match-date {
  color: #fff;
}
.hero-match-kda {
  font-size: 1.2rem;
  color: #b6e2c6;
}
.kda-label {
  font-size: 1rem;
  color: #b6e2c6;
  margin-left: 0.5rem;
  font-weight: 500;
}
.hero-match-danni, .hero-match-cure {
  font-size: 1rem;
  color: #b6e2c6;
}
.hero-match-risultato {
  font-size: 1.1rem;
  font-weight: bold;
  margin-top: 0.5rem;
  padding: 0.2rem 1.1rem;
  border-radius: 8px;
  display: inline-block;
}
.hero-match-risultato.win {
  background: #19d86b;
  color: #0a2e1a;
}
.hero-match-risultato.lose {
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
@media (max-width: 900px) {
  .hero-matches-list {
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  }
  .hero-match-card {
    width: 100%;
    min-width: 0;
  }
}
</style>
