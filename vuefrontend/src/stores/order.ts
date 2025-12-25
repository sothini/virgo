import { useApiError } from '@/composables/apiErrorHandler'
import { apiService } from '@/services/api'
import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { UserProfile } from './user'

const { error, errors, reset, handle } = useApiError()

export interface OrderForm {
  symbol: string
  side: 'buy' | 'sell'
  price: number | null
  amount: number | null
}

export interface Order extends OrderForm {
  id: number
  user_id: number
  status: number
  created_at: string
  updated_at: string
  user: UserProfile | null
}

export interface Symbol {
  name: string
  value: string
}

export const useOrderStore = defineStore('orders', () => {
  const orders = ref<Order[]>([])
  const userOrders = ref<Order[]>([])
  const symbols = ref<Symbol[]>([])
  const loading = ref(false)

  async function fetchUserOrders() {
    loading.value = true
    reset()
    try {
      const response = await apiService.user_orders()
      userOrders.value = response
    } catch (err) {
      handle(err)
    } finally {
      loading.value = false
    }
  }

  async function fetchOrders() {
    loading.value = true
    reset()
    try {
      const response = await apiService.orders()
      orders.value = response
    } catch (err) {
      handle(err)
    } finally {
      loading.value = false
    }
  }

  async function createOrder(payload: OrderForm) {
    loading.value = true
    reset()

    try {
      await apiService.create_order(payload)
      return true
    } catch (err) {
      handle(err)
      return false
    } finally {
      loading.value = false
    }
  }

  async function cancelOrder(orderId: number) {
    loading.value = true
    error.value = null
    try {
      await apiService.cancel_order(orderId)
      return true
    } catch (err) {
      handle(err)
    } finally {
      loading.value = false
    }
  }

  async function fetchSymbols() {
    loading.value = true
    reset()
    try {
      const response = await apiService.symbols()
      symbols.value = response
    } catch (err) {
      handle(err)
    } finally {
      loading.value = false
    }
  }

  function clearOrders() {
    orders.value = []
    userOrders.value = []
    symbols.value = []
    loading.value = false
    reset()
  }

  return {
    error,
    errors,
    orders,
    userOrders,
    symbols,
    fetchUserOrders,
    fetchOrders,
    createOrder,
    cancelOrder,
    fetchSymbols,
    clearOrders,
  }
})
