<script lang="ts">
import {useOrdersStore} from '@/stores/orderStore';
import {useAuthStore} from "@/stores/authStore";
import {useCartStore} from "@/stores/cartStore";
import {isAxiosError} from "axios";

export default {
  data() {
    return {
      orders: useOrdersStore(),
      customer_name: '' as string,
      customer_email: '' as string,
      customer_phone: '' as string,
      shipping_address: '' as string,
      meta: {} as Record<string, any>,
      showGuestForm: false,
      authStore: useAuthStore(),
      cart: useCartStore(),
    }
  },

  computed: {
    isAuthenticated() {
      return this.authStore.isAuthenticated;
    },
    hasItems() {
      return this.cart.displayItems.length > 0;
    },
    error() {
      return this.orders.error
    },
    loading() {
      return this.orders.loading
    }
  },

  async mounted() {
    if (!this.isAuthenticated) {
      this.$router.push({
        name: 'login',
        query: {
          redirect: this.$route.fullPath
        }
      });
      return;
    }
    if (this.cart.displayItems.length === 0) {
      await this.cart.loadCart();
    }
  },

  methods: {
    async submit() {
      this.orders.error = null;
      await this.cart.loadCart();

      if (!this.hasItems) {
        this.orders.error = 'Carts not found. Please add some products to your cart.';
        return;
      }
      if (!this.customer_name || !this.customer_email || !this.customer_phone || !this.shipping_address) {
        this.orders.error = 'Please fill in all required fields.';
        return;
      }
      try {
        const itemsPayload = this.cart.displayItems.map((i: any) => ({
          product_id: i.product_id,
          quantity: i.quantity,
        }));

        const payload: any = {
          customer_name: this.customer_name || undefined,
          customer_email: this.customer_email || undefined,
          customer_phone: this.customer_phone || undefined,
          shipping_address: this.shipping_address || undefined,
          meta: Object.keys(this.meta).length ? this.meta : undefined,
          items: itemsPayload,
        };

        const created = await this.orders.checkout(payload);

        const orderId =
            (typeof created === 'object' && created && (created as any).id) ??
            (created as any)?.order?.id ??
            (created as any)?.data?.id ??
            (created as any)?.data?.order?.id;

        if (!orderId) {
          this.orders.error = 'Order id was not returned by API';
          return;
        }

        void this.$router.push({ name: 'orders.index'});

        this.cart.loadCart().catch(() => {});
      } catch (err: unknown) {
        this.orders.error = isAxiosError(err)
            ? (err.response?.data?.message || err.message)
            : (err instanceof Error ? err.message : 'Error creating order.');
      }
    }
  }
}
</script>

<template>
  <div v-if="isAuthenticated"
       class="max-w-2xl mx-auto p-4 bg-white shadow-md rounded-lg mt-8 mb-16 space-y-6 border border-gray-200">
    <h1 class="text-2xl font-bold mb-4">Placing an order</h1>

    <div v-if="!hasItems" class="p-3 bg-yellow-50 text-yellow-800 rounded">
      There are no products in your cart. Please go back and add products.
    </div>

    <form @submit.prevent="submit" class="space-y-3">
      <div>
        <label class="block text-sm mb-1">Name</label>
        <input v-model="customer_name"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
      </div>
      <label class="block text-sm mb-1">Email</label>
      <input v-model="customer_email" type="email"
             class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
      <div>
        <label class="block text-sm mb-1">Phone number</label>
        <input v-model="customer_phone"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
      </div>
      <div>
        <label class="block text-sm mb-1">Address</label>
        <textarea v-model="shipping_address"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  rows="3"></textarea>
      </div>
      <div v-if="error" class="text-red-600 text-sm">
        {{ error }}
      </div>
      <button
          class="w-full block text-center py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-500 transition-colors duration-200"
          :disabled="loading || !hasItems">
        {{ loading ? 'Мы обрабатываем...' : 'Оформить заказ'}}
      </button>
    </form>
  </div>
</template>