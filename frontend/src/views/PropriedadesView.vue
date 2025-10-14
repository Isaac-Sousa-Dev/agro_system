<script setup lang="ts">
// View para gerenciar propriedades
</script>

<template>
  <div class="page">
    <div class="page-header">
      <h1>🏡 Propriedades</h1>
      <p>Gerencie as propriedades rurais e suas informações</p>
    </div>

    <div class="page-content">
      <div class="coming-soon">
        <h2>🚧 Em Desenvolvimento</h2>
        <p>Esta funcionalidade será implementada em breve.</p>
        <p>Aqui você poderá:</p>
        <ul>
          <li>Cadastrar novas propriedades</li>
          <li>Associar propriedades aos produtores</li>
          <li>Gerenciar culturas e rebanhos</li>
          <li>Visualizar mapas e coordenadas</li>
          <li>Gerar relatórios por região</li>
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
