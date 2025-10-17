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
            <label>Cultura*</label>
            <Select
              :options="crops"
              optionLabel="name"
              placeholder="Selecione uma cultura"
              optionValue="id"
              v-model="local.crop_name"
              :invalid="!!getFieldError('crop_name')"
              @change="clearFieldError('crop_name')"
            />
            <small v-if="getFieldError('crop_name')" class="text-red-500 text-xs">{{ getFieldError('crop_name') }}</small>
          </div>
          <div class="flex flex-col gap-1">
            <label>Área Total (ha)*</label>
            <InputText
              type="text"
              v-model="local.total_area_ha"
              :invalid="!!getFieldError('total_area_ha')"
              placeholder="Ex.: 10000"
              @input="clearFieldError('total_area_ha')"
            />
            <small v-if="getFieldError('total_area_ha')" class="text-red-500 text-xs">{{ getFieldError('total_area_ha') }}</small>
          </div>
          <div class="flex flex-col gap-1">
            <label>Coordenadas Geográficas*</label>
            <InputText
              type="text"
              v-model="local.geographic_coordinates"
              :invalid="!!getFieldError('geographic_coordinates')"
              placeholder="Ex.: -20.5482404,-42.8711134"
              @input="clearFieldError('geographic_coordinates')"
            />
            <small v-if="getFieldError('geographic_coordinates')" class="text-red-500 text-xs">{{ getFieldError('geographic_coordinates') }}</small>
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
import type { ProductionUnitForm } from '@/types/productionUnit'
import type { Property } from '@/types/property'

interface ValidationErrors {
  [key: string]: string[]
}

const propertyStore = usePropertyStore()

const crops = ref([
  { id: 'Laranja Pera', name: 'Laranja Pera' },
  { id: 'Melancia Crimson Sweet', name: 'Melancia Crimson Sweet' },
  { id: 'Goiaba Paluma', name: 'Goiaba Paluma' },
])

const props = defineProps<{
  modelValue: boolean
  value: ProductionUnitForm
}>()

const validationErrors = ref<ValidationErrors>({})

function close() {
  validationErrors.value = {}
  emit('update:modelValue', false)
}

const emit = defineEmits<{
  (e: 'update:modelValue', v: boolean): void
  (e: 'save', v: ProductionUnitForm): void
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

const local = ref<ProductionUnitForm>(props.value)
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


