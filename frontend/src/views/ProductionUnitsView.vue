<template>
  <div class="unidades-producao">
    <div class="page-header">
      <h1>🌾 Unidades de Produção</h1>
      <p>Gerencie as unidades de produção das propriedades rurais</p>
    </div>

    <div class="toolbar">
      <div class="left">
        <button class="btn-primary" @click="openCreate">
          <span class="btn-icon"><i class="pi pi-plus"></i></span>
          Nova Unidade
        </button>
      </div>
      <div class="right">
        <input class="input" v-model="search" placeholder="Buscar por cultura ou propriedade (ID)..." />
        <button class="btn-secondary" @click="load"><i class="pi pi-refresh"></i></button>
      </div>
    </div>

    <div class="table-card">
      <div class="table-header">
        <h3>Unidades de Produção</h3>
      </div>
      <table class="table">
        <thead>
          <tr>
            <th>Propriedade ID</th>
            <th>Cultura</th>
            <th>Área (ha)</th>
            <th>Coordenadas</th>
            <th style="width:1%">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="store.loading"><td colspan="5" class="muted">Carregando...</td></tr>
          <tr v-else-if="!filtered.length"><td colspan="5" class="muted">Nenhuma unidade encontrada</td></tr>
          <tr v-for="u in filtered" :key="u.id">
            <td>{{ u.propriedade_id }}</td>
            <td>{{ u.nome_cultura }}</td>
            <td>{{ u.area_total_ha }}</td>
            <td>{{ u.coordenadas_geograficas || '-' }}</td>
            <td>
              <div class="row-actions">
                <button class="icon" @click="openView(u)" title="Ver"><i class="pi pi-eye"></i></button>
                <button class="icon" @click="openEdit(u)" title="Editar"><i class="pi pi-pencil"></i></button>
                <button class="icon danger" @click="openDelete(u)" title="Excluir"><i class="pi pi-trash"></i></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Criar -->
    <div v-if="showCreate" class="modal-backdrop">
      <div class="modal">
        <h3>Nova Unidade de Produção</h3>
        <form @submit.prevent="submitCreate" class="form-grid">
          <div>
            <label>Propriedade (ID)</label>
            <input v-model.number="form.propriedade_id" class="input" type="number" min="1" required />
          </div>
          <div>
            <label>Cultura</label>
            <input v-model="form.nome_cultura" class="input" required />
          </div>
          <div>
            <label>Área Total (ha)</label>
            <input v-model.number="form.area_total_ha" class="input" type="number" step="0.01" min="0" required />
          </div>
          <div class="col-span-2">
            <label>Coordenadas Geográficas</label>
            <input v-model="form.coordenadas_geograficas" class="input" />
          </div>
          <div class="modal-actions col-span-2">
            <button type="button" class="btn-secondary" @click="showCreate = false">Cancelar</button>
            <button class="btn-primary" type="submit">Salvar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Editar -->
    <div v-if="showEdit" class="modal-backdrop">
      <div class="modal">
        <h3>Editar Unidade</h3>
        <form @submit.prevent="submitEdit" class="form-grid">
          <div>
            <label>Propriedade (ID)</label>
            <input v-model.number="form.propriedade_id" class="input" type="number" min="1" required />
          </div>
          <div>
            <label>Cultura</label>
            <input v-model="form.nome_cultura" class="input" required />
          </div>
          <div>
            <label>Área Total (ha)</label>
            <input v-model.number="form.area_total_ha" class="input" type="number" step="0.01" min="0" required />
          </div>
          <div class="col-span-2">
            <label>Coordenadas Geográficas</label>
            <input v-model="form.coordenadas_geograficas" class="input" />
          </div>
          <div class="modal-actions col-span-2">
            <button type="button" class="btn-secondary" @click="showEdit = false">Cancelar</button>
            <button class="btn-primary" type="submit">Salvar Alterações</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Ver -->
    <div v-if="showView && selected" class="modal-backdrop">
      <div class="modal">
        <h3>Detalhes da Unidade</h3>
        <div class="details">
          <div><strong>Propriedade ID:</strong> {{ selected.propriedade_id }}</div>
          <div><strong>Cultura:</strong> {{ selected.nome_cultura }}</div>
          <div><strong>Área Total (ha):</strong> {{ selected.area_total_ha }}</div>
          <div><strong>Coordenadas:</strong> {{ selected.coordenadas_geograficas || '-' }}</div>
          <div><strong>Criado em:</strong> {{ selected.created_at }}</div>
          <div><strong>Atualizado em:</strong> {{ selected.updated_at }}</div>
        </div>
        <div class="modal-actions">
          <button class="btn-secondary" @click="showView = false">Fechar</button>
        </div>
      </div>
    </div>

    <!-- Confirmar Exclusão -->
    <div v-if="showConfirmDelete && selected" class="modal-backdrop">
      <div class="modal">
        <h3>Confirmar Exclusão</h3>
        <p>Tem certeza que deseja excluir a unidade de "{{ selected.nome_cultura }}"?</p>
        <div class="modal-actions">
          <button class="btn-secondary" @click="showConfirmDelete = false">Cancelar</button>
          <button class="btn-danger" @click="confirmDelete">Excluir</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, reactive, computed } from 'vue'
import { useProductionUnitStore } from '@/stores/productionUnit'
import type { ProductionUnit } from '@/types/productionUnit'

const store = useProductionUnitStore()
const search = ref('')
const showCreate = ref(false)
const showEdit = ref(false)
const showView = ref(false)
const showConfirmDelete = ref(false)

const form = reactive<Omit<ProductionUnit, 'id' | 'created_at' | 'updated_at'>>({
  propriedade_id: 0,
  nome_cultura: '',
  area_total_ha: 0,
  coordenadas_geograficas: ''
})

const selected = ref<ProductionUnit | null>(null)

const filtered = computed(() => {
  if (!search.value) return store.units
  const q = search.value.toLowerCase()
  return store.units.filter(u =>
    u.nome_cultura.toLowerCase().includes(q) || String(u.propriedade_id).includes(q)
  )
})

function resetForm() {
  form.propriedade_id = 0
  form.nome_cultura = ''
  form.area_total_ha = 0
  form.coordenadas_geograficas = ''
}

async function load() { await store.list() }
function openCreate() { resetForm(); showCreate.value = true }
function openView(item: ProductionUnit) { selected.value = item; showView.value = true }
function openEdit(item: ProductionUnit) {
  selected.value = item
  form.propriedade_id = item.propriedade_id
  form.nome_cultura = item.nome_cultura
  form.area_total_ha = item.area_total_ha
  form.coordenadas_geograficas = item.coordenadas_geograficas ?? ''
  showEdit.value = true
}
function openDelete(item: ProductionUnit) { selected.value = item; showConfirmDelete.value = true }

async function submitCreate() { await store.create({ ...form }); showCreate.value = false; resetForm() }
async function submitEdit() { if (!selected.value) return; await store.update(selected.value.id, { ...form }); showEdit.value = false }
async function confirmDelete() { if (!selected.value) return; await store.remove(selected.value.id); showConfirmDelete.value = false }

onMounted(load)
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

<style scoped>
/**** Modal scroll fix ****/
.modal-backdrop {
  position: fixed; inset: 0; background: rgba(0,0,0,0.4);
  display: flex; align-items: center; justify-content: center;
  padding: 1rem; z-index: 1000;
}
.modal {
  position: relative; z-index: 1001;
  background: #fff; border-radius: 0.5rem; padding: 1rem; width: 100%; max-width: 720px;
  border: 1px solid #e5e7eb; max-height: 90vh; overflow-y: auto;
}
</style>

