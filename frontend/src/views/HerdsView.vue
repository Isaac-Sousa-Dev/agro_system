<template>
  <div class="rebanhos">
    <div class="page-header">
      <h1>🐄 Gestão de Rebanhos</h1>
      <p>Administre rebanhos, animais e controle sanitário</p>
    </div>

    <div class="toolbar">
      <div class="left">
        <button class="btn-primary" @click="openCreate">
          <span class="btn-icon"><i class="pi pi-plus"></i></span>
          Novo Rebanho
        </button>
      </div>
      <div class="right">
        <input class="input" v-model="search" placeholder="Buscar por espécie ou propriedade (ID)..." />
        <button class="btn-secondary" @click="load"><i class="pi pi-refresh"></i></button>
      </div>
    </div>

    <div class="table-card">
      <div class="table-header">
        <h3>Rebanhos Cadastrados</h3>
      </div>
      <table class="table">
        <thead>
          <tr>
            <th>Propriedade ID</th>
            <th>Espécie</th>
            <th>Quantidade</th>
            <th>Finalidade</th>
            <th>Atualização</th>
            <th style="width:1%">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="store.loading"><td colspan="6" class="muted">Carregando...</td></tr>
          <tr v-else-if="!filtered.length"><td colspan="6" class="muted">Nenhum rebanho encontrado</td></tr>
          <tr v-for="h in filtered" :key="h.id">
            <td>{{ h.propriedade_id }}</td>
            <td>{{ h.especie }}</td>
            <td>{{ h.quantidade }}</td>
            <td>{{ h.finalidade || '-' }}</td>
            <td>{{ h.data_atualizacao || '-' }}</td>
            <td>
              <div class="row-actions">
                <button class="icon" @click="openView(h)" title="Ver"><i class="pi pi-eye"></i></button>
                <button class="icon" @click="openEdit(h)" title="Editar"><i class="pi pi-pencil"></i></button>
                <button class="icon danger" @click="openDelete(h)" title="Excluir"><i class="pi pi-trash"></i></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Criar -->
    <div v-if="showCreate" class="modal-backdrop">
      <div class="modal">
        <h3>Novo Rebanho</h3>
        <form @submit.prevent="submitCreate" class="form-grid">
          <div>
            <label>Propriedade (ID)</label>
            <input v-model.number="form.propriedade_id" class="input" type="number" min="1" required />
          </div>
          <div>
            <label>Espécie</label>
            <input v-model="form.especie" class="input" required />
          </div>
          <div>
            <label>Quantidade</label>
            <input v-model.number="form.quantidade" class="input" type="number" min="0" required />
          </div>
          <div>
            <label>Finalidade</label>
            <input v-model="form.finalidade" class="input" />
          </div>
          <div>
            <label>Data de Atualização</label>
            <input v-model="form.data_atualizacao" class="input" type="date" />
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
        <h3>Editar Rebanho</h3>
        <form @submit.prevent="submitEdit" class="form-grid">
          <div>
            <label>Propriedade (ID)</label>
            <input v-model.number="form.propriedade_id" class="input" type="number" min="1" required />
          </div>
          <div>
            <label>Espécie</label>
            <input v-model="form.especie" class="input" required />
          </div>
          <div>
            <label>Quantidade</label>
            <input v-model.number="form.quantidade" class="input" type="number" min="0" required />
          </div>
          <div>
            <label>Finalidade</label>
            <input v-model="form.finalidade" class="input" />
          </div>
          <div>
            <label>Data de Atualização</label>
            <input v-model="form.data_atualizacao" class="input" type="date" />
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
        <h3>Detalhes do Rebanho</h3>
        <div class="details">
          <div><strong>Propriedade ID:</strong> {{ selected.propriedade_id }}</div>
          <div><strong>Espécie:</strong> {{ selected.especie }}</div>
          <div><strong>Quantidade:</strong> {{ selected.quantidade }}</div>
          <div><strong>Finalidade:</strong> {{ selected.finalidade || '-' }}</div>
          <div><strong>Data de Atualização:</strong> {{ selected.data_atualizacao || '-' }}</div>
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
        <p>Tem certeza que deseja excluir o rebanho de espécie "{{ selected.especie }}"?</p>
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
import { useToast } from 'primevue/usetoast'
import { useHerdStore } from '@/stores/herd'
import type { Herd } from '@/types/herd'

