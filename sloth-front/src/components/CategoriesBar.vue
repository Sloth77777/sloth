<script lang="ts">
import { useCategoriesStore } from "@/stores/categoriesStore";

type Category = { id: number; title: string };

export default {
  name: "CategoriesBar",
  emits: ["category-selected"],

  data() {
    return {
      activeId: null as number | null,
      allTitle: "All",
      categoriesStore: useCategoriesStore(),
    };
  },

  computed: {
    categories(): Category[] {
      return this.categoriesStore.list;
    },
  },

  async mounted() {
      await this.categoriesStore.fetchCategories();
    this.activeId = this.categoriesStore.selectedId ?? null;
  },

  watch: {
    "categoriesStore.selectedId"(val: number | null) {
      this.activeId = val ?? null;
    },
  },

  methods: {
    selectCategory(id: number | null, title: string) {
      this.activeId = id;
      this.categoriesStore.selectCategory(id);
      this.$emit("category-selected", { id, title });
    },
  },
};
</script>

<template>
  <div class="bg-white rounded-xl shadow-sm p-3 mb-4">
    <div class="flex flex-wrap gap-2">
      <button class="px-4 py-2 rounded-full border transition" :class="activeId === null
          ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-indigo-50 hover:border-indigo-300'"
          @click="selectCategory(null, allTitle)">
        {{ allTitle }}
      </button>

      <button v-for="category in categories" :key="category.id" class="px-4 py-2 rounded-full border transition"
          :class="activeId === category.id ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-indigo-50 hover:border-indigo-300'"
          @click="selectCategory(category.id, category.title ?? `Category ${category.id}`)">
        {{ category.title ?? `Category ${category.id}` }}
      </button>
    </div>
  </div>
</template>