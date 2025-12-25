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



}

export default apiClient
