import api from "@/services/api";
import { defineStore } from "pinia";
import { ref } from "vue";
import type { Property } from "@/types/property";

export const usePropertyStore = defineStore('property', () => {
  const quantityProperties = ref<number>(0);
  const property = ref<Property | null>(null);
  const properties = ref<Property[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);
  const currentPage = ref<number>(1);
  const lastPage = ref<number>(1);
  const perPage = ref<number>(6);
  const total = ref<number>(0);
  const links = ref<Array<{ url: string, label: string, active: boolean}>>([])

  const list = async (page = 1, per: number = perPage.value) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await api.get('/properties');
      const payload = response.data;
      if(payload && Array.isArray(payload.data)) {
        properties.value = payload.data;
        currentPage.value = payload.meta.current_page ?? page;
        lastPage.value = payload.meta.last_page ?? 1;
        perPage.value = Number(payload.meta.per_page ?? per ?? payload.data.length ?? 0);
        total.value = Number(payload.meta.total ?? payload.data.length ?? 0);
        links.value = payload.links ?? [];
      } else {
        properties.value = payload;
        currentPage.value = 1;
        lastPage.value = 1;
        perPage.value = payload?.length ?? 0;
        total.value = payload?.length ?? 0;
        links.value = [];
      }

      quantityProperties.value = properties.value.length;
      return properties.value;
    } catch (err: unknown) {
      error.value = err instanceof Error ? err.message : 'Falha ao carregar propriedades';
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
    } catch (err: unknown) {
      error.value = err instanceof Error ? err.message : 'Falha ao buscar propriedade';
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
    } catch (err: unknown) {
      error.value = err instanceof Error ? err.message : 'Falha ao criar propriedade';
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
    } catch (err: unknown) {
      error.value = err instanceof Error ? err.message : 'Falha ao atualizar propriedade';
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
    } catch (err: unknown) {
      error.value = err instanceof Error ? err.message : 'Falha ao excluir propriedade';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const exportExcel = async () => {
    loading.value = true;
    error.value = null;

    try {
      const response = await api.get('/properties/export/excel', {
        responseType: 'blob',
        headers: {
          Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        },
      });

      // tenta obter o filename do header
      const disposition = response.headers['content-disposition'];
      const fileNameMatch = disposition?.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/i);
      const fileName = fileNameMatch ? fileNameMatch[1].replace(/['"]/g, '') : 'properties.xlsx';

      const url = window.URL.createObjectURL(response.data);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', fileName);
      document.body.appendChild(link);
      link.click();
      link.remove();
      window.URL.revokeObjectURL(url);
      return true;
    } catch (err: unknown) {
      // se o backend retornar erro em JSON, converter o blob para texto/JSON para exibir
      error.value = err instanceof Error ? err.message : 'Falha ao exportar Excel';
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
    exportExcel,
    property,
    properties,
    quantityProperties,
    loading,
    error,
    currentPage,
    lastPage,
    perPage,
    total,
    links,
  };
})
