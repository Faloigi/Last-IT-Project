import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../views/HomeView.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
    },
    {
      path: '/sendprompt',
      name: 'sendprompt',
      component: () => import('../views/PromptView.vue'),
    },
    {
      path: '/stats',
      name: 'stats',
      component: () => import('../views/StatsView.vue'),
    },
    {
      path: '/heroes',
      name: 'heroes',
      component: () => import('../views/HeroesView.vue'),
    },
    {
      path: '/player',
      name: 'player',
      component: () => import('../views/PlayerView.vue'),
    },
  ],
})

export default router
