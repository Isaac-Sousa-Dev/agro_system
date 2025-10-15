<template>
  <div class="page">

    <div class="text-black py-6">
      <h1 class="text-3xl font-bold mb-2">Produtores</h1>
      <p class="text-gray-500">Gerencie os produtores rurais cadastrados</p>
    </div>

    <div class="flex justify-between items-center mb-4">
      <div class="w-full">
        <button class="btn-primary" @click="openCreate">
          <span class="btn-icon"><i class="pi pi-plus"></i></span>
          Novo Produtor
        </button>
      </div>
      <div class="w-full flex gap-2">
        <InputGroup>
            <InputText fluid type="text" v-model="search" placeholder="Buscar por nome, CPF/CNPJ, e-mail..." />
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
            <th>CPF/CNPJ</th>
            <th>Telefone</th>
            <th>E-mail</th>
            <th>Cadastro</th>
            <th style="width: 1%">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="store.loading">
            <td colspan="6" class="muted">Carregando...</td>
          </tr>
          <tr v-else-if="!filtered.length">
            <td colspan="6" class="muted">Nenhum produtor encontrado</td>
          </tr>
          <tr v-for="producer in filtered" :key="producer.id">
            <td>{{ producer.name }}</td>
            <td v-mask="'cpf_cnpj'">{{ producer.cpf_cnpj }}</td>
            <td v-mask="'phone'">{{ producer.phone || '-' }}</td>
            <td>{{ producer.email || '-' }}</td>
            <td v-mask="'date'">{{ producer.registration_date || '-' }}</td>
            <td>
              <div class="row-actions">
                <button class="icon" title="Ver" @click="openView(producer)"><i class="pi pi-eye"></i></button>
                <button class="icon" title="Editar" @click="openEdit(producer)"><i class="pi pi-pencil"></i></button>
                <button class="icon danger" title="Excluir" @click="openDelete(producer)"><i class="pi pi-trash"></i></button>
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

    <ProducerCreateModal v-model="showCreate" :value="form" @save="handleCreate" />

    <ProducerEditModal v-model="showEdit" :value="form" @save="handleEdit" />

    <ProducerViewModal v-model="showView" :value="selected as Producer" />

    <ProducerDeleteModal v-model="showConfirmDelete" :name="selected?.name || ''" @confirm="confirmDelete" />

  </div>

</template>

<script setup lang="ts">
import { onMounted, reactive, ref, computed } from 'vue'
import { useToast } from 'primevue/usetoast'
import { useProducerStore } from '@/stores/producer'
import type { Producer } from '@/types/producer'
import InputText from 'primevue/inputtext';
import Paginator from 'primevue/paginator'
import type { Property } from '@/types/property';
import ProducerCreateModal from '@/components/modals/producer/ProducerCreateModal.vue'
import ProducerEditModal from '@/components/modals/producer/ProducerEditModal.vue'
import ProducerViewModal from '@/components/modals/producer/ProducerViewModal.vue'
import ProducerDeleteModal from '@/components/modals/producer/ProducerDeleteModal.vue'
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';

const store = useProducerStore()
const toast = useToast()
const search = ref('')
const showCreate = ref(false)
const showEdit = ref(false)
const showView = ref(false)
const showConfirmDelete = ref(false)

interface PropertyForm {
  name: string
  municipality: string
  state: string
  state_registration?: string | null
  total_area: string,
  open?: boolean,
  farmer_id?: number | null
}
interface ProducerForm {
  name: string
  cpf_cnpj: string
  phone?: string | null
  email?: string | null
  address?: string | null
  registration_date?: string | null
  properties: PropertyForm[]
}

const form = reactive<ProducerForm>({
  name: '',
  cpf_cnpj: '',
  phone: '',
  email: '',
  address: '',
  registration_date: '',
  properties: [] as PropertyForm[]
})

const selected = ref<Producer | null>(null)

