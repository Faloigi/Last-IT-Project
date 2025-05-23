import EroiView from '@/views/EroiView.vue'
import EroeView from '@/views/EroeView.vue'
import HomeView from '@/views/HomeView.vue'
import LoginView from '@/views/LoginView.vue'
import PlayerView from '@/views/PlayerView.vue'
import StatsView from '@/views/StatsView.vue'
import PartitaView from '@/views/PartitaView.vue'
import ClanView from '@/views/ClanView.vue'
import ClansView from '@/views/ClansView.vue'
import AdminCreaEroeView from '@/views/ADMIN/CreaEroeView.vue'
import AdminEroeEditView from '@/views/ADMIN/EroeEditView.vue'
import AdminCreaClanView from '@/views/ADMIN/CreaClanView.vue'
import AdminClanEditView from '@/views/ADMIN/ClanEditView.vue'
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
      path: '/clans',
      name: 'clans',
      component: ClansView
    },
    // ADMIN routes
    {
      path: '/eroi/modifica/:nome',
      name: 'modifica-eroe',
      component: AdminEroeEditView,
      meta: { requiresAdmin: true }
    },
    {
      path: '/admin/eroi/crea',
      name: 'admin-crea-eroe',
      component: AdminCreaEroeView,
      meta: { requiresAdmin: true }
    },
    {
      path: '/clan/modifica/:nome',
      name: 'modifica-clan',
      component: AdminClanEditView,
      meta: { requiresAdmin: true }
    },
    {
      path: '/admin/clan/crea',
      name: 'admin-crea-clan',
      component: AdminCreaClanView,
      meta: { requiresAdmin: true }
    },
  ],
})

router.beforeEach((to, from, next) => {
  let userRole = null
  const user = localStorage.getItem('user')
  if (user) {
    try {
      userRole = JSON.parse(user).ute_ruolo
    } catch {}
  }
  if (to.meta.requiresAdmin && userRole !== 'admin') {
    return next({ name: 'home' })
  }
  next()
})

export default router
