<script>
export default {
  name: "Search",

  props: {
    modelValue: {
      type: String,
      default: "",
    },
  },

  emits: [
    "update:modelValue",
    "searchChanged"
  ],

  methods: {
    onInput(event) {
      this.$emit("update:modelValue", event.target.value);
    },
    onSearch() {
      this.$emit("searchChanged", this.modelValue);
    },
  },
};
</script>

<template>
  <div class="flex border rounded overflow-hidden w-full max-w-md">
    <input
        type="text"
        :value="modelValue"
        @input="onInput"
        @keyup.enter="onSearch"
        placeholder="Product search"
        class="px-3 py-2 w-full focus:outline-none"
    />
    <button @click="onSearch" class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition">
      Find
    </button>
  </div>
</template>
