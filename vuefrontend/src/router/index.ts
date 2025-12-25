import LoginView from '@/views/LoginView.vue'
import OverviewView from '@/views/OverviewView.vue'
import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/overview',
      name: 'overview',
      component: OverviewView,
    },
  ],
})

export default router
