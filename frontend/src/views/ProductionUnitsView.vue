<template>
  <div class="unidades-producao">
    <div class="text-black py-6">
      <h1 class="text-3xl font-bold mb-2">Unidades de Produção</h1>
      <p class="text-gray-500">Gerencie as unidades de produção das propriedades rurais</p>
    </div>

    <div class="flex justify-between items-center mb-4">
      <div class="w-full">
        <button class="btn-primary" @click="openCreate">
          <span class="btn-icon"><i class="pi pi-plus"></i></span>
          Nova Unidade de Produção
        </button>
      </div>
        <div class="w-full flex gap-2">
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

    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
      <table class="w-full border-collapse">
        <thead class="bg-gray-100">
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
            <td colspan="6" class="muted">Carregando...</td>
          </tr>
          <tr v-else-if="!filtered.length">
            <td colspan="6" class="muted">Nenhuma propriedade encontrada</td>
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
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 0.9rem;
  border-radius: 0.375rem;
  border: 1px solid transparent;
  cursor: pointer;
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

