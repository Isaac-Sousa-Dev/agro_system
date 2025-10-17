import api from "@/services/api";
import type { Herd } from "@/types/herd";
import { defineStore } from "pinia";
import { ref } from "vue";

export const useHerdStore = defineStore('herd', () => {
    const herd = ref<Herd | null>(null);
    const herds = ref<Herd[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);
    const currentPage = ref<number>(1);
    const lastPage = ref<number>(1);
    const perPage = ref<number>(6);
    const total = ref<number>(0);
    const links = ref<Array<{ url: string | null; label: string; active: boolean }>>([]);

    const list = async (page = 1, per: number = perPage.value) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await api.get('/herds', { params: { page, per_page: per } });
            const payload = response.data;
            if (payload && Array.isArray(payload.data)) {
                herds.value = payload.data;
                currentPage.value = payload.meta.current_page ?? page;
                lastPage.value = payload.meta.last_page ?? 1;
                perPage.value = Number(payload.meta.per_page ?? per ?? payload.data.length ?? 0);
                total.value = Number(payload.meta.total ?? payload.data.length ?? 0);
                links.value = payload.links ?? [];
            } else {
                herds.value = payload;
                currentPage.value = 1;
                lastPage.value = 1;
                perPage.value = payload?.length ?? 0;
                total.value = payload?.length ?? 0;
                links.value = [];
            }
            return herds.value;
        } catch (err: unknown) {
            error.value = err instanceof Error ? err.message : 'Falha ao carregar rebanhos';
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
        } catch (err: unknown) {
            error.value = err instanceof Error ? err.message : 'Falha ao buscar rebanho';
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
        } catch (err: unknown) {
            error.value = err instanceof Error ? err.message : 'Falha ao criar rebanho';
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
        } catch (err: unknown) {
            error.value = err instanceof Error ? err.message : 'Falha ao atualizar rebanho';
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
        } catch (err: unknown) {
            error.value = err instanceof Error ? err.message : 'Falha ao excluir rebanho';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const exportPdf = async (farmerId: number | null = null) => {
      const response = await api.get('/herds/export/pdf', {
        params: farmerId ? { farmer_id: farmerId } : {},
        responseType: 'blob',
        headers: { Accept: 'application/pdf' }
      });

      const url = window.URL.createObjectURL(response.data);
      const link = document.createElement('a');
      link.href = url;
      link.download = 'rebanhos.pdf';
      link.click();
    };

    return {
        list,
        getById,
        create,
        update,
        remove,
        exportPdf,
        herd,
        herds,
        loading,
        error,
        currentPage,
        lastPage,
        perPage,
        total,
        links,
    };
});



