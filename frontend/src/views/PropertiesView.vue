<script setup lang="ts">
import { onMounted, reactive, ref, computed } from 'vue'
import { usePropertyStore } from '@/stores/property'
import type { Property } from '@/types/property'

const store = usePropertyStore()
const search = ref('')
const showCreate = ref(false)
const showEdit = ref(false)
const showView = ref(false)
const showConfirmDelete = ref(false)

const form = reactive<Omit<Property, 'id' | 'created_at' | 'updated_at'>>({
  farmer_id: 0,
  name: '',
  municipality: '',
  state: '',
  state_registration: '',
  total_area: 0
})

const selected = ref<Property | null>(null)

const filtered = computed(() => {
  if (!search.value) return store.properties
  const q = search.value.toLowerCase()
  return store.properties.filter(p =>
    p.name.toLowerCase().includes(q) ||
    p.municipality.toLowerCase().includes(q) ||
    p.state.toLowerCase().includes(q)
  )
})

function resetForm() {
  form.farmer_id = 0
  form.name = ''
  form.municipality = ''
  form.state = ''
  form.state_registration = ''
  form.total_area = 0
}

async function load() {
  await store.list()
}

function openCreate() {
  resetForm()
  showCreate.value = true
}

function openView(item: Property) {
  selected.value = item
  showView.value = true
}

function openEdit(item: Property) {
  selected.value = item
  form.farmer_id   = item.farmer_id
  form.name = item.name
  form.municipality = item.municipality
  form.state = item.state
  form.state_registration = item.state_registration ?? ''
  form.total_area = item.total_area
  showEdit.value = true
}

function openDelete(item: Property) {
  selected.value = item
  showConfirmDelete.value = true
}

async function submitCreate() {
  await store.create({ ...form })
  showCreate.value = false
  resetForm()
}

async function submitEdit() {
  if (!selected.value) return
  await store.update(selected.value.id, { ...form })
  showEdit.value = false
}

async function confirmDelete() {
  if (!selected.value) return
  await store.remove(selected.value.id)
  showConfirmDelete.value = false
}

onMounted(load)
</script>

