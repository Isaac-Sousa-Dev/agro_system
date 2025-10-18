import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

export function useAuth() {
  const authStore = useAuthStore()
  const router = useRouter()

  const login = async (credentials: { email: string; password: string }) => {
    const result = await authStore.login(credentials)

    if (result.success) {
      router.push('/dashboard')
    }

    return result
  }

  const logout = async () => {
    await authStore.logout()
    router.push('/login')
  }

  const requireAuth = () => {
    if (!authStore.isAuthenticated) {
      router.push('/login')
      return false
    }
    return true
  }

  return {
    // State
    user: computed(() => authStore.user),
    isAuthenticated: computed(() => authStore.isAuthenticated),
    loading: computed(() => authStore.loading),
    error: computed(() => authStore.error),
    // userInitials: computed(() => authStore.userInitials),

    // Actions
    login,
    logout,
    requireAuth,
    clearError: authStore.clearError
  }
}

