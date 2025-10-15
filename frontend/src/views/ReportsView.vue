<script setup lang="ts">
// View para relatórios
</script>

<template>
  <div class="page">
    <div class="page-header">
      <h1>📊 Relatórios</h1>
      <p>Gere relatórios detalhados e exporte dados do sistema</p>
    </div>

    <div class="page-content">
      <div class="coming-soon">
        <h2>🚧 Em Desenvolvimento</h2>
        <p>Esta funcionalidade será implementada em breve.</p>
        <p>Aqui você poderá:</p>
        <ul>
          <li>Total de propriedades por município</li>
          <li>Animais por espécie</li>
          <li>Hectares por cultura</li>
          <li>Exportação em Excel (.xlsx)</li>
          <li>Exportação em PDF</li>
          <li>Filtros avançados</li>
          <li>Gráficos e visualizações</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 0;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2rem;
  color: #065f46;
  margin: 0 0 0.5rem 0;
  font-weight: 700;
}

.page-header p {
  color: #6b7280;
  margin: 0;
  font-size: 1.1rem;
}

.page-content {
  background: white;
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
}

.coming-soon {
  text-align: center;
  padding: 2rem;
}

.coming-soon h2 {
  color: #374151;
  margin-bottom: 1rem;
}

.coming-soon p {
  color: #6b7280;
  margin-bottom: 1rem;
}

.coming-soon ul {
  text-align: left;
  max-width: 400px;
  margin: 0 auto;
  color: #6b7280;
}

.coming-soon li {
  margin-bottom: 0.5rem;
}
</style>