const filtered = computed(() => {
  if (!search.value) return store.producers
  const q = search.value.toLowerCase()
  return store.producers.filter(producer =>
    producer.name.toLowerCase().includes(q) ||
    (producer.cpf_cnpj?.toLowerCase().includes(q)) ||
    (producer.email?.toLowerCase().includes(q)) ||
    (producer.phone?.toLowerCase().includes(q))
  )
})

async function onPage(event: { first: number; rows: number; page: number }) {
  const nextPage = (event.page ?? 0) + 1
  await store.list(nextPage, event.rows)
}

function resetForm() {
  form.name = ''
  form.cpf_cnpj = ''
  form.phone = ''
  form.email = ''
  form.address = ''
  form.registration_date = ''
  form.properties = []
}

async function load() {
  try {
    await store.list()
    // toast.add({ severity: 'success', summary: 'Produtores', detail: 'Lista atualizada', life: 2000 })
  } catch {
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao carregar produtores', life: 3000 })
  }
}

function openCreate() {
  resetForm()
  showCreate.value = true
}

function openView(item: Producer) {
  selected.value = item
  showView.value = true
}

function openEdit(item: Producer) {
  selected.value = item
  form.name = item.name
  form.cpf_cnpj = item.cpf_cnpj
  form.phone = item.phone ?? ''
  form.email = item.email ?? ''
  form.address = item.address ?? ''
  form.registration_date = item.registration_date ?? ''
  form.properties = item.properties.map((property: Property) => ({
    id: property.id,
    name: property.name,
    municipality: property.municipality,
    state: property.state,
    state_registration: property.state_registration ?? '',
    total_area: property.total_area.toString(),
  }))
  showEdit.value = true
}

function openDelete(item: Producer) {
  selected.value = item
  showConfirmDelete.value = true
}

async function submitCreate() {
  const payload = {
    name: form.name,
    cpf_cnpj: form.cpf_cnpj,
    phone: form.phone,
    email: form.email,
    address: form.address,
    registration_date: form.registration_date,
    properties: form.properties.map((property: PropertyForm) => ({
      name: property.name,
      municipality: property.municipality,
      state: property.state,
      state_registration: property.state_registration ?? '',
      total_area: Number(property.total_area || 0)
    }))
  }

  const payloadForApi = payload as unknown as Omit<Producer, 'id' | 'created_at' | 'updated_at'>
  try {
    await store.create(payloadForApi)
    toast.add({ severity: 'success', summary: 'Produtor', detail: 'Criado com sucesso', life: 2500 })
    showCreate.value = false
    resetForm()
  } catch {
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao criar produtor', life: 3000 })
  }
}

function handleCreate(value: ProducerForm) {
  Object.assign(form, value)
  void submitCreate()
}

async function submitEdit() {
  if (!selected.value) return
  try {
    await store.update(selected.value.id, { ...form })
    toast.add({ severity: 'success', summary: 'Produtor', detail: 'Atualizado com sucesso', life: 2500 })
    showEdit.value = false
  } catch {
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao atualizar produtor', life: 3000 })
  }
}

function handleEdit(value: ProducerForm) {
  Object.assign(form, value)
  void submitEdit()
}

async function confirmDelete() {
  if (!selected.value) return
  try {
    await store.remove(selected.value.id)
    toast.add({ severity: 'success', summary: 'Produtor', detail: 'Excluído com sucesso', life: 2500 })
    showConfirmDelete.value = false
    load()
  } catch {
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao excluir produtor', life: 3000 })
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

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 0.75rem;
  border-top: 1px solid #e5e7eb;
  background: #f9fafb;
}
.page-btn {
  background: #f3f4f6; border: 1px solid #d1d5db; color: #374151;
  padding: 0.4rem 0.6rem; border-radius: 0.375rem; cursor: pointer;
}
.page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.page-info { color: #6b7280; font-size: 0.9rem; }

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
