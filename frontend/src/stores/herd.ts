import api from "@/services/api";
import type { Herd } from "@/types/herd";
import { defineStore } from "pinia";
import { ref } from "vue";

export const useHerdStore = defineStore('herd', () => {
  const herd = ref<Herd | null>(null);
  const herds = ref<Herd[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const list = async (params?: { propertyId?: number }) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.get('/herds', { params: params ?? {} });
      herds.value = response.data.data ?? response.data;
      return herds.value;
    } catch (err: any) {
      error.value = err?.message ?? 'Falha ao carregar rebanhos';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const getById = async (id: number) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.get(`/herds/${id}`);
      herd.value = response.data.data ?? response.data;
      return herd.value;
    } catch (err: any) {
      error.value = err?.message ?? 'Falha ao buscar rebanho';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const create = async (payload: Omit<Herd, 'id' | 'created_at' | 'updated_at'>) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.post('/herds', payload);
      const created: Herd = response.data.data ?? response.data;
      herds.value.unshift(created);
      return created;
    } catch (err: any) {
      error.value = err?.message ?? 'Falha ao criar rebanho';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const update = async (id: number, payload: Partial<Omit<Herd, 'id' | 'created_at' | 'updated_at'>>) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.put(`/herds/${id}`, payload);
      const updated: Herd = response.data.data ?? response.data;
      const index = herds.value.findIndex(p => p.id === id);
      if (index !== -1) herds.value[index] = updated;
      if (herd.value?.id === id) herd.value = updated;
      return updated;
    } catch (err: any) {
      error.value = err?.message ?? 'Falha ao atualizar rebanho';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const remove = async (id: number) => {
    loading.value = true;
    error.value = null;
    try {
      await api.delete(`/herds/${id}`);
      herds.value = herds.value.filter(p => p.id !== id);
      if (herd.value?.id === id) herd.value = null;
    } catch (err: any) {
      error.value = err?.message ?? 'Falha ao excluir rebanho';
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
    herd,
    herds,
    loading,
    error,
  };
});



