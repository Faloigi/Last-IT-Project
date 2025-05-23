<template>
  <div class="clan-edit-outer">
    <div class="clan-edit-container">
      <h1 class="edit-title">Crea Clan</h1>
      <form @submit.prevent="handleSubmit">
        <div class="form-group">
          <label for="nome">Nome</label>
          <input v-model="form.nome" id="nome" required maxlength="20" placeholder="Nome clan" />
        </div>
        <div class="form-group">
          <label for="image">Immagine</label>
          <div v-if="form.image" class="img-preview-box">
            <img :src="getClanImgSrc(form.image)" alt="Immagine clan" class="img-preview" @error="onImageError" />
          </div>
          <div class="img-input-row">
            <label class="custom-file-label">
              <input type="file" accept="image/*" @change="onFileChange" style="display:none" />
              <span>{{ selectedFileName || 'Carica immagine' }}</span>
            </label>
          </div>
          <div v-if="selectedFileName" class="img-path-preview">
            <span>File selezionato: {{ selectedFileName }}</span>
          </div>
          <div v-if="imageError" class="img-path-preview" style="color:#e74c3c">Immagine non trovata</div>
        </div>
        <div class="clan-members-section">
          <h2>Membri del Clan</h2>
          <ul class="clan-members-list">
            <li v-for="membro in membri" :key="membro" class="clan-member-item">
              <span>{{ membro }}</span>
              <button class="remove-member-btn" @click="removeMember(membro)">Rimuovi</button>
            </li>
          </ul>
          <div class="add-member-section">
            <label>Aggiungi player:</label>
            <select v-model="selectedToAdd">
              <option value="" disabled>Seleziona player</option>
              <option v-for="p in nonMembri" :key="p.username" :value="p.username">{{ p.username }}</option>
            </select>
            <button class="add-member-btn" @click="addMember">Aggiungi</button>
          </div>
        </div>
        <div class="form-group">
          <label>Creatore <span style="color:#e74c3c">*</span></label>
          <select v-model="form.creator" required>
            <option value="" disabled>Seleziona creatore</option>
            <option v-for="m in membri" :key="m" :value="m">{{ m }}</option>
          </select>
        </div>
        <div class="edit-btns">
          <button type="submit" class="admin-btn">Crea Clan</button>
          <button type="button" class="admin-btn cancel" @click="() => router.push('/clans')">Annulla</button>
        </div>
      </form>
      <div v-if="message" :class="['message', messageType]">{{ message }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const form = ref({ nome: '', image: '', creator: '' })
const message = ref('')
const messageType = ref('')
const selectedFileName = ref('')
const selectedFile = ref(null)
const imageError = ref(false)
const membri = ref([])
const nonMembri = ref([])
const selectedToAdd = ref('')

async function fetchNonMembri() {
  // Prendi solo player senza clan e non già tra i membri
  const res = await fetch('http://localhost/BigBlackDeath/backend/Player/getStatsPlayer.php')
  const allPlayers = await res.json()
  const membriSet = new Set(membri.value)
  nonMembri.value = allPlayers.filter(p => (!p.clan || p.clan === null || p.clan === '') && !membriSet.has(p.username))
}

function onFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    if (form.value.image && form.value.image.startsWith('blob:')) {
      URL.revokeObjectURL(form.value.image)
    }
    selectedFile.value = file
    selectedFileName.value = file.name
    form.value.image = URL.createObjectURL(file)
    imageError.value = false
  }
}

function onImageError() {
  imageError.value = true
}

async function uploadImage() {
  if (!selectedFile.value) return null
  const formData = new FormData()
  formData.append('image', selectedFile.value)
  formData.append('nomeClan', form.value.nome)
  const res = await fetch('http://localhost/BigBlackDeath/backend/Clan/uploadClanImage.php', {
    method: 'POST',
    body: formData
  })
  const data = await res.json()
  if (data.success && data.filename) {
    if (form.value.image && form.value.image.startsWith('blob:')) {
      URL.revokeObjectURL(form.value.image)
    }
    form.value.image = data.filename
    selectedFile.value = null
    selectedFileName.value = ''
    imageError.value = false
    return data.filename
  } else {
    message.value = data.error || 'Errore upload immagine'
    messageType.value = 'error'
    return null
  }
}

function removeMember(membro) {
  membri.value = membri.value.filter(m => m !== membro)
  if (form.value.creator === membro) form.value.creator = ''
  fetchNonMembri()
}

function addMember(e) {
  e.preventDefault()
  if (!selectedToAdd.value) return
  if (!membri.value.includes(selectedToAdd.value)) {
    membri.value.push(selectedToAdd.value)
    fetchNonMembri()
  }
  selectedToAdd.value = ''
}

watch(membri, (newVal) => {
  if (!newVal.includes(form.value.creator)) {
    form.value.creator = ''
  }
})

async function handleSubmit() {
  if (!form.value.nome || !form.value.image || membri.value.length === 0 || !form.value.creator) {
    message.value = 'Tutti i campi sono obbligatori';
    messageType.value = 'error';
    return;
  }
  let imageFileName = form.value.image
  if (selectedFile.value) {
    const uploaded = await uploadImage()
    if (!uploaded) return
    imageFileName = uploaded
    form.value.image = imageFileName
  } else if (form.value.image && form.value.image.startsWith('blob:')) {
    imageFileName = ''
  } else if (form.value.image) {
    imageFileName = form.value.image.split('/').pop()
  }
  const url = 'http://localhost/BigBlackDeath/backend/Clan/createClan.php'
  const body = {
    nome: form.value.nome,
    image: imageFileName,
    membri: membri.value,
    creator: form.value.creator
  }
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body)
  })
  const result = await res.json()
  message.value = result.success ? 'Clan creato!' : (result.error || 'Errore')
  messageType.value = result.success ? 'success' : 'error'
  if (result.success) {
    if (form.value.image && form.value.image.startsWith('blob:')) {
      URL.revokeObjectURL(form.value.image)
    }
    setTimeout(() => router.push('/clans'), 800)
  }
}

