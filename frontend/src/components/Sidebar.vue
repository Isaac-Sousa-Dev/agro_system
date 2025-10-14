<template>
  <aside class="sidebar" :class="{ 'sidebar-collapsed': collapsed }">
    <!-- Header da Sidebar -->
    <div class="sidebar-header">
      <div class="logo-section">
        <div class="logo-icon">
          <i class="pi pi-seedling"></i>
        </div>
        <div v-if="!collapsed" class="logo-text">
          <h2>Agro System</h2>
          <p>Sistema de Gestão</p>
        </div>
      </div>

    </div>

    <!-- Seção de Perfil do Usuário -->
    <div class="profile-section">
      <div class="profile-info">
        <div class="profile-avatar">
          <img v-if="user?.avatar" :src="user.avatar" :alt="user.name" class="avatar-image">
          <div v-else class="avatar-placeholder">
            {{ user?.name?.charAt(0) || 'U' }}
          </div>
        </div>
        <div v-if="!collapsed" class="profile-details">
          <h3 class="profile-name">{{ user?.name || 'Usuário' }}</h3>
          <p class="profile-email">{{ user?.email || 'email@exemplo.com' }}</p>
        </div>
      </div>
      <button v-if="!collapsed" @click="logout" class="logout-button" title="Sair do sistema">
        <span class="logout-icon">
          <i class="pi pi-sign-out"></i>
        </span>
        <span>Sair</span>
      </button>
      <button v-else @click="logout" class="logout-button-collapsed" title="Sair do sistema">
        <i class="pi pi-sign-out"></i>
      </button>
    </div>

    <!-- Menu de Navegação -->
    <nav class="sidebar-nav">
      <ul class="nav-list">
        <li class="nav-item">
          <router-link to="/dashboard" class="nav-link" :title="collapsed ? 'Dashboard' : ''">
            <span class="nav-icon">
              <i class="pi pi-chart-bar"></i>
            </span>
            <span v-if="!collapsed" class="nav-text">Dashboard</span>
          </router-link>
        </li>

        <li class="nav-item">
          <router-link to="/produtores" class="nav-link" :title="collapsed ? 'Produtores Rurais' : ''">
            <span class="nav-icon">
              <i class="pi pi-users"></i>
            </span>
            <span v-if="!collapsed" class="nav-text">Produtor Rural</span>
          </router-link>
        </li>

        <li class="nav-item">
          <router-link to="/propriedades" class="nav-link" :title="collapsed ? 'Propriedades' : ''">
            <span class="nav-icon">
              <i class="pi pi-home"></i>
            </span>
            <span v-if="!collapsed" class="nav-text">Propriedade</span>
          </router-link>
        </li>

        <li class="nav-item">
          <router-link to="/unidades-producao" class="nav-link" :title="collapsed ? 'Unidades de Produção' : ''">
            <span class="nav-icon">
              <i class="pi pi-tree"></i>
            </span>
            <span v-if="!collapsed" class="nav-text">Unidade de Produção</span>
          </router-link>
        </li>

        <li class="nav-item">
          <router-link to="/rebanhos" class="nav-link" :title="collapsed ? 'Rebanhos' : ''">
            <span class="nav-icon">
              <i class="pi pi-circle"></i>
            </span>
            <span v-if="!collapsed" class="nav-text">Rebanho</span>
          </router-link>
        </li>

        <li class="nav-item">
          <router-link to="/relatorios" class="nav-link" :title="collapsed ? 'Relatórios' : ''">
            <span class="nav-icon">
              <i class="pi pi-file"></i>
            </span>
            <span v-if="!collapsed" class="nav-text">Relatórios</span>
          </router-link>
        </li>
      </ul>
    </nav>

    <!-- Footer da Sidebar -->
    <div v-if="!collapsed" class="sidebar-footer">
      <p class="footer-text">© 2024 Agro System</p>
      <p class="footer-version">v1.0.0</p>
    </div>
  </aside>

  <!-- Overlay para mobile -->
  <div v-if="isMobile && !collapsed" class="sidebar-overlay" @click="collapseSidebar"></div>
</template>

<script setup lang="ts">

import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useAuth } from '@/composables/useAuth'

const { user, logout } = useAuth()

// Props
interface Props {
  isOpen?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  isOpen: false
})

// Emits
const emit = defineEmits<{
  close: []
}>()

