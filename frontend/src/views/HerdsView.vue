<template>
  <div class="page-container">
    <div class="page-header">
      <h1>🐄 Gestão de Rebanhos</h1>
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
            <td v-mask="'date'">{{ h.data_atualizacao || '-' }}</td>
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
          <div class="form-group">
            <label class="form-label">Propriedade (ID)</label>
            <input v-model.number="form.propriedade_id" class="form-input" type="number" min="1" required />
          </div>
          <div class="form-group">
            <label class="form-label">Espécie</label>
            <input v-model="form.especie" class="form-input" required />
          </div>
          <div class="form-group">
            <label class="form-label">Quantidade</label>
            <input v-model.number="form.quantidade" class="form-input" type="number" min="0" required />
          </div>
          <div class="form-group">
            <label class="form-label">Finalidade</label>
            <input v-model="form.finalidade" class="form-input" />
          </div>
          <div class="form-group">
            <label class="form-label">Data de Atualização</label>
            <input v-model="form.data_atualizacao" class="form-input" type="date" />
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
          <div><strong>Data de Atualização:</strong> <span v-mask="'date'">{{ selected.data_atualizacao || '-' }}</span></div>
          <div><strong>Criado em:</strong> <span v-mask="'datetime'">{{ selected.created_at }}</span></div>
          <div><strong>Atualizado em:</strong> <span v-mask="'datetime'">{{ selected.updated_at }}</span></div>
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


