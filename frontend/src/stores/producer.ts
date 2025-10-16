import { defineStore } from "pinia";
import { ref } from "vue";
import api from "@/services/api";
import type { Producer } from "@/types/producer";


export const useProducerStore = defineStore('producer', () => {
    const producer = ref<Producer | null>(null);
    const producers = ref<Producer[]>([]);
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
            if(producers.value.length == 0) {
              const response = await api.get('/farmers', { params: { page, per_page: per } });
              const payload = response.data;
              if (payload && Array.isArray(payload.data)) {
                  producers.value = payload.data;
                  currentPage.value = payload.current_page ?? page;
                  lastPage.value = payload.last_page ?? 1;
                  perPage.value = Number(payload.per_page ?? per ?? payload.data.length ?? 0);
                  total.value = Number(payload.total ?? payload.data.length ?? 0);
                  links.value = payload.links ?? [];
              } else {
                  producers.value = payload;
                  currentPage.value = 1;
                  lastPage.value = 1;
                  perPage.value = payload?.length ?? 0;
                  total.value = payload?.length ?? 0;
                  links.value = [];
              }
            }
            return producers.value;
        } catch (err: unknown) {
            error.value = err instanceof Error ? err.message : 'Falha ao carregar produtores';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const getById = async (id: number) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await api.get(`/farmers/${id}`);
            producer.value = response.data.data ?? response.data;
            return producer.value;
        } catch (err: unknown) {
            error.value = err instanceof Error ? err.message : 'Falha ao buscar produtor';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const create = async (payload: Omit<Producer, 'id' | 'created_at' | 'updated_at'> | Record<string, unknown>) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await api.post('/farmers', payload);
            const created: Producer = response.data.data ?? response.data;
            producers.value.unshift(created);
            return created;
        } catch (err: unknown) {
            error.value = err instanceof Error ? err.message : 'Falha ao criar produtor';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const update = async (id: number, payload: Partial<Omit<Producer, 'id' | 'created_at' | 'updated_at'>> | Record<string, unknown>) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await api.put(`/farmers/${id}`, payload);
            const updated: Producer = response.data.data ?? response.data;
            const index = producers.value.findIndex(p => p.id === id);
            if (index !== -1) producers.value[index] = updated;
            if (producer.value?.id === id) producer.value = updated;
            return updated;
        } catch (err: unknown) {
            error.value = err instanceof Error ? err.message : 'Falha ao atualizar produtor';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const remove = async (id: number) => {
        loading.value = true;
        error.value = null;
        try {
            await api.delete(`/farmers/${id}`);
            producers.value = producers.value.filter(p => p.id !== id);
            if (producer.value?.id === id) producer.value = null;
        } catch (err: unknown) {
            error.value = err instanceof Error ? err.message : 'Falha ao excluir produtor';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return {
        // actions
        list,
        getById,
        create,
        update,
        remove,
        // state
        producer,
        producers,
        loading,
        error,
        currentPage,
        lastPage,
        perPage,
        total,
        links,
    };
});
