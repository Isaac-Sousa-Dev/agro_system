<template>
  <div v-if="modelValue" class="modal-backdrop" @click.self="close">
    <div class="bg-white rounded-lg p-4">
      <h3 class="text-black text-lg font-bold mb-2">Confirmar Exclusão</h3>
      <p>Tem certeza que deseja excluir o produtor "{{ name }}"?</p>
      <div class="flex justify-end gap-2 mt-4">
        <Button label="Secondary" severity="secondary" @click="close">Cancelar</Button>
        <Button label="Danger" severity="danger" @click="$emit('confirm')">Excluir</Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Button from 'primevue/button'

defineProps<{ modelValue: boolean, name: string }>()
const emit = defineEmits<{ (e: 'update:modelValue', v: boolean): void, (e: 'confirm'): void }>()

function close() { emit('update:modelValue', false) }
</script>

<style scoped>
.modal-backdrop {
  position: fixed; inset: 0; background: rgba(0,0,0,0.4);
  display: flex; align-items: center; justify-content: center;
  padding: 1rem; z-index: 1000;
}
</style>


