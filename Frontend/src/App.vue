<script setup lang="tsx">
import { ref } from 'vue';
import MainHeader from './components/MainHeader.vue';
import axios from 'axios';

const message = ref('');

const sendMessage = async () => {
  try {
    const response = await axios.post('http://localhost/LastItProject/api.php', {
      message: message.value
    });
    console.log(response.data);
  } catch (error) {
    console.error(error);
  }
};
</script>

<template>
  <MainHeader />
  <router-view v-slot="{ Component }">
    <transition name="fade" mode="out-in">
      <component :is="Component" />
    </transition>
  </router-view>
</template>

<style>
.fade-enter-active {
  transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-leave-active {
  transition: opacity 0.7s cubic-bezier(0.4, 0, 0.2, 1), transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-enter-from {
  opacity: 0;
  transform: scale(0.98);
}
.fade-enter-to {
  opacity: 1;
  transform: scale(1);
}
.fade-leave-from {
  opacity: 1;
  transform: scale(1);
}
.fade-leave-to {
  opacity: 0;
  transform: scale(0.97);
}
</style>

