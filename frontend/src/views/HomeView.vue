<script setup lang="ts">
// Dashboard principal do sistema agropecuário
</script>

<template>
  <div class="dashboard">
    <div class="welcome-section">
      <h1>Bem-vindo ao Agro System</h1>
      <p>Gerencie sua propriedade rural de forma eficiente e organizada</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="pi pi-users"></i>
        </div>
        <div class="stat-content">
          <h3>Produtores</h3>
          <p class="stat-number">0</p>
          <p class="stat-label">Cadastrados</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="pi pi-home"></i>
        </div>
        <div class="stat-content">
          <h3>Propriedades</h3>
          <p class="stat-number">0</p>
          <p class="stat-label">Registradas</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="pi pi-tree"></i>
        </div>
        <div class="stat-content">
          <h3>Culturas</h3>
          <p class="stat-number">0</p>
          <p class="stat-label">Em Produção</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="pi pi-circle"></i>
        </div>
        <div class="stat-content">
          <h3>Rebanhos</h3>
          <p class="stat-number">0</p>
          <p class="stat-label">Animais</p>
        </div>
      </div>
    </div>

    <div class="actions-grid">
      <div class="action-card">
        <h3>Gestão de Produtores</h3>
        <p>Cadastre e gerencie informações dos produtores rurais</p>
        <router-link to="/produtores" class="btn-primary">Gerenciar Produtores</router-link>
      </div>

      <div class="action-card">
        <h3>Propriedades Rurais</h3>
        <p>Administre propriedades, culturas e rebanhos</p>
        <router-link to="/propriedades" class="btn-primary">Gerenciar Propriedades</router-link>
      </div>

      <div class="action-card">
        <h3>Relatórios</h3>
        <p>Gere relatórios detalhados e exporte dados</p>
        <router-link to="/relatorios" class="btn-primary">Ver Relatórios</router-link>
      </div>
    </div>

    <div class="info-section">
      <h2>Funcionalidades do Sistema</h2>
      <div class="features-grid">
        <div class="feature">
          <div class="feature-icon">
            <i class="pi pi-chart-bar"></i>
          </div>
          <h4>Relatórios Avançados</h4>
          <p>Total de propriedades por município, animais por espécie e hectares por cultura</p>
        </div>
        <div class="feature">
          <div class="feature-icon">
            <i class="pi pi-download"></i>
          </div>
          <h4>Exportação</h4>
          <p>Exporte dados em Excel (.xlsx) e PDF para análise e compartilhamento</p>
        </div>
        <div class="feature">
          <div class="feature-icon">
            <i class="pi pi-search"></i>
          </div>
          <h4>Filtros e Busca</h4>
          <p>Filtros avançados por município, espécie, produtor e muito mais</p>
        </div>
        <div class="feature">
          <div class="feature-icon">
            <i class="pi pi-mobile"></i>
          </div>
          <h4>Interface Responsiva</h4>
          <p>Acesse o sistema de qualquer dispositivo com design moderno</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 0;
}

.welcome-section {
  text-align: center;
  margin-bottom: 3rem;
  padding: 2rem;
  background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
  border-radius: 1rem;
  border: 1px solid #bbf7d0;
}

.welcome-section h1 {
  font-size: 2.5rem;
  color: #065f46;
  margin-bottom: 0.5rem;
  font-weight: 700;
}

.welcome-section p {
  font-size: 1.2rem;
  color: #047857;
  margin: 0;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  font-size: 2.5rem;
  width: 4rem;
  height: 4rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  border-radius: 1rem;
  color: white;
}

.stat-content h3 {
  margin: 0 0 0.5rem 0;
  color: #374151;
  font-size: 1rem;
  font-weight: 600;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: #059669;
  margin: 0;
  line-height: 1;
}

.stat-label {
  margin: 0;
  color: #6b7280;
  font-size: 0.875rem;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.action-card {
  background: white;
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
  text-align: center;
}

.action-card h3 {
  margin: 0 0 1rem 0;
  color: #374151;
  font-size: 1.25rem;
  font-weight: 600;
}

.action-card p {
  margin: 0 0 1.5rem 0;
  color: #6b7280;
  line-height: 1.6;
}

.info-section {
  background: white;
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
}

.info-section h2 {
  margin: 0 0 2rem 0;
  color: #374151;
  font-size: 1.5rem;
  font-weight: 600;
  text-align: center;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.feature {
  text-align: center;
  padding: 1rem;
}

.feature-icon {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.feature h4 {
  margin: 0 0 0.5rem 0;
  color: #374151;
  font-size: 1.1rem;
  font-weight: 600;
}

.feature p {
  margin: 0;
  color: #6b7280;
  font-size: 0.9rem;
  line-height: 1.5;
}

@media (max-width: 768px) {
  .welcome-section h1 {
    font-size: 2rem;
  }
  
  .welcome-section p {
    font-size: 1rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .actions-grid {
    grid-template-columns: 1fr;
  }
  
  .features-grid {
    grid-template-columns: 1fr;
  }
}
</style>
