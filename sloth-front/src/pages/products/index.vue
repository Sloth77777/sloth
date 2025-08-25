<script>
import { useProductStore } from "@/stores/productStore.ts";
import Sidebar from "@/components/Sidebar.vue";
import { useCartStore } from "@/stores/cartStore.ts";
import CategoriesBar from "@/components/CategoriesBar.vue";
import { useCategoriesStore } from "@/stores/categoriesStore";

export default {
  name: "index",
  components: {CategoriesBar, Sidebar },

  data() {
    return {
      productStore: useProductStore(),
      cartStore: useCartStore(),
      categoriesStore: useCategoriesStore(),
      activeFilters: {},
    };
  },

  mounted() {
    this.applyFiltersFromRoute();
  },

  watch: {
    "$route.query": {
      handler() {
        this.applyFiltersFromRoute();
      },
      deep: true,
    },
  },

  methods: {
    async changePage(page) {
      this.$router.push({ query: { ...this.$route.query, page } });
    },

    async applyFilters(filters) {
      this.activeFilters = filters;
      this.$router.push({
        query: {
          page: 1,
          search: filters.search || undefined,
          price_min: filters.price_min || undefined,
          price_max: filters.price_max || undefined,
          categories: this.$route.query.categories || undefined,
        },
      });
    },

    async applyFiltersFromRoute() {
      const { page, search, price_min, price_max, categories } = this.$route.query;

      const categoriesArray = categories === undefined ? undefined : Array.isArray(categories) ?
          categories.map((v) => Number(v)).filter(Number.isFinite) : [Number(categories)].filter(Number.isFinite);

      const selected = categoriesArray && categoriesArray.length === 1 ? categoriesArray[0] : null;
      this.categoriesStore.selectCategory(selected);

      const filters = {
        search: search || "",
        price_min: price_min ? Number(price_min) : undefined,
        price_max: price_max ? Number(price_max) : undefined,
        categories: categoriesArray,
      };

      this.activeFilters = filters;
      await this.productStore.fetchProducts(Number(page) || 1, filters);
    },

    onCategorySelected(payload) {
      const newCategories = payload.id == null ? undefined : payload.id;
      this.$router.push({
        query: {
          ...this.$route.query,
          page: 1,
          categories: newCategories ?? undefined,
        },
      });
    },

    async addToCart(productId) {
      await this.cartStore.addProduct(productId, 1);
    },
  },
};
</script>

<template>
  <div class="container mx-auto flex justify-center mt-6 mb-6 bg-white p-4 rounded shadow-md border border-indigo-200/75 transition-all duration-300 ease-in-out hover:shadow-xl hover:-translate-y-1 hover:border-indigo-400/90 ">
    <CategoriesBar @category-selected="onCategorySelected" />
  </div>
  <div class="container mx-auto flex mt-6 gap-6">
    <Sidebar @filter-changed="applyFilters" />
    <main class="flex-1 bg-gray-50 p-4 rounded shadow-md">
      <h1 class="text-2xl font-bold mb-4">Product List</h1>
      <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <li v-for="product in productStore.products" :key="product.id"
            class="bg-white p-4 rounded-2xl shadow border border-indigo-200/75 flex flex-col justify-between
            transition-all duration-300 ease-in-out hover:shadow-xl hover:-translate-y-1 hover:border-indigo-400/90">
          <div>
            <img v-if="product.preview_image" :src="product.preview_image"
                 alt="Preview"
                 class="w-full h-60 object-cover rounded mb-2 transition-transform duration-300 group-hover:scale-105"
            />
            <strong class="text-lg cursor-pointer">
              <router-link :to="{ name: 'product.show', params: { id: product.id } }">
                {{ product.title }}
              </router-link>
            </strong>

            <template v-if="product?.price_latest">
              <p class="text-green-700 text-lg font-bold">Promotion: {{ product.price }}$</p>
              <p class="text-red-500 text-lg font-semibold line-through">
                {{ product?.price_latest }}$
              </p>
            </template>
            <template v-else>
              <p class="text-gray-600 text-lg">{{ product.price }}$</p>
            </template>
          </div>

          <button @click="addToCart(product.id)" class="bg-green-600 text-white px-3 py-2 rounded mt-2 hover:bg-green-700 transition" >
            Add to cart
          </button>
        </li>
      </ul>

      <div v-if="productStore.pagination && productStore.pagination.last_page" class="flex justify-center mt-6 gap-1">
        <button v-if="productStore.pagination.current_page !== 1"
                class="text-lg px-4 py-2 rounded-md border border-gray-300 bg-white text-gray-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="productStore.pagination.current_page <= 1"
                @click="changePage(productStore.pagination.current_page - 1)">
          &lt;
        </button>

        <button v-for="page in productStore.pagination.last_page"
                :key="page"
                class="text-lg px-4 py-2 rounded-md border border-gray-300 bg-white text-gray-700 transition"
                @click="changePage(page)">
          {{ page }}
        </button>

        <button
            v-if="productStore.pagination.current_page !== productStore.pagination.last_page"
            class="text-lg px-4 py-2 rounded-md border border-gray-300 bg-white text-gray-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="productStore.pagination.current_page >= productStore.pagination.last_page"
            @click="changePage(productStore.pagination.current_page + 1)">
          &gt;
        </button>
      </div>
    </main>
  </div>
</template>