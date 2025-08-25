import { defineStore } from "pinia";
import { getProducts } from "@/api/product";
import {State} from "@/types/filter"

export const useFilterStore = defineStore("filterStore", {
    state: (): State => ({
        filtersList: {price: { min: 0, max: 0 },},
        loaded: false,
        error: null,
    }),

    actions: {
        async fetchFilterList() {
            try {
                const response = await getProducts(1,this.filtersList);
                const productList = (response?.data?.data ?? []) as Array<{
                    price?: number;
                }>;

                const priceList = productList.map((product) => product.price ?? 0);
                const minPrice = priceList.length ? Math.min(...priceList) : 0;
                const maxPrice = priceList.length ? Math.max(...priceList) : 0;

                this.filtersList = {
                    price: { min: minPrice, max: maxPrice },
                };
                this.loaded = true;
                this.error = null;
            } catch (e: any) {
                this.error = e?.response?.data?.message || e?.message || "Error load filters";
                this.loaded = false;
            }
        },
    },
});