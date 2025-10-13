<script setup lang="ts">
import { RouterView } from 'vue-router'
</script>

<template>
  <div id="app">
    <header class="header">
      <div class="container">
        <div class="header-content">
          <div class="logo">
            <h1>🌱 Agro System</h1>
            <p>Sistema de Gestão Agropecuária</p>
          </div>
          <nav class="nav">
            <router-link to="/" class="nav-link">Dashboard</router-link>
            <router-link to="/produtores" class="nav-link">Produtores</router-link>
            <router-link to="/propriedades" class="nav-link">Propriedades</router-link>
            <router-link to="/relatorios" class="nav-link">Relatórios</router-link>
          </nav>
        </div>
      </div>
    </header>

    <main class="main">
      <div class="container">
        <RouterView />
      </div>
    </main>

    <footer class="footer">
      <div class="container">
        <p>&copy; 2024 Agro System - Sistema de Gestão Agropecuária</p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.header {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  color: white;
  padding: 1rem 0;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.logo h1 {
  margin: 0;
  font-size: 1.8rem;
  font-weight: 700;
}

.logo p {
  margin: 0;
  font-size: 0.9rem;
  opacity: 0.9;
}

.nav {
  display: flex;
  gap: 1.5rem;
}

.nav-link {
  color: white;
  text-decoration: none;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  transition: background-color 0.2s;
}

.nav-link:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.nav-link.router-link-active {
  background-color: rgba(255, 255, 255, 0.2);
}

.main {
  min-height: calc(100vh - 140px);
  padding: 2rem 0;
}

.footer {
  background-color: #374151;
  color: white;
  text-align: center;
  padding: 1rem 0;
}

.footer p {
  margin: 0;
  font-size: 0.875rem;
}

@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    text-align: center;
  }
  
  .nav {
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .logo h1 {
    font-size: 1.5rem;
  }
}
</style>
