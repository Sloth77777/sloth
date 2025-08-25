import {addToCart, clearCartItems, fetchCart} from '@/api/cart';
import {defineStore} from 'pinia';
import type {CartItem} from '@/types/cart';
import {updateCartItem, removeCartItem} from '@/api/cart';

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: [] as CartItem[],
        loading: false as boolean,
        error: null as string | null,
        lastSyncAt: null as number | null,
        _requestToken: 0 as number,
    }),

    getters: {
        totalPrice(state): number {
            return state.items.reduce((sum, item) => sum + (item.price ?? 0) * item.quantity, 0);
        },
        displayItems(state) {
            return [...state.items];
        },
        totalQuantity(state): number {
            return state.items.reduce((sum, item) => sum + (item.quantity ?? 0), 0);
        },
    },

    actions: {
        async loadCart() {
            this.loading = true;
            this.error = null;
            const token = ++this._requestToken;
            try {
                const response = await fetchCart();
                if (token !== this._requestToken) return;

                this.items = response.items;
                this.lastSyncAt = Date.now();
            } catch (error: any) {
                this.error = error?.message ?? 'Invalid cart data';
            } finally {
                this.loading = false;
            }
        },

        async addProduct(productId: number, quantity: number = 1) {
            this.error = null;
            this.loading = true;
            try {
                const qty = Math.max(1, Math.floor(Number(quantity) || 1));
                const existing = this.items.find(i => i.product_id === productId || i.id === productId);

                if (existing) {
                    const desiredQty = (existing.quantity ?? 0) + qty;
                    await updateCartItem(existing.id, desiredQty);
                    await this.loadCart();
                } else {
              await addToCart(productId, 1);
                    await this.loadCart();

                    if (qty > 1) {
                        const created = this.items.find(i => i.product_id === productId || i.id === productId);
                        if (created) {
                            await updateCartItem(created.id, qty);
                            await this.loadCart();
                        }
                    }
                }
            } catch (error: any) {
                this.error = error?.message ?? 'Invalid cart data';
            } finally {
                this.loading = false;
            }
        },

        async updateProduct(productId: number, quantity = 1) {
            const qty = Math.max(1, Math.floor(quantity || 1));
            this.error = null;
            this.loading = true;
            try {
                const existing = this.items.find(i => i.product_id === productId || i.id === productId);
                if (!existing) {
                    await addToCart(productId, 1);
                    await this.loadCart();
                    const created = this.items.find(i => i.product_id === productId || i.id === productId);
                    if (created) {
                        await updateCartItem(created.id, qty);
                        await this.loadCart();
                    }
                } else {
                    await updateCartItem(existing.id, qty);
                    await this.loadCart();
                }
            } catch (e: any) {
                this.error = e?.message ?? 'Invalid cart data';
            } finally {
                this.loading = false;
            }
        },

        async removeProduct(productId: number) {
            this.error = null;
            this.loading = true;
            try {
                await removeCartItem(productId);
                await this.loadCart();
            } catch (e: any) {
                this.error = e?.message ?? 'Invalid cart data';
            } finally {
                this.loading = false;
            }
        },

        async clearAll() {
            this.error = null;
            this.loading = true;
            try {
                await clearCartItems();
                this.items = [];
                this.lastSyncAt = Date.now();
            } catch (e: any) {
                this.error = e?.message ?? 'Invalid cart data';
            } finally {
                this.loading = false;
            }
        },
    },
});
