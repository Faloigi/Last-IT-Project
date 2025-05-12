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
  <router-view />
</template>

