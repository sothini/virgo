import LoginView from '@/views/LoginView.vue'
import OrderView from '@/views/OrderView.vue'
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
    {
      path: '/orders',
      name: 'orders',
      component: OrderView,
    },
  ],
})

export default router
