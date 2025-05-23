<template>
  <div class="eroe-edit-outer">
    <div class="eroe-edit-container">
      <h1 class="edit-title">Crea Eroe</h1>
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
          <button type="submit" class="admin-btn">Crea Eroe</button>
          <button type="button" class="admin-btn cancel" @click="() => router.push('/eroi')">Annulla</button>
        </div>
      </form>
      <div v-if="message" :class="['message', messageType]">{{ message }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const form = ref({ nome: '', difficolta: 1, cla_id: '', image: '' })
const classi = ref([])
const message = ref('')
const messageType = ref('')
const selectedFileName = ref('')
const generatedPath = ref('')

async function fetchClassi() {
  const res = await fetch('http://localhost/BigBlackDeath/backend/Classi/getClassi.php')
  classi.value = await res.json()
}

function onFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    selectedFileName.value = file.name
    form.value.image = `/images/heroes/${file.name}`
    generatedPath.value = ''
  }
}

function generateImage() {
  const fakeName = form.value.nome ? form.value.nome.replace(/\s+/g, '_').toLowerCase() : 'eroe_generato'
  const path = `/images/heroes/generated_${fakeName}.png`
  form.value.image = path
  generatedPath.value = path
  selectedFileName.value = ''
}

async function handleSubmit() {
  const url = `http://localhost/BigBlackDeath/backend/Eroi/createEroe.php`
  const body = { ...form.value }
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body)
  })
  const result = await res.json()
  message.value = result.success ? 'Eroe creato!' : (result.error || 'Errore')
  messageType.value = result.success ? 'success' : 'error'
  if (result.success) {
    form.value = { nome: '', difficolta: 1, cla_id: '', image: '' }
    selectedFileName.value = ''
    generatedPath.value = ''
    setTimeout(() => message.value = '', 1500)
  }
}

onMounted(() => {
  fetchClassi()
})
</script>

<style scoped>
</style> 