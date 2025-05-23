<template>
  <div class="clan-container" v-if="loading === false">
    <h1>{{ clan?.nome || nome }}</h1>
    <div v-if="fetchError" class="error-message">
      Clan non trovato o errore di rete.
    </div>
    <div v-else-if="clan">
      <div class="clan-info-box">
        <div class="clan-img-box">
          <img v-if="clan.img" :src="clan.img" :alt="clan.nome" />
          <span v-else class="img-placeholder">Immagine</span>
        </div>
        <div class="clan-details">
          <div><b>Punti:</b> {{ clan.punti || '-' }}</div>
          <div><b>Rank:</b> <img v-if="clan.rank_img" :src="getRankImgSrc(clan.rank_img)" class="rank-img" /> <span v-else>{{ clan.rank || '-' }}</span></div>
          <div><b>Partite giocate:</b> {{ clan.partite || '-' }}</div>
          <div><b>KDA:</b> {{ clan.kd || '-' }}</div>
          <div><b>% Vittorie:</b> {{ clan.vittorie ? clan.vittorie + '%' : '-' }}</div>
          <div><b>Danni medi:</b> {{ clan.danni || '-' }}</div>
        </div>
      </div>
      <div class="clan-membri-title">Membri del Clan</div>
      <div class="clan-membri-list">
        <div v-for="membro in clan.membri" :key="membro.pla_id" class="clan-membro-card">
          <router-link :to="`/player/${membro.pla_username}`">
            <span>{{ membro.pla_username }}</span>
          </router-link>
          <span class="membro-mmr">MMR: {{ membro.pla_mmr }}</span>
          <span class="membro-livello">Livello: {{ membro.pla_livello }}</span>
        </div>
      </div>
      <div class="clan-matches-title">Migliori partite dei membri</div>
      <div class="clan-matches-list">
        <div v-for="match in clan.matches" :key="match.par_id" class="clan-match-card">
          <router-link :to="`/partita/${match.par_id}`" class="clan-match-link">
            <div class="clan-match-info">
              <div class="clan-match-meta">
                <span class="clan-match-player">{{ match.username }}</span>
                <span class="clan-match-date">{{ formatDate(match.data) }}</span>
                <span class="clan-match-modalita">{{ match.modalita }}</span>
                <span class="clan-match-mappa">{{ match.mappa }}</span>
                <span class="clan-match-eroe">{{ match.eroe }}</span>
              </div>
              <div class="clan-match-kda">{{ match.uccisioni }}/{{ match.morti }} <span class="kda-label">K/D</span></div>
              <div class="clan-match-danni">Danni: {{ match.danni }}</div>
              <div class="clan-match-cure">Cure: {{ match.cure }}</div>
              <div class="clan-match-risultato" :class="{ win: match.risultato === 'Vinto', lose: match.risultato === 'Perso' }">
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
const clan = ref(null)
const fetchError = ref(false)
const loading = ref(true)
const isAdmin = ref(false)

function formatDate(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleString()
}

function getRankImgSrc(img) {
  if (!img) return ''
  if (img.startsWith('/images/ranks/')) return img
  return `/images/ranks/${img}`
}

async function fetchClan() {
  fetchError.value = false
  loading.value = true
  try {
    const res = await fetch(`http://localhost/BigBlackDeath/backend/Clan/getClanData.php?nome=${encodeURIComponent(nome)}`)
    if (!res.ok) throw new Error('Errore fetch')
    clan.value = await res.json()
    loading.value = false
  } catch (e) {
    fetchError.value = true
    clan.value = null
    loading.value = false
  }
}

onMounted(() => {
  fetchClan()
  const user = localStorage.getItem('user')
  if (user) {
    try {
      const parsed = JSON.parse(user)
      isAdmin.value = parsed.ute_ruolo === 'admin'
    } catch {}
  }
})
</script>

<style scoped>
.clan-container {
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
.clan-info-box {
  display: flex;
  gap: 2.5rem;
  align-items: center;
  justify-content: center;
  margin-bottom: 2.5rem;
}
.clan-img-box {
  width: 180px;
  height: 180px;
  border-radius: 1.5rem;
  background: #0d2b1a;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.clan-img-box img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 1.5rem;
}
.img-placeholder {
  font-size: 2rem;
  color: #fff;
}
.clan-details {
  font-size: 1.3rem;
  color: #b6e2c6;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.rank-img {
  width: 32px;
  height: 32px;
  vertical-align: middle;
  margin-left: 0.5rem;
}
.clan-membri-title {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 1.2rem;
  text-align: center;
  margin-top: 2.5rem;
}
.clan-membri-list {
  display: flex;
  flex-wrap: wrap;
  gap: 1.2rem;
  justify-content: center;
  margin-bottom: 2.5rem;
}
.clan-membro-card {
  background: #09351e;
  border-radius: 1.2rem;
  box-shadow: 0 2px 12px #0005;
  padding: 1rem 1.5rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 160px;
  font-size: 1.1rem;
}
.membro-mmr, .membro-livello {
  color: #b6e2c6;
  font-size: 1rem;
}
.clan-matches-title {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 1.2rem;
  text-align: center;
  margin-top: 2.5rem;
}
.clan-matches-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
  gap: 2rem;
  justify-items: center;
}
.clan-match-card {
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
.clan-match-card:hover {
  transform: translateY(-6px) scale(1.03);
  box-shadow: 0 6px 24px #0007;
}
.clan-match-link {
  text-decoration: none;
  color: inherit;
  display: block;
  padding: 1.2rem 1.5rem;
  height: 100%;
}
.clan-match-info {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  height: 100%;
}
.clan-match-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1.2rem;
  font-size: 1.1rem;
  color: #b6e2c6;
  margin-bottom: 0.2rem;
  width: 100%;
}
.clan-match-meta span {
  max-width: 120px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  display: inline-block;
  vertical-align: middle;
}
.clan-match-player {
  font-weight: bold;
  color: #19d86b;
}
.clan-match-date {
  color: #fff;
}
.clan-match-kda {
  font-size: 1.2rem;
  color: #fff;
  margin-top: 0.2rem;
}
.kda-label {
  font-size: 0.9rem;
  color: #1affb2;
  margin-left: 0.3rem;
}
.clan-match-danni, .clan-match-cure {
  color: #b6e2c6;
  font-size: 1rem;
}
.clan-match-risultato {
  font-weight: bold;
  margin-top: 0.5rem;
}
.clan-match-risultato.win {
  color: #19d86b;
}
.clan-match-risultato.lose {
  color: #ff4d4d;
}
.loading {
  color: #fff;
  text-align: center;
  font-size: 2rem;
  margin-top: 4rem;
}
.error-message {
  color: #ff4d4d;
  text-align: center;
  font-size: 1.3rem;
  margin-top: 2rem;
}
</style>
