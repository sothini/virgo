<script setup lang="ts">
import { useOrderStore } from '../stores/order'
import { computed, onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'

const orderStore = useOrderStore()
const { orders } = storeToRefs(orderStore)
const activeTab = ref<'buy' | 'sell'>('buy')

const displayedOrders = computed(() => {
  return orders.value.filter((order) => order.side === activeTab.value)
})

onMounted(async () => {
  await orderStore.fetchOrders()
})
</script>

<template>
  <main class="max-w-6xl mx-auto px-4 py-10 space-y-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">Open Orders</h2>
          <p class="mt-1 text-sm text-gray-600">The latest open buy and sell orders.</p>
        </div>
        <div class="flex items-center gap-4">
          <span class="text-sm text-gray-500">{{ displayedOrders.length }} orders</span>
        </div>
      </div>

      <!-- Tabs -->
      <div class="mt-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
          <button
            @click="activeTab = 'buy'"
            :class="
              activeTab === 'buy'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
          >
            Buy
          </button>
          <button
            @click="activeTab = 'sell'"
            :class="
              activeTab === 'sell'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
          >
            Sell
          </button>
        </nav>
      </div>

      <div class="mt-6 overflow-x-auto">
        <table
          :key="activeTab"
          class="min-w-full border border-gray-100 rounded-lg overflow-hidden"
        >
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Symbol
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Side</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                Price (USD)
              </th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                Amount
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Status
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100 bg-white">
            <tr
              v-for="order in displayedOrders"
              :key="order.id"
              class="hover:bg-gray-50 transition"
            >
              <td class="px-4 py-3 text-sm font-medium text-gray-900">
                {{ order.symbol }}
              </td>

              <td class="px-4 py-3 text-sm">
                <span
                  :class="
                    order.side === 'buy' ? 'text-green-700 bg-green-50' : 'text-red-700 bg-red-50'
                  "
                  class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                >
                  {{ order.side.toUpperCase() }}
                </span>
              </td>

              <td class="px-4 py-3 text-sm font-medium text-gray-900">
                {{ order.user?.name || 'NA' }}
              </td>

              <td class="px-4 py-3 text-sm text-gray-900 text-right">
                {{ order.price?.toLocaleString() }}
              </td>

              <td class="px-4 py-3 text-sm text-gray-900 text-right">
                {{ order.amount }}
              </td>

              <td class="px-4 py-3 text-sm">
                <span
                  :class="
                    order.status === 1
                      ? 'text-indigo-700 bg-indigo-50'
                      : order.status === 2
                        ? 'text-green-700 bg-green-50'
                        : 'text-gray-600 bg-gray-100'
                  "
                  class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                >
                  {{ order.status === 1 ? 'Open' : order.status === 2 ? 'Filled' : 'Cancelled' }}
                </span>
              </td>

              <td class="px-4 py-3 text-sm text-gray-600">
                {{ new Date(order.created_at).toLocaleDateString() }}
              </td>
            </tr>

            <tr v-if="displayedOrders.length === 0">
              <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">
                No trades found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</template>