function getClanImgSrc(img) {
  if (!img) return ''
  if (img.startsWith('blob:')) return img
  if (img.startsWith('/images/clans/')) return img
  return `/images/clans/${img}`
}

onMounted(() => {
  fetchNonMembri()
})
</script>

<style scoped>
/* Usa lo stile di EroeEditView adattato per i clan */
.clan-edit-outer {
  min-height: 100vh;
  background: linear-gradient(135deg, #071b13 70%, #09351e 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding-top: 80px;
  overflow-x: hidden;
}
.clan-edit-container {
  background: #0e2d1e;
  border-radius: 2.2rem;
  box-shadow: 0 8px 40px #000a;
  padding: 1.7rem 2rem 1.2rem 2rem;
  width: 480px;
  max-width: 98vw;
  color: #fff;
  display: flex;
  flex-direction: column;
  align-items: center;
  box-sizing: border-box;
  max-width: 100vw;
}
.edit-title {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 1.5rem;
  text-align: center;
  letter-spacing: 1px;
  color: #fff;
  text-shadow: 0 2px 8px #0006;
}
.form-group {
  margin-bottom: 1.1rem;
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}
.form-group label {
  font-size: 1rem;
  color: #b6e2c6;
  font-weight: 600;
  margin-bottom: 0.1rem;
  letter-spacing: 0.5px;
}
.form-group input,
.form-group select {
  padding: 0.7rem 1rem;
  border-radius: 10px;
  border: none;
  background: #1a4d36;
  color: #fff;
  font-size: 1rem;
  transition: background 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 8px #0002;
}
.form-group input:focus,
.form-group select:focus {
  outline: none;
  background: #19d86b22;
  box-shadow: 0 4px 16px #19d86b33;
}
.img-input-row {
  display: flex;
  gap: 0.7rem;
  align-items: center;
  justify-content: space-between;
}
.custom-file-label {
  background: #19d86b;
  color: #fff;
  border-radius: 8px;
  padding: 0.5rem 1.2rem;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s, filter 0.2s;
  box-shadow: 0 1px 4px #19d86b33;
  display: inline-block;
  margin-right: 0.7rem;
}
.custom-file-label span {
  color: #fff;
}
.custom-file-label:hover {
  background: #13b85a;
  filter: brightness(1.1);
}
.img-path-preview {
  margin-top: 0.5rem;
  color: #b6e2c6;
  font-size: 1rem;
}
.edit-btns {
  display: flex;
  gap: 0.7rem;
  justify-content: center;
  margin-top: 2.2rem;
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
.admin-btn.cancel {
  background: #888;
  color: #fff;
}
.admin-btn.cancel:hover {
  background: #555;
}
.message {
  margin-top: 1.1rem;
  padding: 0.7rem 1rem;
  border-radius: 10px;
  font-size: 1rem;
  text-align: center;
  box-shadow: 0 2px 8px #0002;
}
.message.success {
  background: #19d86b33;
  color: #19d86b;
}
.message.error {
  background: #e74c3c33;
  color: #e74c3c;
}
.img-preview-box {
  width: 100%;
  display: flex;
  justify-content: center;
  margin-bottom: 1rem;
}
.img-preview {
  max-width: 150px;
  max-height: 120px;
  border-radius: 1rem;
  object-fit: contain;
  background: #0d2b1a;
  box-shadow: 0 2px 12px #0005;
  display: block;
}
.clan-members-section {
  margin-top: 2.5rem;
  background: #09351e;
  border-radius: 1.2rem;
  padding: 1.2rem 1.5rem;
  box-shadow: 0 2px 12px #0005;
  width: 100%;
  max-width: 420px;
}
.clan-members-section h2 {
  color: #19d86b;
  font-size: 1.3rem;
  margin-bottom: 1rem;
}
.clan-members-list {
  list-style: none;
  padding: 0;
  margin: 0 0 1.2rem 0;
}
.clan-member-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.4rem 0;
  border-bottom: 1px solid #19d86b22;
}
.remove-member-btn {
  background: #e74c3c;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 0.3rem 0.8rem;
  font-size: 0.95rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
}
.remove-member-btn:hover {
  background: #c0392b;
}
.add-member-section {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  margin-top: 0.7rem;
}
.add-member-section label {
  color: #b6e2c6;
  font-size: 1rem;
}
.add-member-btn {
  background: #19d86b;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 0.3rem 0.8rem;
  font-size: 0.95rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
}
.add-member-btn:hover {
  background: #13b85a;
}
@media (max-width: 800px) {
  .clan-edit-container {
    width: 98vw;
    padding: 1rem 0.3rem 1rem 0.3rem;
    border-radius: 1rem;
  }
  .edit-title {
    font-size: 1.2rem;
  }
  .admin-btn {
    padding: 0.5rem 0.7rem;
    font-size: 0.95rem;
  }
}
</style>
