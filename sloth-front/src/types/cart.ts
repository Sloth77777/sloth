export interface CartItem {
    id: number;
    product_id?: number;
    title: string;
    preview_image: string;
    price: number;
    quantity: number;
    [key: string]: any;
}
