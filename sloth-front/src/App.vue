<script setup>
import Header from "@/components/Header.vue";
import Footer from "@/components/Footer.vue";
import CartDropdown from "@/components/CartDropdown.vue";
</script>

<template>
  <Header >
    <CartDropdown />
    <router-link to="/orders">My orders</router-link>
  </Header>
  <router-view>
  </router-view>
  <Footer />
</template>
