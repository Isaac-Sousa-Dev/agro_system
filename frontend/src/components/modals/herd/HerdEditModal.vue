<template>
  <div
    v-if="modelValue"
    class="modal-backdrop"
    @click.self="close"
  >
    <div class="modal">
      <h3 class="text-black text-lg font-bold mb-2">Editar Unidade de Produção</h3>
      <form @submit.prevent="submit">
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-1">
            <label>Espécie*</label>
            <Select
              :options="species"
              optionLabel="name"
              placeholder="Selecione uma cultura"
              optionValue="id"
              v-model="local.species"
              :invalid="!!getFieldError('species')"
              @change="clearFieldError('species')"
            />
            <small v-if="getFieldError('species')" class="text-red-500 text-xs">{{ getFieldError('species') }}</small>
          </div>
          <div class="flex flex-col gap-1">
            <label>Quantidade*</label>
            <InputText
              type="text"
              v-model="local.quantity"
              :invalid="!!getFieldError('quantity')"
              placeholder="Ex.: 10000"
              @input="clearFieldError('quantity')"
            />
            <small v-if="getFieldError('quantity')" class="text-red-500 text-xs">{{ getFieldError('quantity') }}</small>
          </div>
          <div class="flex flex-col gap-1">
            <label>Finalidade*</label>
            <InputText
              type="text"
              v-model="local.purpose"
              :invalid="!!getFieldError('purpose')"
              placeholder="Ex.: Leite, Carne, Lã, etc."
              @input="clearFieldError('purpose')"
            />
            <small v-if="getFieldError('purpose')" class="text-red-500 text-xs">{{ getFieldError('purpose') }}</small>
          </div>
          <div class="flex flex-col gap-1">
            <label>Propriedade*</label>
            <Select
              :options="properties"
              optionLabel="name"
              placeholder="Selecione uma propriedade"
              optionValue="id"
              v-model="local.property_id"
              :invalid="!!getFieldError('property_id')"
              @change="clearFieldError('property_id')"
            />
            <small v-if="getFieldError('property_id')" class="text-red-500 text-xs">{{ getFieldError('property_id') }}</small>
          </div>
        </div>
        <div class="mt-4 flex justify-end gap-2">
          <Button label="Secondary" severity="secondary" class="bg-gray-200" @click="close">Cancelar</Button>
          <Button class="px-10" type="submit">Salvar Alterações</Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Select from 'primevue/select'
import { usePropertyStore } from '@/stores/property'
import type { Property } from '@/types/property'
import type { HerdForm } from '@/types/herd'

interface ValidationErrors {
  [key: string]: string[]
}

const propertyStore = usePropertyStore()

const species = ref([
  { id: 'Suíno', name: 'Suíno' },
  { id: 'Caprino', name: 'Caprino' },
  { id: 'Bovino', name: 'Bovino' },
])

const props = defineProps<{
  modelValue: boolean
  value: HerdForm
}>()

const validationErrors = ref<ValidationErrors>({})

function close() {
  validationErrors.value = {}
  emit('update:modelValue', false)
}

const emit = defineEmits<{
  (e: 'update:modelValue', v: boolean): void
  (e: 'save', v: HerdForm): void
  (e: 'validation-error', v: ValidationErrors): void
}>()


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

function clearAllErrors() {
  validationErrors.value = {}
}

function submit() {
  emit('save', local.value)
}

defineExpose({
  setValidationErrors,
  clearAllErrors,
  validationErrors
})

const local = ref<HerdForm>(props.value)
watch(() => props.value, (nv) => {
  console.log(nv, 'nv')
  Object.assign(local.value, JSON.parse(JSON.stringify(nv)))
})

const properties = ref<Property[]>([])
onMounted(async () => {
  await propertyStore.list()
  properties.value = propertyStore.properties
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


