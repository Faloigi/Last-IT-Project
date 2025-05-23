<template>
  <div class="clans-container">
    <h1>Clan</h1>
    <div class="filters">
      <input v-model="search" placeholder="Cerca Clan..." />
    </div>
    <div class="clans-grid">
      <div
        v-for="clan in filteredClans"
        :key="clan.nome"
        class="clan-card-link"
      >
        <div class="clan-card">
          <router-link :to="`/clan/${encodeURIComponent(clan.nome)}`" class="clan-card-header-link">
            <div class="clan-card-header">{{ clan.nome }}</div>
          </router-link>
          <div class="clan-card-img">
            <img v-if="clan.img" :src="getClanImgSrc(clan.img)" :alt="clan.nome" />
            <span v-else>Immagine</span>
          </div>
          <div class="clan-card-info">
            <div class="clan-card-row"><b>Punti:</b> {{ clan.punti || '-' }}</div>
            <div class="clan-card-row"><b>Partecipanti:</b> {{ clan.partecipanti || '-' }}</div>
            <div class="clan-card-row"><b>% Vittorie:</b> {{ clan.vittorie ? clan.vittorie + '%' : '-' }}</div>
          </div>
          <div v-if="isAdmin" class="admin-buttons-card">
            <button class="admin-btn-card" @click="editClan(clan)">Modifica</button>
            <button class="admin-btn-card delete" @click="handleDeleteClan(clan)">Elimina</button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="isAdmin" class="admin-buttons admin-buttons-bottom">
      <button class="admin-btn" @click="createClan">Crea Clan</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const search = ref('')
const clans = ref([])
const isAdmin = ref(false)
const router = useRouter()

function mapClan(raw) {
  return {
    nome: raw.nome,
    punti: raw.punti,
    vittorie: raw.vittorie,
    partecipanti: raw.partecipanti,
    img: raw.img || raw.cln_image || ''
  }
}

const fetchClans = async () => {
  try {
    const res = await fetch('http://localhost/BigBlackDeath/backend/Clan/getStatsClan.php')
    const data = await res.json()
    clans.value = Array.isArray(data) ? data.map(mapClan) : []
  } catch (e) {
    clans.value = []
  }
}

onMounted(() => {
  fetchClans()
  const user = localStorage.getItem('user')
  if (user) {
    try {
      const parsed = JSON.parse(user)
      isAdmin.value = parsed.ute_ruolo === 'admin'
    } catch {}
  }
})

const filteredClans = computed(() =>
  clans.value.filter(clan =>
    !search.value || clan.nome.toLowerCase().includes(search.value.toLowerCase())
  )
)

function getClanImgSrc(img) {
  if (!img) return ''
  if (img.startsWith('/images/clans/')) return img
  return `/images/clans/${img}`
}

function createClan() {
  router.push('/admin/clan/crea')
}

function editClan(clan) {
  router.push(`/clan/modifica/${encodeURIComponent(clan.nome)}`)
}

async function handleDeleteClan(clan) {
  if (!confirm(`Sei sicuro di voler eliminare il clan ${clan.nome}?`)) return
  try {
    const res = await fetch('http://localhost/BigBlackDeath/backend/Clan/deleteClan.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nome: clan.nome })
    })
    const data = await res.json()
    if (data.success) fetchClans()
    else alert(data.error || 'Errore eliminazione')
  } catch {
    alert('Errore eliminazione')
  }
}
</script>

<style scoped>
.clans-container {
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
.filters input {
  padding: 0.6rem 1.2rem;
  border-radius: 10px;
  border: none;
  background: #09351e;
  color: #fff;
  font-size: 1.1rem;
}
.clans-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 2.5rem;
}
.clan-card {
  background: #09351e;
  border-radius: 2rem;
  box-shadow: 0 2px 12px #0005;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-height: 320px;
  transition: transform 0.15s;
  cursor: pointer;
  padding-bottom: 1.5rem;
}
.clan-card:hover {
  transform: translateY(-8px) scale(1.03);
  box-shadow: 0 6px 24px #0007;
}
.clan-card-header {
  width: 100%;
  background: #06341d;
  border-radius: 2rem 2rem 0 0;
  text-align: center;
  font-size: 2rem;
  font-weight: bold;
  padding: 1.2rem 0 1rem 0;
  margin-bottom: 1.5rem;
}
.clan-card-img {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
}
.clan-card-img img {
  max-width: 80%;
  max-height: 120px;
  border-radius: 1rem;
  object-fit: contain;
  background: #0d2b1a;
  box-shadow: 0 2px 12px #0005;
  display: block;
}
.clan-card-info {
  width: 100%;
  padding: 0.7rem 1.2rem 0.2rem 1.2rem;
  font-size: 1.1rem;
  color: #b6e2c6;
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}
.clan-card-row {
  margin-bottom: 0.2rem;
}
.admin-buttons-card {
  display: flex;
  gap: 0.7rem;
  justify-content: center;
  margin-top: 1.2rem;
  width: 100%;
}
.admin-btn-card {
  background: #19d86b;
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 0.5rem 1rem;
  font-size: 0.95rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s, filter 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 12px #19d86b33, 0 1.5px 8px #0002;
  letter-spacing: 0.5px;
}
.admin-btn-card:hover {
  background: #13b85a;
  filter: brightness(1.1);
  box-shadow: 0 4px 24px #19d86b55, 0 2px 12px #0003;
}
.admin-btn-card.delete {
  background: #e74c3c;
  color: #fff;
  box-shadow: 0 2px 12px #e74c3c33, 0 1.5px 8px #0002;
}
.admin-btn-card.delete:hover {
  background: #c0392b;
}
.admin-buttons {
  display: flex;
  gap: 0.7rem;
  justify-content: center;
  margin-top: 1.2rem;
  width: 100%;
}
.admin-btn {
  background: #19d86b;
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 0.5rem 1rem;
  font-size: 0.95rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s, filter 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 12px #19d86b33, 0 1.5px 8px #0002;
  letter-spacing: 0.5px;
}
.admin-btn:hover {
  background: #13b85a;
  filter: brightness(1.1);
  box-shadow: 0 4px 24px #19d86b55, 0 2px 12px #0003;
}
</style>
