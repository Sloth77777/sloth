export interface OrderItem {
    id: number;
    product_id: number | null;
    product_title: string | null;
    product_slug: string | null;
    quantity: number;
    unit_price: number;
    total: number;
    snapshot: any;
}

export interface Order {
    id: number;
    status: string;
    currency: string;
    subtotal: number;
    discount: number;
    total: number;
    customer_name: string | null;
    customer_email: string | null;
    customer_phone: string | null;
    shipping_address: string | null;
    created_at: string;
    items?: OrderItem[];
}
export interface CreateOrderPayload {
    customer_name?: string;
    customer_email?: string;
    customer_phone?: string;
    shipping_address?: string;
    meta?: Record<string, any>;
}
export interface Paginated<T> {
    data: T[];
}
