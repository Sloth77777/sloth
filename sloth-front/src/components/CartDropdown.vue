<script lang="ts">
import { defineComponent } from 'vue';
import { useCartStore } from '@/stores/cartStore';

export default defineComponent({
  data() {
    return {
      isOpen: false,
    };
  },

  computed: {
    cart() {
      return useCartStore();
    }
  },

  async mounted() {
    await this.cart.loadCart();
  },

  methods: {
    async toggle() {
      this.isOpen = !this.isOpen;
    },

    async clearCart() {
      await this.cart.clearAll();
    },

    goToCheckout() {
      if (this.cart.displayItems.length === 0) return;
      this.isOpen = false;
      this.$router.push('/checkout');
    }
  }
});
</script>

<template>
  <div class="relative">
    <button @click="toggle" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
      Cart ({{ cart.displayItems.length }})
    </button>

    <div v-if="isOpen" class="absolute right-0 mt-2 w-86 bg-white shadow-lg rounded border border-gray-200 z-50">
      <div class="p-4">
        <h3 class="font-bold text-lg mb-2">Cart</h3>

        <div v-if="cart.loading" class="text-gray-500">Loading...</div>
        <div v-else-if="cart.error" class="text-red-600 text-sm">{{ cart.error }}</div>

        <ul v-else-if="cart.displayItems.length" class="max-h-80 overflow-y-auto">
          <li v-for="item in cart.displayItems" :key="item.id" class="flex justify-between mb-2">
            <div>
              <img v-if="item.preview_image" class="w-full h-50 object-cover rounded mb-2 transition-transform duration-300 group-hover:scale-105" :src="item.preview_image" alt=""/>
              <p>{{ item.title }}</p>
              <p class="text-sm text-gray-500">x{{ item.quantity }}</p>
              <div>{{ (item.price * item.quantity).toFixed(2) }}$</div>
              <button @click="goToCheckout" v-if="cart.displayItems.length > 0" class="mt-3 w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition" :disabled="cart.loading">
                Pay
              </button>
            </div>
          </li>
        </ul>

        <p v-else class="text-gray-500">Cart null</p>

        <div class="mt-4 font-bold flex justify-between">
          <span>Total:</span>
          <span>{{ cart.totalPrice.toFixed(2) }}$</span>
        </div>

        <button v-if="cart.displayItems.length > 0" class="mt-3 w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition" :disabled="cart.loading" @click="clearCart">
          Clear Cart
        </button>
      </div>
    </div>
  </div>
</template>
