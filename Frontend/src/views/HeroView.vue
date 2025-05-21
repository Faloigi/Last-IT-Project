<template>
  <div class="hero-container">
    <h1>{{ nome }}</h1>
    <!-- Qui puoi aggiungere dettagli, statistiche, ecc. -->
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'
const route = useRoute()
const nome = route.params.nome
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
</style>
