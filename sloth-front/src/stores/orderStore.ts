import {defineStore} from 'pinia';
import {createOrder, getOrder, getOrders} from "@/api/order";
import {CreateOrderPayload, Order} from "@/types/order";

export const useOrdersStore = defineStore('orders', {
    state: () => ({
        items: [] as Order[],
        current: null as Order | null,
        loading: false,
        error: null as string | null,
    }),
    actions: {
        async checkout(payload: CreateOrderPayload) {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await createOrder(payload);
                this.current = data as Order;
                return this.current;
            } catch (error: any) {
                this.error = error?.response?.data?.message || 'Error creating order.';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async show(id: number | string) {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await getOrder(id);
                const payload = (data && typeof data === 'object' && 'data' in data) ? (data as any).data : data;
                this.current = payload as Order;
                return this.current;
            } catch (error: any) {
                this.error = error?.response?.data?.message || 'Error loading order.';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async index(params?: Record<string, any>) {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await getOrders(params);
                this.items = Array.isArray(data) ? (data as Order[]) : (data?.data ?? []);
                return this.items;
            } catch (error: any) {
                this.error = error?.response?.data?.message || 'Error loading orders.';
                throw error;
            } finally {
                this.loading = false;
            }
        },

    },
});
