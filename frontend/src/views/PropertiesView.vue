<template>
  <div class="page">
    <div class="text-black py-6">
      <h1 class="text-3xl font-bold mb-2">Propriedades</h1>
      <p class="text-gray-500">Gerencie as propriedades rurais e suas informações</p>
    </div>

    <div class="flex justify-between items-center mb-4">
      <div class="w-full">
        <button class="btn-primary" @click="openCreate">
          <span class="btn-icon"><i class="pi pi-plus"></i></span>
          Nova Propriedade
        </button>
      </div>
        <div class="w-full flex gap-2">
          <InputGroup>
              <InputText fluid type="text" v-model="search" placeholder="Buscar por nome, município, UF..." />
              <InputGroupAddon>
                  <span class="pi pi-search"></span>
              </InputGroupAddon>
          </InputGroup>
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
      <Paginator
        :first="(store.currentPage - 1) * store.perPage"
        :rows="store.perPage"
        :totalRecords="store.total"
        @page="onPage"
      />
    </div>

    <PropertyCreateModal v-model="showCreate" :value="form" @save="handleCreate" />

    <PropertyEditModal v-model="showEdit" :value="form" @save="handleEdit" />

    <PropertyViewModal v-model="showView" :value="selected as Property" />

    <PropertyDeleteModal v-model="showConfirmDelete" :name="selected?.name || ''" @confirm="confirmDelete" />
  </div>
</template>

<script setup lang="ts">
import { onMounted, reactive, ref, computed } from 'vue'
import { useToast } from 'primevue/usetoast'
import { usePropertyStore } from '@/stores/property'
import type { Property, PropertyForm } from '@/types/property'
import InputText from 'primevue/inputtext';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import Paginator from 'primevue/paginator';
import PropertyCreateModal from '@/components/modals/property/PropertyCreateModal.vue';
import PropertyEditModal from '@/components/modals/property/PropertyEditModal.vue';
import PropertyViewModal from '@/components/modals/property/PropertyViewModal.vue';
import PropertyDeleteModal from '@/components/modals/property/PropertyDeleteModal.vue';

const store = usePropertyStore()
const toast = useToast()
const search = ref('')
const showCreate = ref(false)

const showEdit = ref(false)
const showView = ref(false)
const showConfirmDelete = ref(false)

const form = reactive<PropertyForm>({
  name: '',
  farmer_id: null,
  municipality: '',
  state: '',
  state_registration: '',
  total_area: '',
  open: false,
  productionUnits: [],
  herds: []
})

const selected = ref<Property | null>(null)

const filtered = computed(() => {
  if (!search.value) return store.properties
  const q = search.value.toLowerCase()
  return store.properties.filter(property =>
    property.name.toLowerCase().includes(q) ||
    property.municipality.toLowerCase().includes(q) ||
    property.state.toLowerCase().includes(q)
  )
})

async function onPage(event: { first: number; rows: number; page: number }) {
  const nextPage = (event.page ?? 0) + 1
  await store.list(nextPage, event.rows)
}

function resetForm() {
  form.name = ''
  form.municipality = ''
  form.state = ''
  form.state_registration = ''
  form.total_area = ''
  form.productionUnits = []
  form.herds = []
}

async function load() {
  try {
    await store.list()
    // toast.add({ severity: 'success', summary: 'Propriedades', detail: 'Lista atualizada', life: 2000 })
  } catch (e) {
    console.error(e)
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao carregar propriedades', life: 3000 })
  }
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
  form.name = item.name
  form.municipality = item.municipality
  form.state = item.state
  form.state_registration = item.state_registration ?? ''
  form.total_area = item.total_area.toString()
  form.productionUnits = item?.productionUnits ?? []
  form.herds = item?.herds ?? []
  showEdit.value = true
}

function openDelete(item: Property) {
  selected.value = item
  showConfirmDelete.value = true
}

async function submitCreate() {
  try {
    await store.create({ ...form, productionUnits: form.productionUnits ?? [], herds: form.herds ?? [] })
    toast.add({ severity: 'success', summary: 'Propriedade', detail: 'Criada com sucesso', life: 2500 })
    showCreate.value = false
    resetForm()
  } catch (e) {
    console.error(e)
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao criar propriedade', life: 3000 })
  }
}

function handleCreate(value: PropertyForm) {
  console.log(value, 'value')
  Object.assign(form, value);
  void submitCreate()
}

async function submitEdit() {
  if (!selected.value) return
  try {
    await store.update(selected.value.id, { ...form })
    toast.add({ severity: 'success', summary: 'Propriedade', detail: 'Atualizada com sucesso', life: 2500 })
    showEdit.value = false
  } catch (e) {
    console.error(e)
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao atualizar propriedade', life: 3000 })
  }
}

function handleEdit(value: PropertyForm) {
  Object.assign(form, value);
  void submitEdit()
}

async function confirmDelete() {
  if (!selected.value) return
  try {
    await store.remove(selected.value.id)
    toast.add({ severity: 'success', summary: 'Propriedade', detail: 'Excluída com sucesso', life: 2500 })
    showConfirmDelete.value = false
  } catch (e) {
    console.error(e)
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao excluir propriedade', life: 3000 })
  }
}

onMounted(load)
</script>

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
