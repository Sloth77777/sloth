export interface Product {
    id: number;
    title: string;
    description?: string;
    preview_image?: string;
    price?: number;
    discount?: number;
    category?: { id: number; title: string };
    images?: string[];
}
