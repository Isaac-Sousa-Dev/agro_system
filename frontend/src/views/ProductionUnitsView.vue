<template>
  <div class="page-container">
    <div class="page-header">
      <h1>Unidades de Produção</h1>
      <p>Gerencie as unidades de produção das propriedades rurais</p>
    </div>

    <div class="toolbar">
      <div class="toolbar-left">
        <button class="btn-primary" @click="openCreate">
          <i class="pi pi-plus"></i>
          Nova Unidade de Produção
        </button>
      </div>
      <div class="toolbar-right">
        <InputGroup>
            <InputText fluid type="text" v-model="search" placeholder="Buscar por cultura ou propriedade (ID)..." />
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
            <th>Cultura</th>
            <th>Propriedade</th>
            <th>Coordenadas Geográficas</th>
            <th>Total de Área (ha)</th>
            <th style="width: 1%">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="store.loading">
            <td colspan="5" class="muted">Carregando...</td>
          </tr>
          <tr v-else-if="!filtered.length">
            <td colspan="5" class="muted">Nenhuma unidade de produção encontrada</td>
          </tr>
          <tr v-for="productionUnit in filtered" :key="productionUnit.id">
            <td>{{ productionUnit.crop_name }}</td>
            <td>{{ productionUnit.property?.name }}</td>
            <td>{{ productionUnit.geographic_coordinates }}</td>
            <td>{{ productionUnit.total_area_ha }}</td>
            <td>
              <div class="row-actions">
                <button class="icon" title="Ver" @click="openView(productionUnit)"><i class="pi pi-eye"></i></button>
                <button class="icon" title="Editar" @click="openEdit(productionUnit)"><i class="pi pi-pencil"></i></button>
                <button class="icon danger" title="Excluir" @click="openDelete(productionUnit)"><i class="pi pi-trash"></i></button>
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

    <ProductionUnitCreateModal v-model="showCreate" :value="form" @save="submitCreate" />

  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, reactive, computed } from 'vue'
import { useToast } from 'primevue/usetoast'
import { useProductionUnitStore } from '@/stores/productionUnit'
import type { ProductionUnit } from '@/types/productionUnit'
import InputText from 'primevue/inputtext';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import Paginator from 'primevue/paginator';
import ProductionUnitCreateModal from '@/components/modals/productionUnit/ProductionUnitCreateModal.vue';

const store = useProductionUnitStore()
const toast = useToast()
const search = ref('')
const showCreate = ref(false)
const showEdit = ref(false)
const showView = ref(false)
const showConfirmDelete = ref(false)

const form = reactive<Omit<ProductionUnit, 'id' | 'created_at' | 'updated_at'>>({
  property_id: null,
  crop_name: '',
  total_area_ha: '',
  geographic_coordinates: ''
})

const selected = ref<ProductionUnit | null>(null)

const filtered = computed(() => {
  if (!search.value) return store.units
  const q = search.value.toLowerCase()
  return store.units.filter(u =>
    u.crop_name.toLowerCase().includes(q) || String(u.property_id).includes(q)
  )
})

async function onPage(event: { first: number; rows: number; page: number }) {
  const nextPage = (event.page ?? 0) + 1
  await store.list(nextPage, event.rows)
}

function resetForm() {
  form.property_id = 0
  form.crop_name = ''
  form.total_area_ha = ''
  form.geographic_coordinates = ''
}

async function load() {
  try {
    await store.list()
    // toast.add({ severity: 'success', summary: 'Unidades', detail: 'Lista atualizada', life: 2000 })
  } catch (e: unknown) {
    console.error(e)
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao carregar unidades', life: 3000 })
  }
}
function openCreate() { resetForm(); showCreate.value = true }
function openView(item: ProductionUnit) { selected.value = item; showView.value = true }
function openEdit(item: ProductionUnit) {
  selected.value = item
  form.property_id = item.property_id
  form.crop_name = item.crop_name
  form.total_area_ha = item.total_area_ha
  form.geographic_coordinates = item.geographic_coordinates ?? ''
  showEdit.value = true
}
function openDelete(item: ProductionUnit) { selected.value = item; showConfirmDelete.value = true }

async function submitCreate() {
  try {
    await store.create({ ...form })
    toast.add({ severity: 'success', summary: 'Unidade', detail: 'Criada com sucesso', life: 2500 })
    showCreate.value = false
    resetForm()
  } catch (e: unknown) {
    console.error(e)
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao criar unidade', life: 3000 })
  }
}
// async function submitEdit() {
//   if (!selected.value) return
//   try {
//     await store.update(selected.value.id, { ...form })
//     toast.add({ severity: 'success', summary: 'Unidade', detail: 'Atualizada com sucesso', life: 2500 })
//     showEdit.value = false
//   } catch (e: unknown) {
//     console.error(e)
//     toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao atualizar unidade', life: 3000 })
//   }
// }
// async function confirmDelete() {
//   if (!selected.value) return
//   try {
//     await store.remove(selected.value.id)
//     toast.add({ severity: 'success', summary: 'Unidade', detail: 'Excluída com sucesso', life: 2500 })
//     showConfirmDelete.value = false
//   } catch (e: unknown) {
//     console.error(e)
//     toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao excluir unidade', life: 3000 })
//   }
// }

onMounted(load)
</script>


