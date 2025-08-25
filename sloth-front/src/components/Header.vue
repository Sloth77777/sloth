<script lang="ts">
import Search from "@/components/Search.vue";
import CartDropdown from "@/components/CartDropdown.vue";
import {useAuthStore} from "@/stores/authStore";
export default {
  name: "Header",
  components: {Search , CartDropdown },

  data(){
    return {
      isOpen: false,
      authStore:useAuthStore(),
    }
  },

  computed: {
    isAuthenticated() {
      return this.authStore.isAuthenticated;
    },
    userName(){
      return this.authStore.userName;
    }
  },

  methods: {
    toggleMenu() {
      this.isOpen = !this.isOpen;
    },
  },
};
</script>

<template>
  <header class="bg-white shadow-md">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
      <div class="text-2xl font-bold text-gray-800">SlothShop</div>

      <router-link to="/products" class="text-gray-600 hover:text-gray-900 font-medium">
        Products
      </router-link>

      <div class="hidden md:block" v-if="!isAuthenticated">
        <router-link :to="{name:'login'}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
          Login
        </router-link>
      </div>
      <div class="flex" v-else>
        <div class="hidden md:block">
          {{ userName }}
        </div>
        <div class="ml-10">
        <router-link to="/orders">
          My orders
        </router-link>
        </div>
      </div>
      <div>
        <CartDropdown></CartDropdown>
      </div>

      <div class="md:hidden">
        <button @click="toggleMenu" class="text-gray-700 focus:outline-none">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
               viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>
    </div>
  </header>
</template>