<script setup lang="ts">
import { RouterView } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { computed, ref, onMounted, onUnmounted } from 'vue'
import Sidebar from '@/components/Sidebar.vue'
import Toast from 'primevue/toast'

const { isAuthenticated } = useAuth()
const sidebarOpen = ref(false)

const showSidebar = computed(() => {
  return isAuthenticated.value
})

// Toggle sidebar no mobile
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

// Fechar sidebar quando clicar fora (mobile)
const closeSidebar = () => {
  sidebarOpen.value = false
}

// Verificar se é mobile
const isMobile = ref(false)
const checkIsMobile = () => {
  isMobile.value = window.innerWidth < 768
}

onMounted(() => {
  checkIsMobile()
  window.addEventListener('resize', checkIsMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkIsMobile)
})
</script>

<template>
  <div id="app">

    <Sidebar
      v-if="showSidebar"
      :is-open="sidebarOpen"
      @close="closeSidebar"
    />

    <header v-if="showSidebar && isMobile" class="mobile-header">
      <button @click="toggleSidebar" class="menu-toggle">
        <i class="pi pi-bars"></i>
      </button>
      <div class="mobile-logo">
        <i class="pi pi-seedling"></i>
        <h1>Agro System</h1>
      </div>
      <div class="mobile-spacer"></div>
    </header>

    <main class="main" :class="{
        'with-sidebar': showSidebar && !isMobile,
        'with-mobile-header': showSidebar && isMobile
      }"
    >
      <Toast />
      <div v-if="showSidebar" class="container">
        <RouterView />
      </div>
      <div v-else class="container">
        <RouterView />
      </div>
    </main>

    <!-- Footer -->
    <footer v-if="showSidebar" class="footer">
      <div class="container">
        <p>&copy; 2024 Agro System - Sistema de Gestão Agropecuária</p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
/* Header Mobile */
.mobile-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  color: white;
  padding: 1rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  z-index: 1001;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.menu-toggle {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
  padding: 0.5rem;
  border-radius: 0.375rem;
  cursor: pointer;
  font-size: 1.25rem;
  transition: background-color 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
}

.menu-toggle:hover {
  background: rgba(255, 255, 255, 0.2);
}

.mobile-logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.mobile-logo h1 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
}

.mobile-logo i {
  font-size: 1.5rem;
}

.mobile-spacer {
  flex: 1;
}

/* Layout Principal */
.main {
  min-height: 100vh;
  transition: margin-left 0.3s ease;
}

.main.with-sidebar {
  margin-left: 280px;
}

.main.with-mobile-header {
  padding-top: 80px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

/* Footer */
.footer {
  background-color: #374151;
  color: white;
  text-align: center;
  padding: 1rem;
  margin-left: 0;
  transition: margin-left 0.3s ease;
}

.main.with-sidebar + .footer {
  margin-left: 280px;
}

.footer p {
  margin: 0;
  font-size: 0.875rem;
}

/* Responsividade */
@media (max-width: 768px) {
  .main.with-sidebar {
    margin-left: 0;
  }

  .main.with-sidebar + .footer {
    margin-left: 0;
  }

  .container {
    padding: 1rem;
  }
}

/* Animações */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.main {
  animation: fadeIn 0.5s ease;
}
</style>
