import { defineStore } from 'pinia'
import { ref } from 'vue'
import { apiService } from '@/services/api'

type UserInfo = {
  email?: string
  name?: string
  [key: string]: unknown
}

const STORAGE_KEY = 'virgovue_auth_user'

// Helper functions for localStorage
function loadUserFromStorage(): UserInfo | null {
  try {
    const stored = localStorage.getItem(STORAGE_KEY)
    if (stored) {
      return JSON.parse(stored) as UserInfo
    }
  } catch (error) {
    console.error('Error loading user from storage:', error)
  }
  return null
}

function saveUserToStorage(userData: UserInfo | null): void {
  try {
    if (userData) {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(userData))
    } else {
      localStorage.removeItem(STORAGE_KEY)
    }
  } catch (error) {
    console.error('Error saving user to storage:', error)
  }
}


export const useAuthStore = defineStore('auth', () => {
  // Initialize from localStorage if available
  const storedUser = loadUserFromStorage()
  const isAuthenticated = ref(!!storedUser)
  const user = ref<UserInfo | null>(storedUser)

  function setAuth(userData: UserInfo | null) {
    isAuthenticated.value = !!userData
    user.value = userData
    saveUserToStorage(userData)
  }

  async function logout() {
    try {
      // Call logout endpoint to invalidate session/token on server
      await apiService.logout()
    } catch (error) {
      // Even if logout endpoint fails, clear local auth state
      console.error('Error calling logout endpoint:', error)
    } finally {
      // Clear auth state and stored user data
      isAuthenticated.value = false
      user.value = null
      saveUserToStorage(null)

    }
  }

  // Clear auth data (useful for session expiration)
  function clearAuth() {
    // Clear auth state and stored user data
    isAuthenticated.value = false
    user.value = null
    saveUserToStorage(null)

   
  }

  return {
    isAuthenticated,
    user,
    setAuth,
    logout,
    clearAuth,
  }
})
