import axios from 'axios';
import {CreateOrderPayload} from "@/types/order";
export function createOrder(payload: CreateOrderPayload) {
    return axios.post('/api/orders', payload);
}
export function getOrder(id: number | string) {
    return axios.get(`/api/orders/${id}`);
}

export function getOrders(params?: Record<string, any>) {
    return axios.get('/api/orders', { params });
}
