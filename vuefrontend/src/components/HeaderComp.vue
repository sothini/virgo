<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()
const showLogoutConfirm = ref(false)

const userLabel = computed(() => authStore.user?.name || authStore.user?.email || 'Guest')

const openLogoutModal = () => {
  showLogoutConfirm.value = true
}

const cancelLogout = () => {
  showLogoutConfirm.value = false
}

const confirmLogout = async () => {
  await authStore.logout()
  router.push('/')
  showLogoutConfirm.value = false
}
</script>

<template>
  <div>
    <header class="w-full bg-gradient-to-r from-blue-50 to-indigo-100 border-b border-indigo-100">
      <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="text-xl font-semibold text-indigo-700 tracking-wide">Virgosoft</div>
        <div class="flex items-center gap-4">
          <span class="text-sm font-medium text-gray-800">Signed in as {{ userLabel }}</span>
          <button
            type="button"
            @click="openLogoutModal"
            class="px-4 py-2 rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Logout
          </button>
        </div>
      </div>
    </header>

    <div
      v-if="showLogoutConfirm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
    >
      <div class="w-full max-w-sm rounded-lg bg-white shadow-lg">
        <div class="px-6 py-4 border-b">
          <h3 class="text-lg font-semibold text-gray-900">Confirm logout</h3>
          <p class="mt-1 text-sm text-gray-600">Are you sure you want to log out?</p>
        </div>
        <div class="px-6 py-4 flex justify-end gap-3">
          <button
            type="button"
            @click="cancelLogout"
            class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300"
          >
            Cancel
          </button>
          <button
            type="button"
            @click="confirmLogout"
            class="px-4 py-2 rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Logout
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
