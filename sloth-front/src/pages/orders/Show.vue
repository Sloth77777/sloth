<script lang="ts">
import {useOrdersStore} from '@/stores/orderStore';
import {useAuthStore} from '@/stores/authStore';
import {isAxiosError} from 'axios';
import login from "@/pages/auth/Login.vue";

export default {
  name: 'OrderShow',
  props: {
    id: {
      type: [Number, String],
      required: false,
    }
  },
  data() {
    return {
      orders: useOrdersStore(),
      auth: useAuthStore(),
    };
  },
  computed: {
    loading() {
      return this.orders.loading;
    },
    error() {
      return this.orders.error;
    },
    order() {
      return this.orders.current;
    }
  },

  async mounted() {
    const id = this.id ?? (this.$route.params.id as string | number | undefined);

    if (!id) {
      return;
    }
    try {
      await this.orders.show(id);
    } catch (error: unknown) {
      this.orders.error = isAxiosError(error)
          ? (error.response?.data?.message || error.message)
          : (error instanceof Error ? error.message : 'Error occurred');
    }
  },
  methods: {
    goBack() {
      this.$router.push({name: 'products'});
    }
  }
};
</script>

<template>
  <div class=" rounded-4xl max-w-3xl mx-auto p-4 bg-white shadow-md mt-8 mb-16 border border-gray-200 flex flex-col justify-between
            transition-all duration-300 ease-in-out hover:shadow-xl hover:-translate-y-1 hover:border-indigo-400/90">
    <div class="flex items-center justify-between mb-4 ">
      <h1 class="text-2xl font-bold">Order</h1>
      <button
          class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200 text-sm"
          @click="goBack"
      >
        Back products
      </button>
    </div>

    <div v-if="loading" class="p-3 bg-blue-50 text-blue-800 rounded">
      Loading order...
    </div>
    <div v-else-if="error" class="p-3 bg-red-50 text-red-700 rounded">
      {{ error }}
    </div>

    <div v-else-if="order" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-3 bg-gray-50 rounded">
          <div class="text-sm text-gray-500">Status</div>
          <div class="text-lg font-semibold">{{ order.status }}</div>
        </div>
        <div class="p-3 bg-gray-50 rounded">
          <div class="text-sm text-gray-500">Date</div>
          <div class="text-lg font-semibold">{{ new Date(order.created_at).toLocaleString() }}</div>
        </div>
        <div class="p-3 bg-gray-50 rounded">
          <div class="text-sm text-gray-500">Summary</div>
          <div class="text-lg font-semibold">
            {{ order.total }} {{ order.currency }}
          </div>
        </div>
      </div>
      <div class="flex items-center justify-between">
        <div class="p-4 w-full" v-if="order.items && order.items.length">
          <h2 class="text-lg font-semibold mb-2">Products</h2>
          <div class="divide-y">
            <div v-for="item in order.items" :key="item.id"
                 class="py-3 flex items-start justify-between mb-3">
              <div class="mr-4 p-4">
                <div class="font-medium">{{ item.product_title || 'Product' }}</div>
                <div class="text-sm text-gray-500">Discount: {{ item.quantity }}</div>
                <div class="text-sm text-gray-500">Price: {{ item.unit_price }} {{ order.currency }}</div>
                <div class="font-semibold whitespace-nowrap">
                  {{ item.total }} {{ order.currency }}
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</template>