import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { apiService } from '@/services/api'

export type UserAsset = {
  id: number
  user_id: number
  symbol: string
  amount: number
  locked_amount: number
  created_at: string
  updated_at: string
}

export type UserProfile = {
  id: number
  name: string
  email: string
  balance: number
  email_verified_at: string | null
  created_at: string
  updated_at: string
  assets: UserAsset[]
}

export const useUserStore = defineStore('user', () => {
  const profile = ref<UserProfile | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const assets = computed(() => profile.value?.assets ?? [])

  async function fetchProfile() {
    loading.value = true
    error.value = null

    try {
      const data = await apiService.profile()
      profile.value = data
    } catch (err) {
      // Safely capture a human-friendly error message
      if (err instanceof Error && err.message) {
        error.value = err.message
      } else {
        error.value = 'Unable to load profile'
      }
    } finally {
      loading.value = false
    }
  }

  function clearProfile() {
    profile.value = null
    error.value = null
    loading.value = false
  }

  return {
    profile,
    assets,
    loading,
    error,
    fetchProfile,
    clearProfile,
  }
})
