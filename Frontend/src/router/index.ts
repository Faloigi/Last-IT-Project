import EroiView from '@/views/EroiView.vue'
import EroeView from '@/views/EroeView.vue'
import HomeView from '@/views/HomeView.vue'
import LoginView from '@/views/LoginView.vue'
import PlayerView from '@/views/PlayerView.vue'
import StatsView from '@/views/StatsView.vue'
import PartitaView from '@/views/PartitaView.vue'
import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView
    },
    {
      path: '/stats',
      name: 'stats',
      component: StatsView
    },
    {
      path: '/eroi',
      name: 'eroi',
      component: EroiView
    },
    {
      path: '/player/:username',
      name: 'player',
      component: PlayerView
    },
    {
      path: '/eroe/:nome',
      name: 'eroe',
      component: EroeView
    },
    {
      path: '/partita/:id',
      name: 'partita',
      component: PartitaView
    },
  ],
})

export default router
