export type Category = {
    id: number;
    title: string;
};

export interface CategoriesState {
    items: Category[];
    selectedId: number | null;
}