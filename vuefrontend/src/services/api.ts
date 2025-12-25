import { useAuthStore } from '@/stores/auth'
import axios, { type AxiosInstance, type AxiosError } from 'axios'

// Base URL for the API
const BASE_URL = 'http://localhost:8000'

// Create axios instance with default config
const apiClient: AxiosInstance = axios.create({
  baseURL: BASE_URL,
  withCredentials: true, // Required for Sanctum (sends cookies)
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

// Request interceptor to handle CSRF token if needed
apiClient.interceptors.request.use(
  (config) => {
    // Sanctum typically uses cookies for CSRF, but if you need to set X-XSRF-TOKEN header
    // you can get it from cookies here
    const csrfToken = document.cookie
      .split('; ')
      .find((row) => row.startsWith('XSRF-TOKEN='))
      ?.split('=')[1]

    if (csrfToken) {
      config.headers['X-XSRF-TOKEN'] = decodeURIComponent(csrfToken)
    }

    return config
  },
  (error) => {
    return Promise.reject(error)
  },
)


// API service functions
export const apiService = {
  async getCsrfCookie() {
    return await apiClient.get('/sanctum/csrf-cookie')
  },

  // Login function
  async login(email: string, password: string) {
    await this.getCsrfCookie()
    const response = await apiClient.post('/api/login', {
      email,
      password,
    })
    return response.data
  },

  // Logout function
  async logout() {
    await this.getCsrfCookie()
    const response = await apiClient.post('/api/logout')
    return response.data
  },

 // Fetch authenticated user profile and assets
 async profile() {
  await this.getCsrfCookie()
  const response = await apiClient.get('/api/profile')
  return response.data
},

async user_orders() {
  await this.getCsrfCookie()
  const response = await apiClient.get('/api/user_orders')
  return response.data
},

async orders() {
  await this.getCsrfCookie()
  const response = await apiClient.get('/api/orders')
  return response.data
},

}

export default apiClient
