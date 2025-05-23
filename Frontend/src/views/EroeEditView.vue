<template>
  <div class="eroe-edit-outer">
    <div class="eroe-edit-container">
      <h1 class="edit-title">{{ isEdit ? 'Modifica Eroe' : 'Crea Eroe' }}</h1>
      <form @submit.prevent="handleSubmit">
        <div class="form-group">
          <label for="nome">Nome</label>
          <input v-model="form.nome" id="nome" required maxlength="20" placeholder="Nome eroe" />
        </div>
        <div class="form-group">
          <label for="difficolta">Difficoltà</label>
          <input v-model.number="form.difficolta" id="difficolta" type="number" min="1" max="5" required placeholder="1-5" />
        </div>
        <div class="form-group">
          <label for="classe">Classe</label>
          <select v-model="form.cla_id" id="classe" required class="dropdown-classi">
            <option value="" disabled>Seleziona classe</option>
            <option v-for="c in classi" :key="c.cla_id" :value="c.cla_id">{{ c.cla_nome }}</option>
          </select>
        </div>
        <div class="form-group">
          <label for="image">Immagine</label>
          <div class="img-input-row">
            <input type="file" accept="image/*" @change="onFileChange" class="img-file-input" />
            <button type="button" class="img-generate-btn" @click="generateImage">Genera</button>
          </div>
          <div v-if="selectedFileName || generatedPath" class="img-path-preview">
            <span v-if="selectedFileName">File selezionato: {{ selectedFileName }}</span>
            <span v-else-if="generatedPath">Immagine generata: {{ generatedPath }}</span>
          </div>
        </div>
        <div class="edit-btns">
          <button type="submit" class="admin-btn">{{ isEdit ? 'Salva Modifiche' : 'Crea Eroe' }}</button>
          <button v-if="isEdit" type="button" class="admin-btn delete" @click="handleDelete">Elimina Eroe</button>
          <button type="button" class="admin-btn cancel" @click="() => router.push('/eroi')">Annulla</button>
        </div>
      </form>
      <div v-if="message" :class="['message', messageType]">{{ message }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const isEdit = ref(!!route.params.nome)
const form = ref({ nome: '', difficolta: 1, cla_id: '', image: '' })
const classi = ref([])
const message = ref('')
const messageType = ref('')
const selectedFileName = ref('')
const generatedPath = ref('')
const classiLoaded = ref(false)
const eroeLoaded = ref(false)

async function fetchClassi() {
  const res = await fetch('http://localhost/BigBlackDeath/backend/Classi/getClassi.php')
  classi.value = await res.json()
  classiLoaded.value = true
  setClasseIfReady()
}

async function fetchEroe() {
  if (!isEdit.value) return
  const res = await fetch(`http://localhost/BigBlackDeath/backend/Eroi/getEroe.php?nome=${encodeURIComponent(route.params.nome)}`)
  const data = await res.json()
  form.value = {
    nome: data.nome || data.ero_nome,
    difficolta: data.difficolta || data.ero_difficolta,
    cla_id: '', // lo settiamo dopo
    image: data.img || data.ero_image || ''
  }
  eroeLoaded.value = true
  form._cla_id_temp = (data.cla_id || data.ero_cla_id)?.toString() || ''
  setClasseIfReady()
}

function setClasseIfReady() {
  if (classiLoaded.value && eroeLoaded.value && form._cla_id_temp) {
    form.value.cla_id = form._cla_id_temp
    delete form.value._cla_id_temp
  }
}

function onFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    selectedFileName.value = file.name
    // Salva solo il nome file o il path relativo (simulato)
    form.value.image = `/images/heroes/${file.name}`
    generatedPath.value = ''
  }
}

function generateImage() {
  // Simula la generazione di un'immagine (puoi collegare qui una vera API)
  const fakeName = form.value.nome ? form.value.nome.replace(/\s+/g, '_').toLowerCase() : 'eroe_generato'
  const path = `/images/heroes/generated_${fakeName}.png`
  form.value.image = path
  generatedPath.value = path
  selectedFileName.value = ''
}

