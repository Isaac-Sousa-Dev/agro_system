<template>
  <div v-if="modelValue" class="modal-backdrop" @click.self="close">
    <div class="modal">
      <h3 class="text-black text-lg font-bold mb-2">Novo Produtor</h3>
      <form @submit.prevent="submit">
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-1">
            <label>Nome*</label>
            <InputText
              type="text"
              v-model="local.name"
              :invalid="!!getFieldError('name')"
              placeholder="Digite o nome do produtor"
              @input="clearFieldError('name')"
            />
            <small v-if="getFieldError('name')" class="text-red-500 text-xs">{{ getFieldError('name') }}</small>
          </div>
          <div class="flex flex-col gap-1">
            <label>CPF/CNPJ*</label>
            <InputText
              type="text"
              v-model="local.cpf_cnpj"
              v-mask="'cpf_cnpj'"
              :invalid="!!getFieldError('cpf_cnpj')"
              placeholder="000.000.000-00"
              @input="clearFieldError('cpf_cnpj')"
            />
            <small v-if="getFieldError('cpf_cnpj')" class="text-red-500 text-xs">{{ getFieldError('cpf_cnpj') }}</small>
          </div>
          <div class="flex flex-col gap-1">
            <label>Telefone</label>
            <InputText
              type="text"
              v-model="local.phone"
              v-mask="'phone'"
              :invalid="!!getFieldError('phone')"
              placeholder="(00) 00000-0000"
              @input="clearFieldError('phone')"
            />
            <small v-if="getFieldError('phone')" class="text-red-500 text-xs">{{ getFieldError('phone') }}</small>
          </div>
          <div class="flex flex-col gap-1">
            <label>E-mail</label>
            <InputText
              type="email"
              v-model="local.email"
              :invalid="!!getFieldError('email')"
              placeholder="exemplo@email.com"
              @input="clearFieldError('email')"
            />
            <small v-if="getFieldError('email')" class="text-red-500 text-xs">{{ getFieldError('email') }}</small>
          </div>
          <div class="flex flex-col gap-1">
            <label>Endereço</label>
            <InputText
              type="text"
              v-model="local.address"
              :invalid="!!getFieldError('address')"
              placeholder="Rua, número, bairro, cidade, estado"
              @input="clearFieldError('address')"
            />
            <small v-if="getFieldError('address')" class="text-red-500 text-xs">{{ getFieldError('address') }}</small>
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
                  <Button type="button" class="" @click="addProperty">
                    <span class="btn-icon"><i class="pi pi-plus"></i></span>
                    Adicionar propriedade
                  </Button>
                </div>

                <div class="space-y-3">
                  <div v-for="(prop, idx) in local.properties" :key="idx" class="accordion">
                    <div class="bg-gray-100 flex justify-between items-center p-2 rounded-md" @click="toggleProperty(idx)">
                      <div class="title">
                        <i class="pi" :class="prop.open ? 'pi-angle-down' : 'pi-angle-right'"></i>
                        <span class="ml-4">Propriedade {{ idx + 1 }} — {{ prop.name || 'Sem nome' }}</span>
                      </div>
                      <div class="actions">
                        <Button label="Danger" severity="danger" @click.stop="removeProperty(idx)">
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
                          <InputText type="text" v-model="prop.state" v-mask="'state'" maxlength="2" placeholder="UF" />
                        </div>
                        <div class="flex flex-col gap-1">
                          <label>Inscrição Estadual</label>
                          <InputText type="text" v-model="prop.state_registration" v-mask="'state_registration'" placeholder="Opcional" />
                        </div>
                        <div class="flex flex-col gap-1">
                          <label>Área Total (ha)</label>
                          <InputText type="text" v-model="prop.total_area" placeholder="00000" />
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
          <Button label="Secondary" severity="secondary" class="bg-gray-200" @click="close">Cancelar</Button>
          <Button class="px-10" type="submit">Salvar</Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import InputText from 'primevue/inputtext'
import Tabs from 'primevue/tabs'
import TabList from 'primevue/tablist'
import Tab from 'primevue/tab'
import TabPanels from 'primevue/tabpanels'
import TabPanel from 'primevue/tabpanel'
import Button from 'primevue/button'
import type { ProducerForm } from '@/types/producer'

interface ValidationErrors {
  [key: string]: string[]
}

const props = defineProps<{
  modelValue: boolean
  value: ProducerForm
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', v: boolean): void
  (e: 'save', v: ProducerForm): void
  (e: 'validation-error', v: ValidationErrors): void
}>()

const local = ref<ProducerForm>(props.value)
const validationErrors = ref<ValidationErrors>({})

watch(() => props.value, (nv) => {
  Object.assign(local.value, JSON.parse(JSON.stringify(nv)))
  validationErrors.value = {}
})

function close() {
  emit('update:modelValue', false)
}

function submit() {
  emit('save', local.value)
}

function addProperty() {
  local.value.properties.push({ name: '', municipality: '', state: '', state_registration: '', total_area: '', open: true, productionUnits: [], herds: [] })
}

function removeProperty(index: number) {
  local.value.properties.splice(index, 1)
}

function toggleProperty(index: number) {
  const item = local.value.properties[index]
  if (!item) return
  item.open = !item.open
}

function setValidationErrors(errors: ValidationErrors) {
  validationErrors.value = errors
  emit('validation-error', errors)
}

function clearFieldError(fieldName: string) {
  if (validationErrors.value[fieldName]) {
    delete validationErrors.value[fieldName]
  }
}

function getFieldError(fieldName: string): string | undefined {
  return validationErrors.value[fieldName]?.[0]
}

defineExpose({
  setValidationErrors,
  validationErrors
})
</script>

<style scoped>
.modal-backdrop {
  position: fixed; inset: 0; background: rgba(0,0,0,0.4);
  display: flex; align-items: center; justify-content: center;
  padding: 1rem; z-index: 1000;
}
.modal {
  background: #fff; border-radius: 0.5rem; padding: 1rem; width: 100%; max-width: 720px;
  border: 1px solid #e5e7eb; max-height: 90vh; overflow-y: auto; position: relative; z-index: 1001;
}
</style>


