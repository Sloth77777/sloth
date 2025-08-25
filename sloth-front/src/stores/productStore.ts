import { defineStore } from "pinia";
import { getProducts, getProduct, createProduct, updateProduct, deleteProduct } from "@/api/product";
import router from "@/router/router";
import type {Product} from "@/types/product";

export const useProductStore = defineStore("productStore", {
    state: () => ({
        products: [] as Product[],
        pagination: {} as any,
    }),

    actions: {
        async fetchProducts(page = 1, filters: any = {}) {
            const res = await getProducts(page, filters);
            this.products = res.data.data;
            this.pagination = res.data.meta;
        },

        async fetchProductById(id: number): Promise<Product> {
            const res = await getProduct(id);
            return res.data;
        },

        async createProduct(product: Partial<Product>) {
            await createProduct(product);
            await this.fetchProducts();
            await router.push({name: 'products'});
        },

        async updateProductById(id: number, product: Partial<Product>) {
            await updateProduct(id, product);
            await this.fetchProducts();
        },

        async deleteProductById(id: number) {
            await deleteProduct(id);
            this.products = this.products.filter((p) => p.id !== id);
        },
    },
});
