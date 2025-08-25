<script lang="ts">
import {useAuthStore} from '@/stores/authStore';

export default {
  name: "Register",

  data() {
    return {
      title: '',
      email: '',
      password: '',
      password_confirmation: '',
      isLoading: false,
      auth: useAuthStore()
    }
  },

  methods: {
    async register() {
      this.isLoading = true;
      try {
        await this.auth.register({
          name: this.title,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation
        });
        this.$router.push({name: 'products'});
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>
<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Create an Account</h2>

      <div class="mb-4">
        <label class="block text-gray-700 mb-2" for="name">Name</label>
        <input
            id="name"
            type="text"
            v-model="title"
            placeholder="Your name"
            :class="['w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                    auth.errors.name ? 'border-red-500' : 'border-gray-300']"
        />
        <p v-if="auth.errors.name" class="mt-1 text-red-500 text-sm">{{ auth.errors.name }}</p>
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 mb-2" for="email">Email</label>
        <input
            id="email"
            type="email"
            v-model="email"
            placeholder="you@example.com"
            :class="['w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                    auth.errors.email ? 'border-red-500' : 'border-gray-300']"
        />
        <p v-if="auth.errors.email" class="mt-1 text-red-500 text-sm">{{ auth.errors.email }}</p>
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 mb-2" for="password">Password</label>
        <input
            id="password"
            type="password"
            v-model="password"
            placeholder="********"
            :class="['w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                    auth.errors.password ? 'border-red-500' : 'border-gray-300']"
        />
        <p v-if="auth.errors.password" class="mt-1 text-red-500 text-sm">{{ auth.errors.password }}</p>
      </div>

      <div class="mb-6">
        <label class="block text-gray-700 mb-2" for="password_confirmation">Confirm Password</label>
        <input
            id="password_confirmation"
            type="password"
            v-model="password_confirmation"
            placeholder="********"
            :class="['w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                    auth.errors.password ? 'border-red-500' : 'border-gray-300']"
        />
        <p v-if="auth.errors.password" class="mt-1 text-red-500 text-sm">{{ auth.errors.password }}</p>
      </div>

      <button
          @click="register"
          :disabled="isLoading"
          class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {{ isLoading ? 'Registering...' : 'Register' }}
      </button>
    </div>
  </div>
</template>