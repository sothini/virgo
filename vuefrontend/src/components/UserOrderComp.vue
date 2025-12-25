<script setup lang="ts">
import { useOrderStore } from '../stores/order'
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'

const orderStore = useOrderStore()
const { userOrders } = storeToRefs(orderStore)
const router = useRouter()
const activeTab = ref<'open' | 'filled' | 'cancelled'>('open')
const showCancelModal = ref(false)
const orderToCancel = ref<number | null>(null)

const displayedOrders = computed(() => {
  if (activeTab.value === 'open') {
    return userOrders.value.filter((order) => order.status === 1)
  }
  if (activeTab.value === 'filled') {
    return userOrders.value.filter((order) => order.status === 2)
  }
  return userOrders.value.filter((order) => order.status === 3)
})

const orderToCancelDetails = computed(() => {
  if (!orderToCancel.value) return null
  return userOrders.value.find((order) => order.id === orderToCancel.value)
})

const openCancelModal = (orderId: number) => {
  orderToCancel.value = orderId
  showCancelModal.value = true
}

const closeCancelModal = () => {
  showCancelModal.value = false
  orderToCancel.value = null
}

const confirmCancelOrder = async () => {
  if (!orderToCancel.value) return

  const success = await orderStore.cancelOrder(orderToCancel.value)
  if (success) {
    await orderStore.fetchUserOrders()
    closeCancelModal()
  }
}

onMounted(async () => {
  await orderStore.fetchUserOrders()
})
</script>

<template>
  <main class="max-w-6xl mx-auto px-4 py-10 space-y-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">My Orders</h2>
          <p class="mt-1 text-sm text-gray-600">Your latest buy and sell orders.</p>
        </div>
        <div class="flex items-center gap-4">
          <span class="text-sm text-gray-500">{{ displayedOrders.length }} orders</span>
          <button
            @click="router.push('/orders')"
            class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors"
          >
            Create Order
          </button>
        </div>
      </div>

      <!-- Tabs -->
      <div class="mt-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
          <button
            @click="activeTab = 'open'"
            :class="
              activeTab === 'open'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
          >
            Open
          </button>
          <button
            @click="activeTab = 'filled'"
            :class="
              activeTab === 'filled'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
          >
            Filled
          </button>
          <button
            @click="activeTab = 'cancelled'"
            :class="
              activeTab === 'cancelled'
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            "
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
          >
            Cancelled
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
              <th
                v-if="activeTab === 'open'"
                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase"
              >
                Actions
              </th>
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
              <td v-if="activeTab === 'open'" class="px-4 py-3 text-sm">
                <button
                  @click="openCancelModal(order.id)"
                  class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors"
                >
                  Cancel
                </button>
              </td>
            </tr>

            <tr v-if="displayedOrders.length === 0">
              <td
                :colspan="activeTab === 'open' ? 7 : 6"
                class="px-4 py-6 text-center text-sm text-gray-500"
              >
                No trades found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div
      v-if="showCancelModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
    >
      <div class="w-full max-w-lg rounded-lg bg-white shadow-lg">
        <div class="px-6 py-4 border-b">
          <div class="flex items-start">
            <div
              class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-red-100"
            >
              <svg
                class="h-6 w-6 text-red-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                />
              </svg>
            </div>
            <div class="ml-4 flex-1">
              <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Cancel Order</h3>
              <p class="mt-1 text-sm text-gray-600">Are you sure you want to cancel this order?</p>
              <div v-if="orderToCancelDetails" class="mt-4 p-3 bg-gray-50 rounded-lg">
                <div class="text-sm space-y-1">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Symbol:</span>
                    <span class="font-medium text-gray-900">{{ orderToCancelDetails.symbol }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Side:</span>
                    <span
                      :class="
                        orderToCancelDetails.side === 'buy'
                          ? 'text-green-700 font-medium'
                          : 'text-red-700 font-medium'
                      "
                    >
                      {{ orderToCancelDetails.side.toUpperCase() }}
                    </span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Price:</span>
                    <span class="font-medium text-gray-900">
                      ${{ orderToCancelDetails.price?.toLocaleString() }}
                    </span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Amount:</span>
                    <span class="font-medium text-gray-900">{{ orderToCancelDetails.amount }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="px-6 py-4 flex justify-end gap-3">
          <button
            type="button"
            @click="closeCancelModal"
            class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300"
          >
            Keep Order
          </button>
          <button
            type="button"
            @click="confirmCancelOrder"
            class="px-4 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            Cancel Order
          </button>
        </div>
      </div>
    </div>
  </main>
</template>
