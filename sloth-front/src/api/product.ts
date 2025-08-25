import axios from 'axios';

export const getProducts = (page: number, filters: Record<string, any> = {}, search?: string) => {
    const params: Record<string, any> = { page, ...filters };
    if (search) {
        params.search = search;
    }
    return axios.get(`/api/products`, { params });
};

export const getProduct = (id: number) => {
    return axios.get(`/api/products/${id}`);
};

export const createProduct = (data: any) => {
    return axios.post('/api/products', data);
};

export const updateProduct = (id: number, data: any) => {
    return axios.put(`/api/products/${id}`, data);
};

export const deleteProduct = (id: number) => {
    return axios.delete(`/api/products/${id}`);
};
