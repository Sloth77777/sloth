<script lang="ts">
import Search from "@/components/Search.vue";
import {useFilterStore} from "@/stores/filterStore";


export default {
  name: "Sidebar",
  components: {Search},
  setup() {
    const filterStore = useFilterStore();
    return {filterStore};
  },
  data() {
    return {
      minPrice: 0,
      maxPrice: 0,
      searchQuery: "",
    };
  },

  async mounted() {
    await this.filterStore.fetchFilterList();
    this.minPrice = this.filterStore.filtersList.price.min;
    this.maxPrice = this.filterStore.filtersList.price.max;
  },

  methods: {
    applyFilters(search: string | null = null) {
      this.$emit("filter-changed", {
        search: search ?? this.searchQuery,
        price_min: this.minPrice,
        price_max: this.maxPrice,
      });
    },
    resetFilters() {
      this.minPrice = this.filterStore.filtersList.price.min;
      this.maxPrice = this.filterStore.filtersList.price.max;
      this.searchQuery = "";
      this.applyFilters();
    },
  },

};
</script>
<template>
  <aside class="w-1/6 bg-white shadow-md p-4">
    <template v-if="filterStore.loaded">
      <h2 class="text-xl font-bold mt-6 mb-2">Price</h2>
      <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-2xl shadow-sm">
        <input
            type="number"
            v-model.number="minPrice"
            :min="filterStore.filtersList.price.min"
            placeholder="Min"
            class="w-1/2 px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
        />
        <span class="text-gray-500">—</span>
        <input
            type="number"
            v-model.number="maxPrice"
            :max="filterStore.filtersList.price.max"
            placeholder="Max"
            class="w-1/2 px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
        />
      </div>
      <div class="flex flex-wrap gap-2 mt-4">
        <Search v-model="searchQuery" @searchChanged="applyFilters"/>
      </div>

      <button @click="applyFilters()"
              class="mt-4 w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition">
        Apply
      </button>

      <button @click="resetFilters"
              class="mt-2 w-full bg-gray-400 text-white py-2 rounded hover:bg-gray-500 transition">
        Reset
      </button>
    </template>
    <template v-else>
      <div class="text-sm text-gray-500">
        {{ filterStore.error || 'Loading filters...' }}
      </div>
    </template>
  </aside>
</template>