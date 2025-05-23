import EroiView from '@/views/EroiView.vue'
import EroeView from '@/views/EroeView.vue'
import HomeView from '@/views/HomeView.vue'
import LoginView from '@/views/LoginView.vue'
import PlayerView from '@/views/PlayerView.vue'
import StatsView from '@/views/StatsView.vue'
import PartitaView from '@/views/PartitaView.vue'
import ClanView from '@/views/ClanView.vue'
import EroeEditView from '@/views/EroeEditView.vue'
import AdminCreaEroeView from '@/views/ADMIN/CreaEroeView.vue'
import AdminEroeEditView from '@/views/ADMIN/EroeEditView.vue'
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
    {
      path: '/clan/:nome',
      name: 'clan',
      component: ClanView
    },
    {
      path: '/eroi/modifica/:nome',
      name: 'modifica-eroe',
      component: EroeEditView
    },
    // ADMIN routes
    {
      path: '/admin/eroi/crea',
      name: 'admin-crea-eroe',
      component: AdminCreaEroeView,
      meta: { requiresAdmin: true }
    },
    {
      path: '/admin/eroi/modifica/:nome',
      name: 'admin-modifica-eroe',
      component: AdminEroeEditView,
      meta: { requiresAdmin: true }
    },
  ],
})

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAdmin) {
    const userRole = localStorage.getItem('userRole')
    if (userRole !== 'admin') {
      // Puoi mostrare un messaggio o reindirizzare dove vuoi
      return next({ name: 'home' })
    }
  }
  next()
})

export default router
