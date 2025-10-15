<template>
  <div v-if="modelValue" class="modal-backdrop" @click.self="close">
    <div class="modal">
      <h3 class="text-black text-lg font-bold mb-2">Detalhes do Produtor</h3>
      <div class="details">
        <div class="flex flex-col">
          <strong>Nome:</strong>
          <span>{{ value.name }}</span>
        </div>
        <div class="flex flex-col">
          <strong>CPF/CNPJ:</strong>
          <span v-mask="'cpf_cnpj'">{{ value.cpf_cnpj }}</span>
        </div>
        <div class="flex flex-col">
          <strong>Telefone:</strong>
          <span v-mask="'phone'">{{ value.phone || '-' }}</span>
        </div>
        <div class="flex flex-col">
          <strong>E-mail:</strong>
          <span>{{ value.email || '-' }}</span>
        </div>
        <div class="flex flex-col">
          <strong>Endereço:</strong>
          <span>{{ value.address || '-' }}</span>
        </div>
        <div class="flex flex-col"><strong>Criado em:</strong>
          {{ value.created_at }}
        </div>
        <div class="flex flex-col">
          <strong>Atualizado em:</strong>
          {{ value.updated_at }}
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
import type { Producer } from '@/types/producer'

defineProps<{ modelValue: boolean, value: Producer }>()
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


