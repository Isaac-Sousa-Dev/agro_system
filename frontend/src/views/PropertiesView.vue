<template>
  <div class="page-container">
    <div class="page-header">
      <h1>Propriedades</h1>
      <p>Gerencie as propriedades rurais e suas informações</p>
    </div>

    <div class="toolbar">
      <div class="toolbar-left">
        <button class="btn-primary" @click="openCreate">
          <i class="pi pi-plus"></i>
          Nova Propriedade
        </button>
      </div>
      <div class="toolbar-right">
        <!-- <button class="btn-primary bg-sky-400  text-white px-4 py-2 rounded-md hover:bg-sky-300 w-1/2 bg-gradient-to-r from-sky-400 to-sky-500 " @click="openCreateHerd">Exportar Excel</button> -->
        <button @click="exportExcel" class="btn-export w-1/2">Exportar Excel</button>
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

    <PropertyCreateModal
        ref="createModalRef"
        v-model="showCreate"
        :value="form"
        @save="handleCreate"
    />

    <PropertyEditModal
        v-model="showEdit"
        :value="form"
        @save="handleEdit"
    />

    <PropertyViewModal
        v-model="showView"
        :value="selected as Property"
    />

    <PropertyDeleteModal
        v-model="showConfirmDelete"
        :name="selected?.name || ''"
        @confirm="confirmDelete"
    />
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
const createModalRef = ref()

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
  form.farmer_id = null
}

async function load() {
  try {
    await store.list()
  } catch (e) {
    console.error(e)
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao carregar propriedades', life: 3000 })
  }
}

const exportExcel = async () => {
  try {
    await store.exportExcel()
  } catch (e) {
    console.error(e)
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao exportar Excel', life: 3000 })
  }
}

function openCreate() {
  resetForm()
  showCreate.value = true
  createModalRef.value?.clearAllErrors()
}

function openView(item: Property) {
  selected.value = item
  showView.value = true
}

function openEdit(item: Property) {
  selected.value = item
  form.name = item.name
  form.farmer_id = item.farmer_id
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
  } catch (e: unknown) {
    console.error(e)
    if (e && typeof e === 'object' && 'response' in e) {
      const error = e as { response?: { data?: { errors?: Record<string, string[]> } } }
      if (error.response?.data?.errors) {
        createModalRef.value?.setValidationErrors(error.response.data.errors)
        toast.add({ severity: 'error', summary: 'Erro', detail: 'Verifique os campos obrigatórios', life: 3000 })
        return
      }
    }
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
    load()
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

