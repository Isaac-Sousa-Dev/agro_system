<template>
  <div class="page">

    <div class="text-black py-6">
      <h1 class="text-3xl font-bold mb-2">Produtores</h1>
      <p class="text-gray-500">Gerencie os produtores rurais cadastrados</p>
    </div>

    <div class="toolbar">
      <div class="left">
        <button class="btn-primary" @click="openCreate">
          <span class="btn-icon"><i class="pi pi-plus"></i></span>
          Novo Produtor
        </button>
      </div>
      <div class="right">
        <InputText type="text" v-model="search" placeholder="Buscar por nome, CPF/CNPJ, e-mail..." />
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
            <td>{{ producer.cpf_cnpj }}</td>
            <td>{{ producer.phone || '-' }}</td>
            <td>{{ producer.email || '-' }}</td>
            <td>{{ producer.registration_date || '-' }}</td>
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
    </div>

    <!-- Criar -->
    <div v-if="showCreate" class="modal-backdrop">
      <div class="modal">
        <h3 class="text-black text-lg font-bold mb-2">Novo Produtor</h3>
        <form @submit.prevent="submitCreate">

          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
              <label>Nome</label>
              <InputText type="text" v-model="form.name" placeholder="Digite o nome do produtor" />
            </div>
            <div class="flex flex-col gap-1">
              <label>CPF/CNPJ</label>
              <InputText type="text" v-model="form.cpf_cnpj" placeholder="000.000.000-00" />
            </div>
            <div class="flex flex-col gap-1">
              <label>Telefone</label>
              <InputText type="text" v-model="form.phone" placeholder="(00) 00000-0000" />
            </div>
            <div class="flex flex-col gap-1">
              <label>E-mail</label>
              <InputText type="email" v-model="form.email" placeholder="exemplo@email.com" />
            </div>
            <div class="flex flex-col gap-1">
              <label>Endereço</label>
              <InputText type="text" v-model="form.address" placeholder="Rua, número, bairro, cidade, estado" />
            </div>
          </div>

          <div class="mt-4">
            <Tabs value="0">
              <TabList>
                <Tab value="0">Propriedades</Tab>
              </TabList>
              <TabPanels>
                <TabPanel value="0">
                  <div class="flex justify-end mb-2">
                    <Button type="button" class="" @click="addPropertySection">
                      <span class="btn-icon"><i class="pi pi-plus"></i></span>
                      Adicionar propriedade
                    </Button>
                  </div>

                  <div class="space-y-3">
                    <div v-for="(prop, idx) in form.properties" :key="idx" class="accordion">
                      <div class="bg-gray-100 flex justify-between items-center p-2 rounded-md" @click="prop.open = !prop.open">
                        <div class="title">
                          <i class="pi" :class="prop.open ? 'pi-angle-down' : 'pi-angle-right'"></i>
                          <span class="ml-4">Propriedade {{ idx + 1 }} — {{ prop.name || 'Sem nome' }}</span>
                        </div>
                        <div class="actions">
                          <Button label="Danger" severity="danger" @click.stop="removePropertySection(idx)">
                            <i class="pi pi-trash"></i>
                          </Button>
                        </div>
                      </div>
                      <div v-show="prop.open" class="mt-3 p-2">
                        <div class="grid grid-cols-2 gap-4">
                          <div class="flex flex-col gap-1">
                            <label>Nome</label>
                            <InputText type="text" v-model="prop.name" placeholder="Ex.: Fazenda São João" />
                          </div>
                          <div class="flex flex-col gap-1">
                            <label>Município</label>
                            <InputText type="text" v-model="prop.municipality" placeholder="Ex.: Viçosa do Ceará" />
                          </div>
                          <div class="flex flex-col gap-1">
                            <label>UF</label>
                            <InputText type="text" v-model="prop.state" maxlength="2" placeholder="UF" />
                          </div>
                          <div class="flex flex-col gap-1">
                            <label>Inscrição Estadual</label>
                            <InputText type="text" v-model="prop.state_registration" placeholder="Opcional" />
                          </div>
                          <div class="flex flex-col gap-1">
                            <label>Área Total (ha)</label>
                            <InputText type="number" v-model.number="prop.total_area" placeholder="0.00" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </TabPanel>
              </TabPanels>
            </Tabs>
          </div>
          <div class="mt-4 flex justify-end gap-2">
            <Button label="Secondary" severity="secondary" class="bg-gray-200" @click="showCreate = false">Cancelar</Button>
            <Button class="px-10" type="submit">Salvar</Button>
          </div>
        </form>
      </div>
    </div>

    <!-- Editar -->
    <div v-if="showEdit" class="modal-backdrop">
      <div class="modal">
        <h3 class="text-black text-lg font-bold mb-2">Editar Produtor</h3>

        <form @submit.prevent="submitEdit">
          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
              <label>Nome</label>
              <InputText type="text" v-model="form.name" placeholder="Digite o nome do produtor" />
            </div>
            <div class="flex flex-col gap-1">
              <label>CPF/CNPJ</label>
              <InputText type="text" v-model="form.cpf_cnpj" placeholder="000.000.000-00" />
            </div>
            <div class="flex flex-col gap-1">
              <label>Telefone</label>
              <InputText type="text" v-model="form.phone" placeholder="(00) 00000-0000" />
            </div>
            <div class="flex flex-col gap-1">
              <label>E-mail</label>
              <InputText type="email" v-model="form.email" placeholder="exemplo@email.com" />
            </div>
            <div class="flex flex-col gap-1">
              <label>Endereço</label>
              <InputText type="text" v-model="form.address" placeholder="Rua, número, bairro, cidade, estado" />
            </div>
          </div>

          <div class="mt-4">
            <Tabs value="0">
              <TabList>
                <Tab value="0">Propriedades</Tab>
              </TabList>
              <TabPanels>
                <TabPanel value="0">
                  <div class="flex justify-end mb-2">
                    <Button type="button" class="" @click="addPropertySection">
                      <span class="btn-icon"><i class="pi pi-plus"></i></span>
                      Adicionar propriedade
                    </Button>
                  </div>

                  <div class="space-y-3">
                    <div v-for="(prop, idx) in form.properties" :key="idx" class="accordion">
                      <div class="bg-gray-100 flex justify-between items-center p-2 rounded-md" @click="prop.open = !prop.open">
                        <div class="title">
                          <i class="pi" :class="prop.open ? 'pi-angle-down' : 'pi-angle-right'"></i>
                          <span class="ml-4">Propriedade {{ idx + 1 }} — {{ prop.name || 'Sem nome' }}</span>
                        </div>
                        <div class="actions">
                          <Button label="Danger" severity="danger" @click.stop="removePropertySection(idx)">
                            <i class="pi pi-trash"></i>
                          </Button>
                        </div>
                      </div>
                      <div v-show="prop.open" class="mt-3 p-2">
                        <div class="grid grid-cols-2 gap-4">
                          <div class="flex flex-col gap-1">
                            <label>Nome</label>
                            <InputText type="text" v-model="prop.name" placeholder="Ex.: Fazenda São João" />
                          </div>
                          <div class="flex flex-col gap-1">
                            <label>Município</label>
                            <InputText type="text" v-model="prop.municipality" placeholder="Ex.: Viçosa do Ceará" />
                          </div>
                          <div class="flex flex-col gap-1">
                            <label>UF</label>
                            <InputText type="text" v-model="prop.state" maxlength="2" placeholder="UF" />
                          </div>
                          <div class="flex flex-col gap-1">
                            <label>Inscrição Estadual</label>
                            <InputText type="text" v-model="prop.state_registration" placeholder="Opcional" />
                          </div>
                          <div class="flex flex-col gap-1">
                            <label>Área Total (ha)</label>
                            <InputText type="number" v-model.number="prop.total_area" placeholder="0.00" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </TabPanel>
              </TabPanels>
            </Tabs>
          </div>
          <div class="mt-4 flex justify-end gap-2">
            <Button label="Secondary" severity="secondary" class="bg-gray-200" @click="showEdit = false">Cancelar</Button>
            <Button class="px-10" type="submit">Salvar Alterações</Button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="showView && selected" class="modal-backdrop">
      <div class="modal">
        <h3>Detalhes do Produtor</h3>
        <div class="details">
          <div><strong>Nome:</strong> {{ selected.name }}</div>
          <div><strong>CPF/CNPJ:</strong> {{ selected.cpf_cnpj }}</div>
          <div><strong>Telefone:</strong> {{ selected.phone || '-' }}</div>
          <div><strong>E-mail:</strong> {{ selected.email || '-' }}</div>
          <div><strong>Endereço:</strong> {{ selected.address || '-' }}</div>
          <div><strong>Data de Cadastro:</strong> {{ selected.registration_date || '-' }}</div>
          <div><strong>Criado em:</strong> {{ selected.created_at }}</div>
          <div><strong>Atualizado em:</strong> {{ selected.updated_at }}</div>
        </div>
        <div class="modal-actions">
          <Button class="btn-secondary" @click="showView = false">Fechar</Button>
        </div>
      </div>
    </div>

    <div v-if="showConfirmDelete && selected" class="modal-backdrop">
      <div class="modal">
        <h3>Confirmar Exclusão</h3>
        <p>Tem certeza que deseja excluir o produtor "{{ selected.name }}"?</p>
        <div class="modal-actions">
          <Button class="btn-secondary" @click="showConfirmDelete = false">Cancelar</Button>
          <Button class="btn-danger" @click="confirmDelete">Excluir</Button>
        </div>
      </div>
    </div>

  </div>

</template>

<script setup lang="ts">
import { onMounted, reactive, ref, computed } from 'vue'
import { useProducerStore } from '@/stores/producer'
import type { Producer } from '@/types/producer'
import InputText from 'primevue/inputtext';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
import Button from 'primevue/button';
import type { Property } from '@/types/property';

const store = useProducerStore()
const search = ref('')
const showCreate = ref(false)
const showEdit = ref(false)
const showView = ref(false)
const showConfirmDelete = ref(false)

// Tipos auxiliares para o formulário de propriedades
interface PropertyForm {
  name: string
  municipality: string
  state: string
  state_registration?: string | null
  total_area: string // string no form, convertida no submit
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
  await store.list()
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
  await store.create(payloadForApi)
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

function addPropertySection() {
  form.properties.push({
    name: '',
    municipality: '',
    state: '',
    state_registration: '',
    total_area: '',
    open: true,
  })
}

function removePropertySection(index: number) {
  form.properties.splice(index, 1)
}
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
