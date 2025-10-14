import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('../views/HomeView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
      meta: { requiresGuest: true }
    },
    {
      path: '/produtores',
      name: 'produtores',
      component: () => import('../views/ProdutoresView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/propriedades',
      name: 'propriedades',
      component: () => import('../views/PropriedadesView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/unidades-producao',
      name: 'unidades-producao',
      component: () => import('../views/UnidadesProducaoView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/rebanhos',
      name: 'rebanhos',
      component: () => import('../views/RebanhosView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/relatorios',
      name: 'relatorios',
      component: () => import('../views/RelatoriosView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: '/dashboard'
    }
  ],
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  console.log(authStore.isAuthenticated, 'isAuthenticated')
  // Check if user is authenticated
  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      // Try to fetch user with stored token
      const hasValidToken = await authStore.fetchUser()
      
      if (!hasValidToken) {
        next('/login')
        return
      }
    }
  }
  
  // Redirect authenticated users away from login page
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next('/dashboard')
    return
  }
  
  next()
})

export default router
