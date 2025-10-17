<template>
    <div class="page-container">
        <div class="page-header">
            <h1>Produtores</h1>
            <p>Gerencie os produtores rurais cadastrados</p>
        </div>

        <div class="toolbar">
            <div class="toolbar-left">
                <button class="btn-primary" @click="openCreate">
                    <i class="pi pi-plus"></i>
                    Novo Produtor
                </button>
            </div>
            <div class="toolbar-right">
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
                                <button class="icon" title="Ver" @click="openView(producer)"><i
                                        class="pi pi-eye"></i></button>
                                <button class="icon" title="Editar" @click="openEdit(producer)"><i
                                        class="pi pi-pencil"></i></button>
                                <button class="icon danger" title="Excluir" @click="openDelete(producer)"><i
                                        class="pi pi-trash"></i></button>
                                <button class="icon" title="Exportar Rebanhos" @click="exportPdf(producer.id)"><i
                                        class="pi pi-file-pdf"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Paginator :first="(store.currentPage - 1) * store.perPage" :rows="store.perPage"
                :totalRecords="store.total" @page="onPage" />
        </div>

        <!-- Modais -->
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
import type { Producer, ProducerForm } from '@/types/producer'
import InputText from 'primevue/inputtext';
import Paginator from 'primevue/paginator'
import type { Property, PropertyForm } from '@/types/property';
import ProducerCreateModal from '@/components/modals/producer/ProducerCreateModal.vue'
import ProducerEditModal from '@/components/modals/producer/ProducerEditModal.vue'
import ProducerViewModal from '@/components/modals/producer/ProducerViewModal.vue'
import ProducerDeleteModal from '@/components/modals/producer/ProducerDeleteModal.vue'
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import { useHerdStore } from '@/stores/herd'
const herdStore = useHerdStore()


const store = useProducerStore()
const toast = useToast()
const search = ref('')
const showCreate = ref(false)
const showEdit = ref(false)
const showView = ref(false)
const showConfirmDelete = ref(false)

const form = reactive<ProducerForm>({
    name: '',
    cpf_cnpj: '',
    phone: '',
    email: '',
    address: '',
    registration_date: '',
    properties: [] as PropertyForm[]
})

const exportPdf = async (producerId: number) => {
  try {
    await herdStore.exportPdf(producerId)
  } catch (e) {
    console.error(e)
    toast.add({ severity: 'error', summary: 'Erro', detail: 'Falha ao exportar PDF', life: 3000 })
  }
}

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
        productionUnits: property.productionUnits ?? [],
        herds: property.herds ?? []
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
