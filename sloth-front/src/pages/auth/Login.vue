<script lang="ts">
import {defineComponent} from 'vue';
import type {AxiosError} from "axios";
import {useAuthStore} from "@/stores/authStore";
import {useCartStore} from "@/stores/cartStore";

export default defineComponent({
  name: "Login",
  data() {
    return {
      auth: useAuthStore(),
      cart:useCartStore(),
      email: '',
      password: '',
      error: null as string | null
    }
  },

  methods: {
    async login() {
      try {
        await this.auth.login({
          email: this.email,
          password: this.password
        });
        // this.$router.push({name: 'products'});
      } catch (err: unknown) {
        const error = err as AxiosError<{ message?: string }>;
        this.error = error.response?.data?.message || (err instanceof Error ? err.message : 'Login failed');
      }
    }
  }
});
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Login to Your Account</h2>
      <div class="mb-4">
        <label class="block text-gray-700 mb-2" for="email">Email</label>
        <input
            id="email"
            type="email"
            v-model="email"
            placeholder="you@example.com"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>

      <div class="mb-6">
        <label class="block text-gray-700 mb-2" for="password">Password</label>
        <input
            id="password"
            type="password"
            v-model="password"
            placeholder="********"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        />
      </div>
      <div>
        <p v-if="error" class="text-red-500 mb-4">{{ error }}</p>
        <button @click="login"
                class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200">
          Login
        </button>
      </div>
      <br>
      <router-link :to="{ name: 'register' }"
                   class="w-full block text-center py-3 bg-blue-400 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200">
        Register
      </router-link>

    </div>
  </div>
</template>