async function handleSubmit() {
  const url = isEdit.value
    ? `http://localhost/BigBlackDeath/backend/Eroi/updateEroe.php`
    : `http://localhost/BigBlackDeath/backend/Eroi/createEroe.php`
  const body = { ...form.value }
  if (isEdit.value) body.nome_originale = route.params.nome
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body)
  })
  const result = await res.json()
  message.value = result.success ? 'Operazione riuscita!' : (result.error || 'Errore')
  messageType.value = result.success ? 'success' : 'error'
  if (result.success) {
    if (isEdit.value) {
      setTimeout(() => router.push('/eroi'), 1000)
    } else {
      // Reset form dopo creazione
      form.value = { nome: '', difficolta: 1, cla_id: '', image: '' }
      selectedFileName.value = ''
      generatedPath.value = ''
      setTimeout(() => message.value = '', 1500)
    }
  }
}

async function handleDelete() {
  if (!confirm('Sei sicuro di voler eliminare questo eroe?')) return
  const res = await fetch(`http://localhost/BigBlackDeath/backend/Eroi/deleteEroe.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ nome: route.params.nome })
  })
  const result = await res.json()
  message.value = result.success ? 'Eroe eliminato!' : (result.error || 'Errore')
  messageType.value = result.success ? 'success' : 'error'
  if (result.success) setTimeout(() => router.push('/eroi'), 1000)
}

onMounted(() => {
  fetchClassi()
  fetchEroe()
})
</script>

<style scoped>
.eroe-edit-outer {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #071b13;
}
.eroe-edit-container {
  max-width: 420px;
  width: 100%;
  margin: 2rem auto;
  background: #09351e;
  border-radius: 2rem;
  padding: 2.5rem 2rem 2rem 2rem;
  box-shadow: 0 4px 32px #0007;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.edit-title {
  font-size: 2.2rem;
  color: #19d86b;
  font-weight: bold;
  margin-bottom: 2.2rem;
  text-align: center;
  letter-spacing: 1px;
}
.form-group {
  margin-bottom: 1.3rem;
  display: flex;
  flex-direction: column;
  width: 100%;
  align-items: center;
}
label {
  font-weight: bold;
  margin-bottom: 0.3rem;
  color: #b6e2c6;
  font-size: 1.1rem;
  align-self: flex-start;
}
input, select {
  padding: 0.8rem 1rem;
  border-radius: 0.7rem;
  border: none;
  font-size: 1.1rem;
  background: #0d2b1a;
  color: #fff;
  margin-top: 0.1rem;
  width: 100%;
  max-width: 350px;
  box-sizing: border-box;
}
input::placeholder, select:invalid {
  color: #7fd6a7;
  opacity: 1;
}
.dropdown-classi {
  background: #0d2b1a;
  color: #fff;
  font-size: 1.1rem;
}
.img-input-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  max-width: 350px;
}
.img-file-input {
  background: #0d2b1a;
  color: #fff;
  border: none;
  border-radius: 0.7rem;
  font-size: 1rem;
  padding: 0.3rem 0.5rem;
  width: 48%;
}
.img-generate-btn {
  background: #1b7c3a;
  color: #fff;
  border: none;
  border-radius: 0.7rem;
  padding: 0.5rem 1rem;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
  white-space: nowrap;
  width: 48%;
}
.img-generate-btn:hover {
  background: #19d86b;
}
.img-path-preview {
  margin-top: 0.5rem;
  color: #b6e2c6;
  font-size: 1rem;
  max-width: 350px;
  word-break: break-all;
}
.edit-btns {
  display: flex;
  gap: 1.2rem;
  margin-top: 1.5rem;
  justify-content: center;
  width: 100%;
  max-width: 350px;
}
.admin-btn {
  background: #1b7c3a;
  color: #fff;
  border: none;
  border-radius: 0.7rem;
  padding: 0.8rem 1.5rem;
  font-size: 1.1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
  width: 100%;
  max-width: 150px;
}
.admin-btn.delete,
.admin-btn.cancel {
  display: v-bind('isEdit ? "inline-block" : "none"');
}
.admin-btn:hover {
  filter: brightness(1.1);
}
.message {
  margin-top: 2rem;
  font-weight: bold;
  font-size: 1.2rem;
  padding: 0.7rem 1.2rem;
  border-radius: 1rem;
  text-align: center;
}
.message.success {
  background: #19d86b33;
  color: #19d86b;
}
.message.error {
  background: #e74c3c33;
  color: #e74c3c;
}
</style> 