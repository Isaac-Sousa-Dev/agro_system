import axios from 'axios'
import Cookies from 'js-cookie'

// Create axios instance
const api = axios.create({
  baseURL: 'http://localhost/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  }
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Add auth token to requests
    const token = Cookies.get('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    
    // Add CSRF token if needed
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (csrfToken) {
      config.headers['X-CSRF-TOKEN'] = csrfToken
    }

    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response
  },
  async (error) => {
    const originalRequest = error.config

    // Handle 401 Unauthorized
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true

      // Clear invalid token
      Cookies.remove('token')
      
      // Redirect to login
      if (window.location.pathname !== '/login') {
        window.location.href = '/login'
      }
    }

    // Handle 419 CSRF token mismatch
    if (error.response?.status === 419) {
      // Refresh CSRF token and retry
      try {
        await api.get('/csrf-cookie')
        return api(originalRequest)
      } catch (refreshError) {
        return Promise.reject(refreshError)
      }
    }

    // Handle network errors
    if (!error.response) {
      error.message = 'Erro de conexão. Verifique sua internet.'
    }

    return Promise.reject(error)
  }
)

export default api
