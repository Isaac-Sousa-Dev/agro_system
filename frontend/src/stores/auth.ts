import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import Cookies from 'js-cookie'
import api from '@/services/api'
import type { User, LoginCredentials } from '@/types/auth'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<User | null>(null)
  const token = ref<string | null>(Cookies.get('token') || null)
  const loading = ref(false)
  const error = ref<string | null>(null)


  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const userInitials = computed(() => {
    if (!user.value) return 'U'
    return user.value.name
      .split(' ')
      .map(name => name.charAt(0))
      .join('')
      .toUpperCase()
      .slice(0, 2)
  })

  // Actions
  const login = async (credentials: LoginCredentials) => {
    loading.value = true
    error.value = null

    try {
      const response = await api.post('/auth/login', credentials)
      const { token: authToken, user: userData } = response.data

      // Store token in cookie
      Cookies.set('token', authToken, {
        expires: 7, // 7 days
        secure: true,
        sameSite: 'strict'
      })

      // Store in state
      token.value = authToken
      user.value = userData

      // Set default authorization header
      api.defaults.headers.common['Authorization'] = `Bearer ${authToken}`

      return { success: true }
    } catch (err: unknown) {
      const axiosError = err as { response?: { data?: { message?: string } } }
      error.value = axiosError?.response?.data?.message || 'Erro ao fazer login'
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    try {
      // Call logout endpoint if needed
      if (token.value) {
        await api.post('/auth/logout')
      }
    } catch (err) {
      console.warn('Erro ao fazer logout no servidor:', err)
    } finally {
      // Clear local data
      Cookies.remove('token')
      token.value = null
      user.value = null
      delete api.defaults.headers.common['Authorization']
    }
  }

  const fetchUser = async () => {
    if (!token.value) return false

    try {
      api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      const response = await api.get('/auth/me')
      user.value = response.data
      return true
    } catch (err) {
      // Token is invalid, clear it
      console.log(err)
      await logout()
      return false
    }
  }

  const clearError = () => {
    error.value = null
  }

  return {
    // State
    user,
    token,
    loading,
    error,

    // Getters
    isAuthenticated,
    userInitials,

    // Actions
    login,
    logout,
    fetchUser,
    clearError
  }
})
