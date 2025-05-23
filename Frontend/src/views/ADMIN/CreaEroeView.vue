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
          <div v-if="form.image" class="img-preview-box">
            <img :src="getHeroImgSrc(form.image)" alt="Immagine eroe" class="img-preview" @error="onImageError" />
          </div>
          <div class="img-input-row">
            <label class="custom-file-label">
              <input type="file" accept="image/*" @change="onFileChange" style="display:none" />
              <span>{{ selectedFileName || 'Carica immagine' }}</span>
            </label>
            <button type="button" class="img-generate-btn" @click="generateImage">Genera</button>
          </div>
          <div v-if="selectedFileName || generatedPath" class="img-path-preview">
            <span v-if="selectedFileName">File selezionato: {{ selectedFileName }}</span>
            <span v-else-if="generatedPath">Immagine generata: {{ generatedPath }}</span>
          </div>
          <div v-if="imageError" class="img-path-preview" style="color:#e74c3c">Immagine non trovata</div>
        </div>
        <div class="edit-btns">
          <button type="submit" class="admin-btn" :disabled="isSubmitting">Crea Eroe</button>
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
const selectedFile = ref(null)
const imageError = ref(false)
const isSubmitting = ref(false)

async function fetchClassi() {
  const res = await fetch('http://localhost/BigBlackDeath/backend/Classi/getClassi.php')
  classi.value = await res.json()
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
    generatedPath.value = ''
    imageError.value = false
  }
}

function onImageError() {
  imageError.value = true
}

function generateImage() {
  const fakeName = form.value.nome ? form.value.nome.replace(/\s+/g, '_').toLowerCase() : 'eroe_generato'
  const path = `/images/heroes/generated_${fakeName}.png`
  form.value.image = path
  generatedPath.value = path
  selectedFileName.value = ''
}

function getHeroImgSrc(img) {
  if (!img) return ''
  if (img.startsWith('blob:')) return img
  if (img.startsWith('/images/heroes/')) return img
  return `/images/heroes/${img}`
}

async function uploadImage() {
  if (!selectedFile.value) return null
  const formData = new FormData()
  formData.append('image', selectedFile.value)
  formData.append('nomeEroe', form.value.nome)
  const res = await fetch('http://localhost/BigBlackDeath/backend/Eroi/uploadEroeImage.php', {
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
    generatedPath.value = ''
    imageError.value = false
    message.value = 'Immagine aggiornata!'
    messageType.value = 'success'
    setTimeout(() => { message.value = '' }, 1500)
    return data.filename
  } else {
    message.value = data.error || 'Errore upload immagine'
    messageType.value = 'error'
    return null
  }
}

async function handleSubmit() {
  if (!form.value.nome || !form.value.difficolta || !form.value.cla_id) {
    message.value = 'Compila tutti i campi obbligatori';
    messageType.value = 'error';
    return;
  }
  if (isSubmitting.value) return;
  isSubmitting.value = true;
  let imageFileName = form.value.image
  if (selectedFile.value) {
    const uploaded = await uploadImage()
    if (!uploaded) { isSubmitting.value = false; return }
    imageFileName = uploaded
    form.value.image = imageFileName
  } else if (generatedPath.value) {
    imageFileName = generatedPath.value.split('/').pop()
  } else if (form.value.image && form.value.image.startsWith('blob:')) {
    imageFileName = ''
  } else if (form.value.image) {
    imageFileName = form.value.image.split('/').pop()
  }
  const url = `http://localhost/BigBlackDeath/backend/Eroi/createEroe.php`
  const body = { ...form.value, image: imageFileName }
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body)
  })
  const result = await res.json()
  message.value = result.success ? 'Eroe creato!' : (result.error || 'Errore')
  messageType.value = result.success ? 'success' : 'error'
  if (result.success) {
    if (form.value.image && form.value.image.startsWith('blob:')) {
      URL.revokeObjectURL(form.value.image)
    }
    setTimeout(() => router.push('/eroi'), 800)
  }
  isSubmitting.value = false
}

onMounted(() => {
  fetchClassi()
})
</script>

<style scoped>
.eroe-edit-outer {
  min-height: 100vh;
  background: linear-gradient(135deg, #071b13 70%, #09351e 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding-top: 80px;
  overflow-x: hidden;
}
.eroe-edit-container {
  background: #0e2d1e;
  border-radius: 2.2rem;
  box-shadow: 0 8px 40px #000a;
  padding: 1.7rem 2rem 1.2rem 2rem;
  width: 480px;
  max-width: 100vw;
  color: #fff;
  display: flex;
  flex-direction: column;
  align-items: center;
  box-sizing: border-box;
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
.dropdown-classi {
  width: 100%;
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
.img-generate-btn {
  background: #19d86b;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 0.5rem 1rem;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s, filter 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 8px #19d86b33;
}
.img-generate-btn:hover {
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
.admin-btn.delete {
  background: #e74c3c;
  box-shadow: 0 2px 12px #e74c3c33, 0 1.5px 8px #0002;
}
.admin-btn.delete:hover {
  background: #c0392b;
  box-shadow: 0 4px 24px #e74c3c55, 0 2px 12px #0003;
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
@media (max-width: 800px) {
  .eroe-edit-container {
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
