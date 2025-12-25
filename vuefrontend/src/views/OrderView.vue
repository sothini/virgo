<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useOrderStore, type OrderForm } from '../stores/order'
import { defineAsyncComponent, reactive, ref, onMounted } from 'vue'
import type { Component } from 'vue'

const HeaderComp = defineAsyncComponent(async () => {
  const module = (await import('../components/HeaderComp.vue')) as { default: Component }
  return module.default
})

const router = useRouter()
const orderStore = useOrderStore()

const isLoading = ref(false)
const error = ref('')

onMounted(async () => {
  await orderStore.fetchSymbols()
})

const formData = reactive<OrderForm>({
  symbol: 'BTC',
  side: 'buy',
  price: null,
  amount: null,
})

const handleSubmit = async () => {
  const result = await orderStore.createOrder(formData)

  if (result) router.push('/overview')
}
</script>
<template>
  <div class="min-h-screen bg-gray-50">
    <HeaderComp />
    <main class="min-h-screen flex items-center justify-center from-blue-50 to-indigo-100 px-4">
      <div class="bg-white rounded-xl shadow-lg p-8 max-w-md w-full">
        <div class="text-center">
          <h1 class="text-2xl font-semibold text-gray-900">Create Order</h1>
        </div>

        <div
          v-if="orderStore.error"
          class="mb-4 flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800"
          role="alert"
        >
          <span class="font-medium">{{ orderStore.error }}</span>
        </div>

        <div class="mt-6">
          <form @submit.prevent="handleSubmit" class="space-y-5">
            <div class="space-y-4">
              <div>
                <label for="symbol" class="block text-sm font-medium text-gray-700 mb-1"
                  >Symbol</label
                >
                <select
                  id="symbol"
                  v-model="formData.symbol"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white"
                >
                  <option
                    v-for="symbol in orderStore.symbols"
                    :key="symbol.value"
                    :value="symbol.value"
                  >
                    {{ symbol.name }}
                  </option>
                </select>
              </div>

              <div>
                <label for="side" class="block text-sm font-medium text-gray-700 mb-1">Side</label>
                <select
                  id="side"
                  v-model="formData.side"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white"
                >
                  <option value="buy">Buy</option>
                  <option value="sell">Sell</option>
                </select>
              </div>

              <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1"
                  >Price (USD)</label
                >
                <input
                  id="price"
                  v-model="formData.price"
                  type="number"
                  step="0.01"
                  required
                  placeholder="0.00"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                />
              </div>

              <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1"
                  >Amount</label
                >
                <input
                  id="amount"
                  v-model="formData.amount"
                  type="number"
                  step="0.000001"
                  required
                  placeholder="0.00"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                />
              </div>
            </div>

            <div
              v-if="error"
              class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm"
            >
              {{ error }}
            </div>

            <div class="flex gap-3">
              <button
                type="button"
                @click="router.push('/overview')"
                class="flex-1 flex justify-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition"
              >
                Back to Overview
              </button>
              <button
                type="submit"
                :disabled="isLoading || !formData.price || !formData.amount"
                class="flex-1 flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition"
              >
                <span v-if="isLoading">Placing Order...</span>
                <span v-else>Place Order</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped></style>