// Estado da sidebar
const collapsed = ref(false)
const isMobile = ref(false)

// Colapsar sidebar no mobile
const collapseSidebar = () => {
  if (isMobile.value) {
    collapsed.value = true
    emit('close')
  }
}

// Verificar se é mobile
const checkIsMobile = () => {
  isMobile.value = window.innerWidth < 768
  if (isMobile.value) {
    collapsed.value = true
  } else {
    collapsed.value = false
  }
}

// Watch para props.isOpen no mobile
watch(() => props.isOpen, (newValue) => {
  if (isMobile.value) {
    collapsed.value = !newValue
  }
})

// Event listeners
onMounted(() => {
  checkIsMobile()
  console.log(user.value, 'user on mount')
  window.addEventListener('resize', checkIsMobile)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkIsMobile)
})
</script>

<style scoped>
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  width: 280px;
  background: linear-gradient(180deg, #059669 0%, #047857 100%);
  color: white;
  display: flex;
  flex-direction: column;
  transition: width 0.3s ease;
  z-index: 1000;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar-collapsed {
  width: 70px;
}

/* Header da Sidebar */
.sidebar-header {
  padding: 1.5rem 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.logo-icon {
  font-size: 1.8rem;
  width: 2.5rem;
  height: 2.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 0.5rem;
}

.logo-text h2 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
}

.logo-text p {
  margin: 0;
  font-size: 0.75rem;
  opacity: 0.8;
}

.toggle-button {
  background: rgba(255, 255, 255, 0.1);
  border: none;
  color: white;
  width: 2rem;
  height: 2rem;
  border-radius: 0.375rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.2s;
}

.toggle-button:hover {
  background: rgba(255, 255, 255, 0.2);
}

.toggle-icon {
  font-size: 0.875rem;
  transition: transform 0.2s;
}

/* Seção de Perfil */
.profile-section {
  padding: 1.5rem 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.profile-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.profile-avatar {
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
}

.avatar-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1rem;
  color: white;
}

.profile-details {
  flex: 1;
  min-width: 0;
}

.profile-name {
  margin: 0 0 0.25rem 0;
  font-size: 1rem;
  font-weight: 600;
  color: white;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.profile-email {
  margin: 0;
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.8);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.logout-button {
  width: 100%;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
  padding: 0.75rem;
  border-radius: 0.5rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s;
}

.logout-button:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-1px);
}

.logout-button-collapsed {
  width: 100%;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
  padding: 0.75rem;
  border-radius: 0.5rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  transition: all 0.2s;
}

.logout-button-collapsed:hover {
  background: rgba(255, 255, 255, 0.2);
}

/* Navegação */
.sidebar-nav {
  flex: 1;
  padding: 1rem 0;
}

.nav-list {
  list-style: none;
  margin: 0;
  padding: 0;
}

.nav-item {
  margin: 0.25rem 0;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.875rem 1rem;
  color: rgba(255, 255, 255, 0.9);
  text-decoration: none;
  transition: all 0.2s;
  border-radius: 0 1.5rem 1.5rem 0;
  margin-right: 1rem;
  position: relative;
}

.nav-link:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  transform: translateX(4px);
}

.nav-link.router-link-active {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  font-weight: 600;
}

.nav-link.router-link-active::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 4px;
  height: 60%;
  background: white;
  border-radius: 0 2px 2px 0;
}

.nav-icon {
  font-size: 1.25rem;
  width: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.nav-text {
  font-size: 0.875rem;
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Footer */
.sidebar-footer {
  padding: 1rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  text-align: center;
}

.footer-text {
  margin: 0 0 0.25rem 0;
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.8);
}

.footer-version {
  margin: 0;
  font-size: 0.625rem;
  color: rgba(255, 255, 255, 0.6);
}

/* Overlay para mobile */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  z-index: 999;
}

/* Responsividade */
@media (max-width: 768px) {
  .sidebar {
    width: 280px;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }

  .sidebar:not(.sidebar-collapsed) {
    transform: translateX(0);
  }

  .sidebar-collapsed {
    transform: translateX(-100%);
    width: 280px;
  }
}

/* Animações */
@keyframes slideIn {
  from {
    transform: translateX(-100%);
  }

  to {
    transform: translateX(0);
  }
}

.sidebar:not(.sidebar-collapsed) {
  animation: slideIn 0.3s ease;
}
</style>
