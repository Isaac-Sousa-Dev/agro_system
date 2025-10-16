import api from "@/services/api";
import type { ProductionUnit } from "@/types/productionUnit";
import { defineStore } from "pinia";
import { ref } from "vue";

export const useProductionUnitStore = defineStore('productionUnit', () => {
    const unit = ref<ProductionUnit | null>(null);
    const units = ref<ProductionUnit[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);
    const currentPage = ref<number>(1);
    const lastPage = ref<number>(1);
    const perPage = ref<number>(6);
    const total = ref<number>(0);
    const links = ref<Array<{ url: string, label: string, active: boolean }>>([])


    const list = async (page = 1, per: number = perPage.value, params?: { propertyId?: number }) => {
        loading.value = true;
        error.value = null;
        try {

            if (units.value.length == 0) {
                const response = await api.get('/production-units', { params: params ?? {} });

                const payload = response.data;
                if (payload && Array.isArray(payload.data)) {
                    units.value = payload.data;
                    currentPage.value = payload.current_page ?? page;
                    lastPage.value = payload.last_page ?? 1;
                    perPage.value = Number(payload.per_page ?? per ?? payload.data.length ?? 0);
                    total.value = Number(payload.total ?? payload.data.length ?? 0);
                    links.value = payload.links ?? [];
                } else {
                    units.value = payload;
                    currentPage.value = 1;
                    lastPage.value = 1;
                    perPage.value = payload?.length ?? 0;
                    total.value = payload?.length ?? 0;
                    links.value = [];
                }
            }
            return units.value;
        } catch (err: unknown) {
            error.value = err instanceof Error ? err.message : 'Falha ao carregar unidades de produção';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const getById = async (id: number) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await api.get(`/production-units/${id}`);
            unit.value = response.data.data ?? response.data;
            return unit.value;
        } catch (err: unknown) {
            error.value = (err as { message?: string })?.message ?? 'Falha ao buscar unidade';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const create = async (payload: Omit<ProductionUnit, 'id' | 'created_at' | 'updated_at'>) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await api.post('/production-units', payload);
            const created: ProductionUnit = response.data.data ?? response.data;
            units.value.unshift(created);
            return created;
        } catch (err: unknown) {
            error.value = err instanceof Error ? err.message : 'Falha ao criar unidade de produção';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const update = async (id: number, payload: Partial<Omit<ProductionUnit, 'id' | 'created_at' | 'updated_at'>>) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await api.put(`/production-units/${id}`, payload);
            const updated: ProductionUnit = response.data.data ?? response.data;
            const index = units.value.findIndex(p => p.id === id);
            if (index !== -1) units.value[index] = updated;
            if (unit.value?.id === id) unit.value = updated;
            return updated;
        } catch (err: unknown) {
            error.value = (err as { message?: string })?.message ?? 'Falha ao atualizar unidade';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const remove = async (id: number) => {
        loading.value = true;
        error.value = null;
        try {
            await api.delete(`/production-units/${id}`);
            units.value = units.value.filter(p => p.id !== id);
            if (unit.value?.id === id) unit.value = null;
        } catch (err: unknown) {
            error.value = (err as { message?: string })?.message ?? 'Falha ao excluir unidade';
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
        unit,
        units,
        loading,
        error,
        currentPage,
        lastPage,
        perPage,
        total,
        links,
    };
});



