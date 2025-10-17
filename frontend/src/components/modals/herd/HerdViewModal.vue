<template>
  <div v-if="modelValue" class="modal-backdrop" @click.self="close">
    <div class="modal">
      <h3 class="text-black text-lg font-bold mb-2">Detalhes da Unidade de Produção</h3>
      <div class="details">
        <div class="flex flex-col">
          <strong>Espécie:</strong>
          <span>{{ value.species }}</span>
        </div>
        <div class="flex flex-col">
          <strong>Quantidade:</strong>
          <span>{{ value.quantity }}</span>
        </div>
        <div class="flex flex-col">
          <strong>Finalidade:</strong>
          <span>{{ value.purpose || '-' }}</span>
        </div>
        <div class="flex flex-col">
          <strong>Propriedade:</strong>
          <span>{{ value.property?.name || '-' }}</span>
        </div>
        <div class="flex flex-col"><strong>Criado em:</strong>
          <span v-mask="'datetime'">{{ value.created_at }}</span>
        </div>
        <div class="flex flex-col">
          <strong>Atualizado em:</strong>
          <span v-mask="'datetime'">{{ value.updated_at }}</span>
        </div>
      </div>
      <div class="modal-actions">
        <Button label="Secondary" severity="secondary" @click="close">Fechar</Button>
      </div>
    </div>
  </div>

</template>

<script setup lang="ts">
import Button from 'primevue/button'
import type { Herd } from '@/types/herd';

defineProps<{ modelValue: boolean, value: Herd }>()
const emit = defineEmits<{ (e: 'update:modelValue', v: boolean): void }>()

function close() { emit('update:modelValue', false) }
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
.details { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin: 1rem 0; }
.modal-actions { display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 0.5rem; }
</style>


