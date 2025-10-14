<template>
  <div class="unidades-producao">
    <div class="page-header">
      <h1>🌾 Unidades de Produção</h1>
      <p>Gerencie as unidades de produção das propriedades rurais</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="pi pi-tree"></i>
        </div>
        <div class="stat-content">
          <h3>Unidades Ativas</h3>
          <p class="stat-number">0</p>
          <p class="stat-label">Em Produção</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="pi pi-map"></i>
        </div>
        <div class="stat-content">
          <h3>Área Total</h3>
          <p class="stat-number">0</p>
          <p class="stat-label">Hectares</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="pi pi-seedling"></i>
        </div>
        <div class="stat-content">
          <h3>Culturas</h3>
          <p class="stat-number">0</p>
          <p class="stat-label">Tipos Diferentes</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="pi pi-chart-line"></i>
        </div>
        <div class="stat-content">
          <h3>Produtividade</h3>
          <p class="stat-number">0</p>
          <p class="stat-label">Kg/Hectare</p>
        </div>
      </div>
    </div>

    <div class="content-grid">
      <div class="actions-card">
        <h2>Gestão de Unidades</h2>
        <div class="action-buttons">
          <button class="btn-primary">
            <span class="btn-icon">
              <i class="pi pi-plus"></i>
            </span>
            Nova Unidade
          </button>
          <button class="btn-secondary">
            <span class="btn-icon">
              <i class="pi pi-file"></i>
            </span>
            Relatório de Produção
          </button>
          <button class="btn-secondary">
            <span class="btn-icon">
              <i class="pi pi-chart-bar"></i>
            </span>
            Análise de Produtividade
          </button>
        </div>
      </div>

      <div class="filters-card">
        <h3>Filtros</h3>
        <div class="filter-group">
          <label>Propriedade:</label>
          <select class="filter-select">
            <option value="">Todas as propriedades</option>
            <option value="1">Fazenda São João</option>
            <option value="2">Sítio Verde</option>
          </select>
        </div>
        <div class="filter-group">
          <label>Cultura:</label>
          <select class="filter-select">
            <option value="">Todas as culturas</option>
            <option value="milho">Milho</option>
            <option value="soja">Soja</option>
            <option value="algodao">Algodão</option>
          </select>
        </div>
        <div class="filter-group">
          <label>Status:</label>
          <select class="filter-select">
            <option value="">Todos os status</option>
            <option value="ativa">Ativa</option>
            <option value="inativa">Inativa</option>
            <option value="preparacao">Em Preparação</option>
          </select>
        </div>
      </div>
    </div>

    <div class="table-card">
      <div class="table-header">
        <h3>Unidades de Produção</h3>
        <div class="table-actions">
          <button class="btn-icon-only" title="Exportar">
            <i class="pi pi-download"></i>
          </button>
          <button class="btn-icon-only" title="Atualizar">
            <i class="pi pi-refresh"></i>
          </button>
        </div>
      </div>
      
      <div class="table-placeholder">
        <div class="placeholder-content">
          <div class="placeholder-icon">
            <i class="pi pi-tree"></i>
          </div>
          <h4>Nenhuma unidade de produção cadastrada</h4>
          <p>Comece criando sua primeira unidade de produção</p>
          <button class="btn-primary">
            <span class="btn-icon">
              <i class="pi pi-plus"></i>
            </span>
            Criar Primeira Unidade
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Lógica para gestão de unidades de produção
</script>

<style scoped>
.unidades-producao {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 0;
}

.page-header {
  text-align: center;
  margin-bottom: 3rem;
  padding: 2rem;
  background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
  border-radius: 1rem;
  border: 1px solid #bbf7d0;
}

.page-header h1 {
  font-size: 2.5rem;
  color: #065f46;
  margin-bottom: 0.5rem;
  font-weight: 700;
}

.page-header p {
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

.content-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.actions-card, .filters-card {
  background: white;
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
}

.actions-card h2 {
  margin: 0 0 1.5rem 0;
  color: #374151;
  font-size: 1.5rem;
  font-weight: 600;
}

.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.btn-primary, .btn-secondary {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
  text-decoration: none;
}

.btn-primary {
  background: linear-gradient(135deg, #059669 0%, #047857 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(5, 150, 105, 0.3);
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
  border: 1px solid #d1d5db;
}

.btn-secondary:hover {
  background: #e5e7eb;
  transform: translateY(-1px);
}

.btn-icon {
  font-size: 1.125rem;
}

.filters-card h3 {
  margin: 0 0 1.5rem 0;
  color: #374151;
  font-size: 1.25rem;
  font-weight: 600;
}

.filter-group {
  margin-bottom: 1rem;
}

.filter-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.filter-select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background: white;
  color: #374151;
  font-size: 0.875rem;
}

.filter-select:focus {
  outline: none;
  border-color: #059669;
  box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
}

.table-card {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
  overflow: hidden;
}

.table-header {
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f9fafb;
}

.table-header h3 {
  margin: 0;
  color: #374151;
  font-size: 1.25rem;
  font-weight: 600;
}

.table-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-icon-only {
  background: #f3f4f6;
  border: 1px solid #d1d5db;
  color: #6b7280;
  padding: 0.5rem;
  border-radius: 0.375rem;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.2s;
}

.btn-icon-only:hover {
  background: #e5e7eb;
  color: #374151;
}

.table-placeholder {
  padding: 4rem 2rem;
  text-align: center;
}

.placeholder-content {
  max-width: 400px;
  margin: 0 auto;
}

.placeholder-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.placeholder-content h4 {
  margin: 0 0 0.5rem 0;
  color: #374151;
  font-size: 1.25rem;
  font-weight: 600;
}

.placeholder-content p {
  margin: 0 0 2rem 0;
  color: #6b7280;
  line-height: 1.5;
}

@media (max-width: 768px) {
  .page-header h1 {
    font-size: 2rem;
  }
  
  .page-header p {
    font-size: 1rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .content-grid {
    grid-template-columns: 1fr;
  }
  
  .action-buttons {
    flex-direction: column;
  }
}
</style>

