import api from "@/services/api";
import type { Property } from "@/types/property";
import { defineStore } from "pinia";
import { ref } from "vue";

export const usePropertyStore = defineStore('property', () => {
  const quantityProperties = ref<number>(0);
  const property = ref<Property | null>(null);
  const properties = ref<Property[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const list = async () => {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.get('/properties');
      properties.value = response.data.data ?? response.data;
      quantityProperties.value = properties.value.length;
      return properties.value;
    } catch (err: any) {
      error.value = err?.message ?? 'Falha ao carregar propriedades';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const getById = async (id: number) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.get(`/properties/${id}`);
      property.value = response.data.data ?? response.data;
      return property.value;
    } catch (err: any) {
      error.value = err?.message ?? 'Falha ao buscar propriedade';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const create = async (payload: Omit<Property, 'id' | 'created_at' | 'updated_at'>) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.post('/properties', payload);
      const created: Property = response.data.data ?? response.data;
      properties.value.unshift(created);
      quantityProperties.value = properties.value.length;
      return created;
    } catch (err: any) {
      error.value = err?.message ?? 'Falha ao criar propriedade';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const update = async (id: number, payload: Partial<Omit<Property, 'id' | 'created_at' | 'updated_at'>>) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.put(`/properties/${id}`, payload);
      const updated: Property = response.data.data ?? response.data;
      const index = properties.value.findIndex(p => p.id === id);
      if (index !== -1) properties.value[index] = updated;
      if (property.value?.id === id) property.value = updated;
      return updated;
    } catch (err: any) {
      error.value = err?.message ?? 'Falha ao atualizar propriedade';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const remove = async (id: number) => {
    loading.value = true;
    error.value = null;
    try {
      await api.delete(`/properties/${id}`);
      properties.value = properties.value.filter(p => p.id !== id);
      quantityProperties.value = properties.value.length;
      if (property.value?.id === id) property.value = null;
    } catch (err: any) {
      error.value = err?.message ?? 'Falha ao excluir propriedade';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    list,
    getById,
    create,
    update,
    remove,
    property,
    properties,
    quantityProperties,
    loading,
    error,
  };
})
