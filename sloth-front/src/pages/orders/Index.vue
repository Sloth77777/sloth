<script lang="ts">
import { useOrdersStore } from '@/stores/orderStore';
import { useAuthStore } from '@/stores/authStore';
import { isAxiosError } from 'axios';

export default {
  name: 'OrdersIndex',
  data() {
    return {
      orders: useOrdersStore(),
      auth: useAuthStore(),
    };
  },
  computed: {
    isAuthenticated() {
      return this.auth.isAuthenticated;
    },
    loading() {
      return this.orders.loading;
    },
    error() {
      return this.orders.error;
    },
    items() {
      return this.orders.items;
    }
  },
  async mounted() {
    if (!this.isAuthenticated) {
      this.$router.push({
        name: 'login',
        query: { redirect: this.$route.fullPath }
      });
      return;
    }
    try {
      await this.orders.index();
    } catch (e: unknown) {
      this.orders.error = isAxiosError(e)
          ? (e.response?.data?.message || e.message)
          : (e instanceof Error ? e.message : 'Error occurred');
    }
  },
  methods: {
    goto(orderId: number | string) {
      this.$router.push({ name: 'orders.show', params: { id: orderId } });
    }
  }
};
</script>

<template>
  <div class="max-w-4xl mx-auto p-4 bg-white shadow-md rounded-lg mt-8 mb-16 border border-gray-200">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">My orders</h1>
      <router-link class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500" :to="{ name: 'products' }">
        Continue shopping
      </router-link>
    </div>

    <div v-if="loading" class="p-3 bg-blue-50 text-blue-800 rounded">
      Loading order list...
    </div>
    <div v-else-if="error" class="p-3 bg-red-50 text-red-700 rounded">
      {{ error }}
    </div>

    <div v-else>
      <div v-if="!items || !items.length" class="p-3 bg-gray-50 text-gray-600 rounded">
        You have no orders yet.
      </div>
      <div v-else class="divide-y">
        <div
            v-for="order in items"
            :key="order.id"
            class="bg-white p-4 rounded-2xl shadow border border-indigo-200/75 flex flex-col justify-between
            transition-all duration-300 ease-in-out hover:shadow-xl hover:-translate-y-1 hover:border-indigo-400/90 mt-2"
            @click="goto(order.id)"
        >
          <div class="flex-1">
            <div class="font-semibold text-lg text-primary-400">Order #{{ order.id }}</div>
            <div class="text-sm text-gray-600">
              {{ new Date(order.created_at).toLocaleString() }}
              • {{ order.status }}
            </div>
          </div>
          <div class="text-right">
            <div class="font-semibold text-green-800">{{ order.total }} {{ order.currency }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>