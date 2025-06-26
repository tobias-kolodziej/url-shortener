import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import ListView from '@/views/ListView.vue'
import StatsView from '@/views/StatsView.vue'
import NotFoundView from '@/views/NotFoundView.vue'

const routes = [
  { path: '/', component: HomeView },
  { path: '/urls', component: ListView },
  { path: '/stats/:code', component: StatsView },
  { path: '/not-found', component: NotFoundView },
  { path: '/:pathMatch(.*)*', redirect: '/not-found' },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