<template>
  <div class="page">
    <div class="page-header">
      <h1>🏡 Propriedades</h1>
      <p>Gerencie as propriedades rurais e suas informações</p>
    </div>

    <div class="toolbar">
      <div class="left">
        <button class="btn-primary" @click="openCreate">
          <span class="btn-icon"><i class="pi pi-plus"></i></span>
          Nova Propriedade
        </button>
      </div>
      <div class="right">
        <input class="input" v-model="search" placeholder="Buscar por nome, município, UF..." />
        <button class="btn-secondary" @click="load">
          <i class="pi pi-refresh"></i>
        </button>
      </div>
    </div>

    <div class="table-card">
      <table class="table">
        <thead>
          <tr>
            <th>Nome</th>
            <th>Município</th>
            <th>UF</th>
            <th>Inscrição Estadual</th>
            <th>Área Total (ha)</th>
            <th style="width: 1%">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="store.loading">
            <td colspan="6" class="muted">Carregando...</td>
          </tr>
          <tr v-else-if="!filtered.length">
            <td colspan="6" class="muted">Nenhuma propriedade encontrada</td>
          </tr>
          <tr v-for="p in filtered" :key="p.id">
            <td>{{ p.name }}</td>
            <td>{{ p.municipality }}</td>
            <td>{{ p.state }}</td>
            <td>{{ p.state_registration || '-' }}</td>
            <td>{{ p.total_area }}</td>
            <td>
              <div class="row-actions">
                <button class="icon" title="Ver" @click="openView(p)"><i class="pi pi-eye"></i></button>
                <button class="icon" title="Editar" @click="openEdit(p)"><i class="pi pi-pencil"></i></button>
                <button class="icon danger" title="Excluir" @click="openDelete(p)"><i class="pi pi-trash"></i></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Criar -->
    <div v-if="showCreate" class="modal-backdrop">
      <div class="modal">
        <h3>Nova Propriedade</h3>
        <form @submit.prevent="submitCreate" class="form-grid">
          <div>
            <label>Produtor (ID)</label>
            <input v-model.number="form.farmer_id" class="input" type="number" min="1" required />
          </div>
          <div class="col-span-2">
            <label>Nome</label>
            <input v-model="form.name" class="input" required />
          </div>
          <div>
            <label>Município</label>
            <input v-model="form.municipality" class="input" required />
          </div>
          <div>
            <label>UF</label>
            <input v-model="form.state" class="input" maxlength="2" required />
          </div>
          <div>
            <label>Inscrição Estadual</label>
            <input v-model="form.state_registration" class="input" />
          </div>
          <div>
            <label>Área Total (ha)</label>
            <input v-model.number="form.total_area" class="input" type="number" step="0.01" min="0" required />
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
        <h3>Editar Propriedade</h3>
        <form @submit.prevent="submitEdit" class="form-grid">
          <div>
            <label>Produtor (ID)</label>
            <input v-model.number="form.farmer_id" class="input" type="number" min="1" required />
          </div>
          <div class="col-span-2">
            <label>Nome</label>
            <input v-model="form.name" class="input" required />
          </div>
          <div>
            <label>Município</label>
            <input v-model="form.municipality" class="input" required />
          </div>
          <div>
            <label>UF</label>
            <input v-model="form.state" class="input" maxlength="2" required />
          </div>
          <div>
            <label>Inscrição Estadual</label>
            <input v-model="form.state_registration" class="input" />
          </div>
          <div>
            <label>Área Total (ha)</label>
            <input v-model.number="form.total_area" class="input" type="number" step="0.01" min="0" required />
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
        <h3>Detalhes da Propriedade</h3>
        <div class="details">
          <div><strong>Nome:</strong> {{ selected.name }}</div>
          <div><strong>Produtor ID:</strong> {{ selected.farmer_id }}</div>
          <div><strong>Município:</strong> {{ selected.municipality }}</div>
          <div><strong>UF:</strong> {{ selected.state }}</div>
          <div><strong>Inscrição Estadual:</strong> {{ selected.state_registration || '-' }}</div>
          <div><strong>Área Total (ha):</strong> {{ selected.total_area }}</div>
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
        <p>Tem certeza que deseja excluir a propriedade "{{ selected.name }}"?</p>
        <div class="modal-actions">
          <button class="btn-secondary" @click="showConfirmDelete = false">Cancelar</button>
          <button class="btn-danger" @click="confirmDelete">Excluir</button>
        </div>
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
  margin-bottom: 1rem;
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
}

.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.left, .right {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.input {
  padding: 0.625rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
}

.btn-primary, .btn-secondary, .btn-danger {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 0.9rem;
  border-radius: 0.375rem;
  border: 1px solid transparent;
  cursor: pointer;
}

.btn-primary { background: #059669; color: #fff; }
.btn-secondary { background: #f3f4f6; color: #374151; border-color: #d1d5db; }
.btn-danger { background: #dc2626; color: #fff; }

.btn-icon { font-size: 1rem; }

.table-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  overflow: hidden;
}

.table { width: 100%; border-collapse: collapse; }
.table thead { background: #f9fafb; }
.table th, .table td { padding: 0.75rem 1rem; border-bottom: 1px solid #e5e7eb; text-align: left; }
.table th { color: #374151; font-weight: 600; }
.muted { color: #6b7280; text-align: center; }

.row-actions { display: flex; gap: 0.25rem; }
.icon { background: #f3f4f6; border: 1px solid #d1d5db; color: #374151; padding: 0.35rem; border-radius: 0.375rem; cursor: pointer; }
.icon.danger { background: #fee2e2; border-color: #fecaca; color: #991b1b; }

.modal-backdrop {
  position: fixed; inset: 0; background: rgba(0,0,0,0.4);
  display: flex; align-items: center; justify-content: center;
  padding: 1rem; z-index: 1000;
}

.modal {
  background: #fff; border-radius: 0.5rem; padding: 1rem; width: 100%; max-width: 720px;
  border: 1px solid #e5e7eb;
  max-height: 90vh; overflow-y: auto; position: relative; z-index: 1001;
}

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
.form-grid label { display: block; margin-bottom: 0.25rem; color: #374151; font-weight: 500; }
.form-grid .col-span-2 { grid-column: span 2; }

.modal-actions { display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 0.5rem; }

.details { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin: 1rem 0; }

@media (max-width: 768px) {
  .form-grid { grid-template-columns: 1fr; }
  .details { grid-template-columns: 1fr; }
}
</style>
