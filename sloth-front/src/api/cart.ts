import axios from 'axios';
import {CartItem} from "@/types/cart";

export async function fetchCart(): Promise<{ items: CartItem[] }> {
    const res = await axios.get("/api/cart");
    return res.data;
}
export const addToCart = (productId: number, qty:number) =>
    axios.post<{ success: boolean; items: CartItem[] }>('/api/cart/items', {
        product_id: productId,
        quantity: qty,
    });

export const updateCartItem = (itemId: number, qty: number) =>
    axios.patch(`/api/cart/items/${itemId}`, { quantity: qty });
export const removeCartItem = (itemId: number) =>
    axios.delete(`/api/cart/items/${itemId}`);
export const clearCartItems = () => axios.delete('/api/cart');
