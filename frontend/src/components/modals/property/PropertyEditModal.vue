<template>
  <div
    v-if="modelValue"
    class="modal-backdrop"
    @click.self="close"
  >
    <div class="modal">
      <h3 class="text-black text-lg font-bold mb-2">Editar Propriedade</h3>
      <form @submit.prevent="submit">
        <!-- <div class="card">
            <Toast />
            <FileUpload name="demo[]" url="/api/upload" :multiple="true" accept="image/*" :maxFileSize="1000000">
                <template #empty>
                    <span>Drag and drop files to here to upload.</span>
                </template>
            </FileUpload>
        </div> -->
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-1">
            <label>Nome</label>
            <InputText type="text" v-model="local.name" placeholder="Ex.: Fazenda São João"  />
          </div>
          <div class="flex flex-col gap-1">
            <label>CEP</label>
            <InputText
              type="text"
              v-model="cep"
              v-mask="'cep'"
              :invalid="!!getFieldError('cep')"
              placeholder="Ex.: 61600-000"
              @blur="handleCepSearch"
            />
            <small v-if="getFieldError('cep')" class="text-red-500 text-xs">{{ getFieldError('cep') }}</small>
          </div>
          <div class="flex flex-col gap-1">
            <label>Município</label>
            <InputText
              disabled
              type="text"
              v-model="local.municipality"
              :invalid="!!getFieldError('municipality')"
              placeholder="Ex.: Viçosa do Ceará"
              @input="clearFieldError('municipality')"
            />
            <small v-if="getFieldError('municipality')" class="text-red-500 text-xs">{{ getFieldError('municipality') }}</small>
          </div>
          <div class="flex flex-col gap-1">
            <label>UF</label>
            <InputText
              disabled
              v-model="local.state"
              v-mask="'state'"
              :invalid="!!getFieldError('state')"
              placeholder="Ex.: CE"
              @input="clearFieldError('state')"
            />
            <small v-if="getFieldError('state')" class="text-red-500 text-xs">{{ getFieldError('state') }}</small>
          </div>
          <div class="flex flex-col gap-1">
            <label>Área Total (ha)</label>
            <InputText type="text" v-model="local.total_area" placeholder="00000" />
          </div>
          <div class="flex flex-col gap-1">
            <label>Inscrição Estadual</label>
            <InputText type="text" v-model="local.state_registration" placeholder="Opcional" />
          </div>
          <div class="flex flex-col gap-1">
            <label>Produtor</label>
            <Select
              :options="producers"
              optionLabel="name"
              placeholder="Selecione um produtor"
              optionValue="id"
              v-model="local.farmer_id" />
          </div>
        </div>

        <div class="mt-4">
          <Tabs value="0">
            <TabList>
              <Tab value="0">Unidades de Produção</Tab>
              <Tab value="1">Rebanhos</Tab>
            </TabList>
            <TabPanels>
              <TabPanel value="0">
                <div class="flex justify-end mb-2">
                  <Button type="button" class="" @click="addProductionUnit">
                    <span class="btn-icon"><i class="pi pi-plus"></i></span>
                    Adicionar Unidade de Produção
                  </Button>
                </div>

                <div class="space-y-3">
                  <div v-for="(prop, idx) in local.productionUnits" :key="idx" class="accordion">
                    <div class="bg-gray-100 flex justify-between items-center p-2 rounded-md" @click="toggleProductionUnit(idx)">
                      <div class="title">
                        <i class="pi" :class="prop.open ? 'pi-angle-down' : 'pi-angle-right'"></i>
                        <span class="ml-4">Unidade de Produção {{ idx + 1 }} — {{ getCropName(prop.crop_name) || 'Sem cultura' }}</span>
                      </div>
                      <div class="actions">
                        <Button label="Danger" severity="danger" @click.stop="removeProductionUnit(idx)">
                          <i class="pi pi-trash"></i>
                        </Button>
                      </div>
                    </div>
                    <div v-show="prop.open" class="mt-3 p-2">
                      <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                          <label>Cultura*</label>
                          <Select
                            :options="CROPS"
                            optionLabel="name"
                            optionValue="id"
                            v-model="prop.crop_name"
                            placeholder="Selecione uma cultura"
                            @change="onCropChange(idx, $event)"
                          />
                        </div>
                        <div class="flex flex-col gap-1">
                          <label>Área Total (ha)*</label>
                          <InputText type="text" v-model="prop.total_area_ha" placeholder="00000" />
                        </div>
                        <div class="flex flex-col gap-1">
                          <label>Coordenadas Geográficas</label>
                          <InputText type="text" v-model="prop.geographic_coordinates" placeholder="Ex.: -20.5482404,-42.8711134" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </TabPanel>

              <TabPanel value="1">
                <div class="flex justify-end mb-2">
                  <Button type="button" class="" @click="addHerd">
                    <span class="btn-icon"><i class="pi pi-plus"></i></span>
                    Adicionar rebanho
                  </Button>
                </div>

                <div class="space-y-3">
                  <div v-for="(herd, idx) in local.herds" :key="idx" class="accordion">
                    <div class="bg-gray-100 flex justify-between items-center p-2 rounded-md" @click="toggleHerd(idx)">
                      <div class="title">
                        <i class="pi" :class="herd.open ? 'pi-angle-down' : 'pi-angle-right'"></i>
                        <span class="ml-4">Rebanho {{ idx + 1 }} — {{ getSpeciesName(herd.species) || 'Sem espécie' }}</span>
                      </div>
                      <div class="actions">
                        <Button label="Danger" severity="danger" @click.stop="removeHerd(idx)">
                          <i class="pi pi-trash"></i>
                        </Button>
                      </div>
                    </div>
                    <div v-show="herd.open" class="mt-3 p-2">
                      <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                          <label>Espécie*</label>
                          <Select
                            :options="SPECIES"
                            optionLabel="name"
                            optionValue="id"
                            v-model="herd.species"
                            placeholder="Selecione uma espécie"
                            @change="onSpeciesChange(idx, $event)"
                          />
                        </div>
                        <div class="flex flex-col gap-1">
                          <label>Quantidade*</label>
                          <InputText type="text" v-model="herd.quantity" placeholder="Ex.: 100" />
                        </div>
                        <div class="flex flex-col gap-1">
                          <label>Finalidade</label>
                          <InputText type="text" v-model="herd.purpose" placeholder="Ex.: Leite, Carne, Lã" />
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
          <Button class="px-10" type="submit">Salvar Alterações</Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import InputText from 'primevue/inputtext'
