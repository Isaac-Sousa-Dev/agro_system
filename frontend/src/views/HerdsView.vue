<template>
  <div class="page-container">
    <div class="page-header">
      <h1>Rebanhos</h1>
      <p>Administre rebanhos, animais e controle sanitário</p>
    </div>

    <div class="toolbar">
      <div class="toolbar-left">
        <button class="btn-primary" @click="openCreate">
          <i class="pi pi-plus"></i>
          Novo Rebanho
        </button>
      </div>
      <div class="toolbar-right">
        <input class="form-input" v-model="search" placeholder="Buscar por espécie ou propriedade (ID)..." />
        <button class="btn-secondary" @click="load"><i class="pi pi-refresh"></i></button>
      </div>
    </div>

    <div class="table-card">
      <table class="table">
        <thead>
          <tr>
            <th>Propriedade</th>
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
            <td>{{ h.property?.name }}</td>
            <td>{{ h.species }}</td>
            <td>{{ h.quantity }}</td>
            <td>{{ h.purpose ?? '-' }}</td>
            <td v-mask="'date'">{{ h.update_date ?? '-' }}</td>
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
      <Paginator :first="(store.currentPage - 1) * store.perPage" :rows="store.perPage"
                :totalRecords="store.total" @page="onPage" />
    </div>

    <!-- Modais -->
    <HerdCreateModal
      ref="createModalRef"
      v-model="showCreate"
      :value="form"
      @save="submitCreate"
    />
    <HerdEditModal
      ref="editModalRef"
      v-model="showEdit"
      :value="form"
      @save="submitEdit"
    />
    <HerdViewModal
      v-model="showView"
      :value="selected as Herd"
    />
    <HerdDeleteModal
      ref="deleteModalRef"
      v-model="showConfirmDelete"
      :species="selected?.species || ''"
      :property="selected?.property?.name || ''"
      @confirm="confirmDelete"
    />
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, reactive, computed } from 'vue'
import { useToast } from 'primevue/usetoast'
import { useHerdStore } from '@/stores/herd'
import type { Herd } from '@/types/herd'
import HerdCreateModal from '@/components/modals/herd/HerdCreateModal.vue'
import HerdEditModal from '@/components/modals/herd/HerdEditModal.vue'
import HerdViewModal from '@/components/modals/herd/HerdViewModal.vue'
import HerdDeleteModal from '@/components/modals/herd/HerdDeleteModal.vue'
import Paginator from 'primevue/paginator';

const store = useHerdStore()
const toast = useToast()
const search = ref('')
const showCreate = ref(false)
const showEdit = ref(false)
const showView = ref(false)
const showConfirmDelete = ref(false)
const createModalRef = ref()
const editModalRef = ref()

const form = reactive<Omit<Herd, 'id' | 'created_at' | 'updated_at'>>({
  property_id: null,
  species: '',
  quantity: '',
  purpose: '',
  update_date: null
})

const selected = ref<Herd | null>(null)

const filtered = computed(() => {
  if (!search.value) return store.herds
  const q = search.value.toLowerCase()
  return store.herds.filter(herd =>
    herd.species.toLowerCase().includes(q) || String(herd.property_id).includes(q)
  )
})

async function onPage(event: { first: number; rows: number; page: number }) {
  const nextPage = (event.page ?? 0) + 1
  await store.list(nextPage, event.rows)
}

function resetForm() {
  form.property_id = null
  form.species = ''
  form.quantity = ''
  form.purpose = ''
  form.update_date = null
}

async function load() {
  try {
    await store.list()
  } catch (e: unknown) {
    console.error(e)
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao carregar rebanhos', life: 3000 })
  }
}
function openCreate() { resetForm(); showCreate.value = true }
function openView(item: Herd) { selected.value = item; showView.value = true }
function openEdit(item: Herd) {
  selected.value = item
  form.property_id = item.property_id
  form.species = item.species
  form.quantity = item.quantity
  form.purpose = item.purpose ?? ''
  form.update_date = item.update_date ?? null
  showEdit.value = true
}
function openDelete(item: Herd) { selected.value = item; showConfirmDelete.value = true }

async function submitCreate() {
  try {
    await store.create({ ...form })
    toast.add({ severity: 'success', summary: 'Rebanho', detail: 'Criado com sucesso', life: 2500 })
    showCreate.value = false
    resetForm()
    load()
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
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao criar rebanho', life: 3000 })
  }
}

async function submitEdit() {
  if (!selected.value) return
  try {
    await store.update(selected.value.id, { ...form })
    toast.add({ severity: 'success', summary: 'Rebanho', detail: 'Atualizado com sucesso', life: 2500 })
    showEdit.value = false
    load()
  } catch (e) {
    console.error(e)
    if (e && typeof e === 'object' && 'response' in e) {
      const error = e as { response?: { data?: { errors?: Record<string, string[]> } } }
      if (error.response?.data?.errors) {
        editModalRef.value?.setValidationErrors(error.response.data.errors)
        toast.add({ severity: 'error', summary: 'Erro', detail: 'Verifique os campos obrigatórios', life: 3000 })
        return
      }
    }
  }
}

async function confirmDelete() {
  if (!selected.value) return
  try {
    await store.remove(selected.value.id)
    toast.add({ severity: 'success', summary: 'Rebanho', detail: 'Excluído com sucesso', life: 2500 })
    showConfirmDelete.value = false
    load()
  } catch (e) {
    console.error(e)
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao excluir rebanho', life: 3000 })
  }
}

onMounted(load)
</script>


