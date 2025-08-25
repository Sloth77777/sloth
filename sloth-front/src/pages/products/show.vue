<script>
import { useProductStore } from "@/stores/productStore";
import { capitalizeFirst } from "@/utils/stringUtils.js";
import { useCartStore } from "@/stores/cartStore.ts";

export default {
  name: "ProductShow",

  data() {
    return {
      product: null,
      mainImage: null,
      cartStore: useCartStore(),
      relatedProducts: [],
      currentSlide: 0,
      slidesPerView: 4,
    };
  },

  watch: {
    '$route.params.id': {
      immediate: true,
      handler:
          async function (newId) {
        if (newId) {
          const response = await this.productStore.fetchProductById(Number(newId));
          this.product = response.data;
          this.relatedProducts = response.related_products || [];
          this.mainImage = this.product.preview_image;
        }
      }
    }
  },

  computed: {
    productStore() {
      return useProductStore();
    },
    needSlider() {
      return this.product?.images?.length > 4;
    },
    visibleImages() {
      if (!this.needSlider) return this.product.images;
      const start = this.currentSlide;
      return this.product.images.slice(start, start + this.slidesPerView);
    },
  },

  async mounted() {
    const id = Number(this.$route.params.id);
    if (id) {
      const response = await this.productStore.fetchProductById(id);
      this.product = response.data;
      this.relatedProducts = response.related_products || [];
      this.mainImage = this.product.preview_image;
    }
  },

  methods: {
    setMainImage(img) {
      this.mainImage = img;
    },
    capitalizeFirst,
    async addToCart(productId) {
      await this.cartStore.addProduct(productId, 1);
    },
    nextSlide() {
      if (this.currentSlide + this.slidesPerView < this.product.images.length) {
        this.currentSlide++;
      }
    },
    prevSlide() {
      if (this.currentSlide > 0) {
        this.currentSlide--;
      }
    },
  }
};
</script>

<template>
  <div class="container mx-auto mt-6" v-if="product">
    <p v-if="product.category.full" class="text-gray-400">
      Category List: {{ capitalizeFirst(product.category.full) }}
    </p>
    <p v-else class="text-gray-400">
      Category: {{ capitalizeFirst(product.category.title) }}
    </p>

    <div class="bg-white p-6 rounded shadow-md flex flex-col md:flex-row gap-6">
      <div class="flex flex-col gap-2 md:w-1/3">
        <img v-if="mainImage" :src="mainImage" alt="Main"
             class="h-full w-120 object-cover rounded mb-2 transition-opacity duration-300">
      </div>

      <div class="flex-1">
        <h1 class="text-3xl font-bold mb-4">{{ product.title }}</h1>
        <p class="text-gray-700 mb-2">{{ product.description }}</p>

        <template v-if="product?.price_latest">
          <p class="text-green-700 text-lg font-bold">
            Promotion: {{ product.price }}$
          </p>
          <p class="text-red-500 text-lg font-semibold line-through">
            {{ product?.price_latest }}$
          </p>
        </template>
        <template v-else>
          <p class="text-gray-600 text-lg">{{ product.price }}$</p>
        </template>
        <p class="text-gray-900 font-semibold">
          Count : {{ product.discount }}
        </p>
        <button class="mt-4 bg-green-600 text-white px-8 py-4 rounded hover:bg-green-500 transition" @click.prevent="addToCart(product.id)">
          Add to cart
        </button>
        <div class="relative mt-5">
          <div class="flex gap-2 transition-transform duration-300 ease-in-out">
            <button v-if="needSlider && currentSlide > 0" @click="prevSlide"
                    class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md z-10 transition-all duration-300 hover:bg-gray-100"
                    :disabled="currentSlide === 0">
              <span class="text-gray-600">&lt;</span>
            </button>
            <img
                v-for="(img, index) in visibleImages"
                :key="index"
                :src="img"
                alt="Extra"
                class="w-70 h-70 object-cover rounded cursor-pointer border-2 transition-all duration-300 hover:scale-105"
                :class="{ 'border-blue-500': mainImage === img }"
                @click="setMainImage(img)"
            />
            <button v-if="needSlider && currentSlide + slidesPerView < product.images.length" @click="nextSlide"
                    class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-white p-2 rounded-full shadow-md z-10 transition-all duration-300 hover:bg-gray-100"
                    :disabled="currentSlide + slidesPerView >= product.images.length">
              <span class="text-gray-600">&gt;</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-5">
      <h2 class="text-2xl font-bold mb-4">Similar products</h2>
      <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <li v-for="relatedProduct in relatedProducts" :key="relatedProduct.id" class="bg-white p-4 border border-indigo-200/75 rounded flex flex-col">
          <router-link :to="{ name: 'product.show', params: { id: relatedProduct.id } }" class="block mb-2">
            <img :src="relatedProduct.preview_image" alt="Preview" class="w-full h-60 object-cover rounded mb-2">
            <strong class="text-lg">{{ relatedProduct.title }}</strong>
          </router-link>
          <p class="text-gray-600 text-lg">{{ relatedProduct.price }}$</p>
          <button class="bg-green-600 text-white px-3 py-2 rounded mt-2 hover:bg-green-700 transition cursor-pointer" @click.prevent="addToCart(relatedProduct.id)">
            Add to cart
          </button>
        </li>
      </ul>
    </div>
  </div>
</template>