const store = useHerdStore()
const toast = useToast()
const search = ref('')
const showCreate = ref(false)
const showEdit = ref(false)
const showView = ref(false)
const showConfirmDelete = ref(false)

const form = reactive<Omit<Herd, 'id' | 'created_at' | 'updated_at'>>({
  propriedade_id: 0,
  especie: '',
  quantidade: 0,
  finalidade: '',
  data_atualizacao: ''
})

const selected = ref<Herd | null>(null)

const filtered = computed(() => {
  if (!search.value) return store.herds
  const q = search.value.toLowerCase()
  return store.herds.filter(h =>
    h.especie.toLowerCase().includes(q) || String(h.propriedade_id).includes(q)
  )
})

function resetForm() {
  form.propriedade_id = 0
  form.especie = ''
  form.quantidade = 0
  form.finalidade = ''
  form.data_atualizacao = ''
}

async function load() {
  try {
    await store.list()
    toast.add({ severity: 'success', summary: 'Rebanhos', detail: 'Lista atualizada', life: 2000 })
  } catch (e) {
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao carregar rebanhos', life: 3000 })
  }
}
function openCreate() { resetForm(); showCreate.value = true }
function openView(item: Herd) { selected.value = item; showView.value = true }
function openEdit(item: Herd) {
  selected.value = item
  form.propriedade_id = item.propriedade_id
  form.especie = item.especie
  form.quantidade = item.quantidade
  form.finalidade = item.finalidade ?? ''
  form.data_atualizacao = item.data_atualizacao ?? ''
  showEdit.value = true
}
function openDelete(item: Herd) { selected.value = item; showConfirmDelete.value = true }

async function submitCreate() {
  try {
    await store.create({ ...form })
    toast.add({ severity: 'success', summary: 'Rebanho', detail: 'Criado com sucesso', life: 2500 })
    showCreate.value = false
    resetForm()
  } catch (e) {
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao criar rebanho', life: 3000 })
  }
}
async function submitEdit() {
  if (!selected.value) return
  try {
    await store.update(selected.value.id, { ...form })
    toast.add({ severity: 'success', summary: 'Rebanho', detail: 'Atualizado com sucesso', life: 2500 })
    showEdit.value = false
  } catch (e) {
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao atualizar rebanho', life: 3000 })
  }
}
async function confirmDelete() {
  if (!selected.value) return
  try {
    await store.remove(selected.value.id)
    toast.add({ severity: 'success', summary: 'Rebanho', detail: 'Excluído com sucesso', life: 2500 })
    showConfirmDelete.value = false
  } catch (e) {
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao excluir rebanho', life: 3000 })
  }
}

onMounted(load)
</script>

<style scoped>
.rebanhos {
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
  margin-bottom: 3rem;
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

/* Seção de Informações */
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

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.info-card {
  text-align: center;
  padding: 1.5rem;
  background: #f9fafb;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
}

.info-icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.info-card h4 {
  margin: 0 0 0.75rem 0;
  color: #374151;
  font-size: 1.1rem;
  font-weight: 600;
}

.info-card p {
  margin: 0;
  color: #6b7280;
  font-size: 0.9rem;
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

  .info-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<style scoped>
/**** Modal scroll fix ****/
.modal-backdrop {
  position: fixed; inset: 0; background: rgba(0,0,0,0.4);
  display: flex; align-items: center; justify-content: center;
  padding: 1rem; z-index: 40;
}
.modal {
  background: #fff; border-radius: 0.5rem; padding: 1rem; width: 100%; max-width: 720px;
  border: 1px solid #e5e7eb;
  max-height: 90vh; overflow-y: auto;
}
/* Elevate modal above sidebar and block background interactions */
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