import Tabs from 'primevue/tabs'
import TabList from 'primevue/tablist'
import Tab from 'primevue/tab'
import TabPanels from 'primevue/tabpanels'
import TabPanel from 'primevue/tabpanel'
import Button from 'primevue/button'
import Select from 'primevue/select'
import type { PropertyForm } from '@/types/property'
import type { Producer } from '@/types/producer'
import { useProducerStore } from '@/stores/producer'
import { CROPS, SPECIES } from '@/data/cropsAndSpecies'
// import FileUpload from 'primevue/fileupload'
import { getAddressByCep } from '@/utils/cep'

interface ValidationErrors {
  [key: string]: string[]
}

const producerStore = useProducerStore()

const props = defineProps<{
  modelValue: boolean
  value: PropertyForm
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', v: boolean): void
  (e: 'save', v: PropertyForm): void
  (e: 'validation-error', v: ValidationErrors): void
}>()

const cep = ref('')
const validationErrors = ref<ValidationErrors>({})


const handleCepSearch = async () => {
  const result = await getAddressByCep(cep.value)

  if (!result.success) {
    setValidationErrors({
      cep: [result.error?.message || 'CEP inválido']
    })
    local.value.municipality = ''
    local.value.state = ''
    return
  }

  clearFieldError('cep')
  local.value.municipality = result.data?.localidade || ''
  local.value.state = result.data?.uf || ''
}

function close() {
  validationErrors.value = {}
  emit('update:modelValue', false)
}

function submit() {
  emit('save', local.value)
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


function addProductionUnit() {
  local.value.productionUnits?.push(
    { crop_id: null, total_area_ha: '', geographic_coordinates: '', open: true }
  )
}
function removeProductionUnit(index: number) {
  local.value.productionUnits?.splice(index, 1)
}
function toggleProductionUnit(index: number) {
  const prodUnit = local.value.productionUnits?.[index]
  if (!prodUnit) return
  prodUnit.open = !prodUnit.open
}


const addHerd = () => {
  local.value.herds?.push(
    { species_id: null, quantity: '', purpose: '', update_date: '', property_id: null }
  )
}
const removeHerd = (index: number) => {
  local.value.herds?.splice(index, 1)
}
const toggleHerd = (index: number) => {
  const herd = local.value.herds?.[index]
  if (!herd) return
  herd.open = !herd.open
}

const local = ref<PropertyForm>(props.value)
watch(() => props.value, (nv) => {
  console.log(nv, 'nv')
  Object.assign(local.value, JSON.parse(JSON.stringify(nv)))
  validationErrors.value = {}
})

const producers = ref<Producer[]>([])
onMounted(async () => {
  await producerStore.list()
  producers.value = producerStore.producers
})

// Funções para lidar com mudanças nos selects
function onCropChange(index: number, event: { value: string }) {
  const cropId = event.value
  const prodUnit = local.value.productionUnits?.[index]
  if (prodUnit && cropId) {
    const selectedCrop = CROPS.find(crop => crop.id === cropId)
    if (selectedCrop) {
      prodUnit.crop_name = selectedCrop.name
    }
  }
}

function onSpeciesChange(index: number, event: { value: string }) {
  const speciesId = event.value
  const herd = local.value.herds?.[index]
  if (herd && speciesId) {
    const selectedSpecies = SPECIES.find(species => species.id === speciesId)
    if (selectedSpecies) {
      herd.species = selectedSpecies.name
    }
  }
}

// Funções auxiliares para obter nomes
function getCropName(cropName: string | null | undefined): string | undefined {
  if (!cropName) return undefined
  return CROPS.find(crop => crop.name === cropName)?.name
}

function getSpeciesName(speciesId: string | null | undefined): string | undefined {
  if (!speciesId) return undefined
  return SPECIES.find(species => species.id === speciesId)?.name
}
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


