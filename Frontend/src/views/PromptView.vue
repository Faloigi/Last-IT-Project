<template>
  <div class="chat-container">
    <div class="chat-messages">
      <div
        v-for="(msg, idx) in messages"
        :key="idx"
        :class="['chat-message', msg.sender]"
      >
        <span class="sender">{{ msg.sender === 'user' ? 'You' : 'Bot' }}:</span>
        <span class="text">{{ msg.text }}</span>
      </div>
    </div>
    <div class="chat-input">
      <textarea
        v-model="prompt"
        name="InputPromt"
        id="InputPromt"
        placeholder="Enter your prompt here"
        @keydown.enter.exact.prevent="sendPrompt"
      ></textarea>
      <button @click="sendPrompt">Send</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

interface Message {
  sender: 'user' | 'bot'
  text: string
}

const prompt = ref('')
const messages = ref<Message[]>([])

const sendPrompt = async () => {
  if (!prompt.value.trim()) return
  messages.value.push({ sender: 'user', text: prompt.value })
  const userPrompt = prompt.value
  prompt.value = ''
  try {
    const res = await fetch('http://127.0.0.1:5000', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ prompt: userPrompt }),
    })
    const data = await res.json()
    messages.value.push({ sender: 'bot', text: data.response })
  } catch (err) {
    messages.value.push({ sender: 'bot', text: 'Errore nella richiesta: ' + err })
  }
}
</script>

<style scoped>
.chat-container {
  display: flex;
  flex-direction: column;
  max-width: 800px;
  margin: 2rem auto;
  padding: 0 1rem;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 100%;
  height: 80vh;
  background: #000000;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  overflow-x: hidden;
  box-sizing: border-box;
  max-width: 100vw;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 1rem 0;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.chat-message {
  display: flex;
  flex-direction: column;
  max-width: 70%;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-size: 1rem;
  word-break: break-word;
}

.chat-message.user {
  align-self: flex-end;
  background: #e0f7fa;
  color: #006064;
}

.chat-message.bot {
  align-self: flex-start;
  background: #e8eaf6;
  color: #1a237e;
}

.sender {
  font-weight: bold;
  margin-bottom: 0.25rem;
}

.chat-input {
  display: flex;
  gap: 1rem;
  align-items: flex-end;
  margin-top: 1rem;
}

textarea {
  flex: 1;
  min-height: 60px;
  padding: 1rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-family: inherit;
  font-size: 1rem;
  resize: vertical;
}

button {
  padding: 0.75rem 1.5rem;
  background-color: #4caf50;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

button:hover {
  background-color: #45a049;
}
</style>
