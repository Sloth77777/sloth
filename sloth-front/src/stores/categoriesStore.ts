import {defineStore} from "pinia";
import {getProducts} from "@/api/product";
import {Category, CategoriesState} from "@/types/category";

export const useCategoriesStore = defineStore("categoriesStore", {
    state: (): CategoriesState => ({
        items: [],
        selectedId: null
    }),

    getters: {
        list(state): Category[] {
            return state.items;
        },
    },

    actions: {
        async fetchCategories() {

            const response = await getProducts(1);
            const productList = (response?.data?.data ?? []) as Array<{
                category?: | {
                    id: number | string;
                    title?: string;
                }
            }>;

            const rawCategoryItems = productList.map((product) => {
                const category = product?.category;
                if (category && typeof category === "object") {
                    const id = Number((category as any).id);
                    if (!Number.isFinite(id)) return null;
                    const title = String(
                        (category as any).title ??
                        (category as any).name ??
                        (category as any).slug ??
                        id
                    );
                    return {id, title} as Category;
                }
                const id = Number(category as number | string | undefined);
                if (!Number.isFinite(id)) return null;
                return {id, title: String(id)} as Category;
            }) as Array<Category | null>;

            const categoryCandidates: Category[] = rawCategoryItems.filter(
                (item: Category | null): item is Category => Boolean(item)
            );

            this.items = Array.from(
                new Map<number, Category>(
                    categoryCandidates.map(
                        (c: Category): [number, Category] => [c.id, c]
                    )
                ).values()
            );
        },

        selectCategory(id: number | null) {
            this.selectedId = id;
        },
    },
});