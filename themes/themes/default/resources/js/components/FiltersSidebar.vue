<template>
  <div class="filters-sidebar">
    <h3 class="text-lg font-semibold mb-4">Filters</h3>
    
    <!-- Categories -->
    <div class="filter-section mb-6">
      <h4 class="font-medium mb-3">Categories</h4>
      <div class="space-y-2">
        <label v-for="category in categories" :key="category.id" class="flex items-center">
          <input 
            type="checkbox" 
            :value="category.id"
            v-model="selectedCategories"
            @change="$emit('filter-change', getFilters())"
            class="mr-2"
          />
          <span class="text-sm">{{ category.name }}</span>
        </label>
      </div>
    </div>
    
    <!-- Price Range -->
    <div class="filter-section mb-6">
      <h4 class="font-medium mb-3">Price Range</h4>
      <div class="space-y-3">
        <div class="flex items-center space-x-2">
          <input 
            v-model.number="priceRange.min"
            @input="$emit('filter-change', getFilters())"
            type="number" 
            placeholder="Min"
            class="w-20 px-2 py-1 border rounded text-sm"
          />
          <span>-</span>
          <input 
            v-model.number="priceRange.max"
            @input="$emit('filter-change', getFilters())"
            type="number" 
            placeholder="Max"
            class="w-20 px-2 py-1 border rounded text-sm"
          />
        </div>
      </div>
    </div>
    
    <!-- Availability -->
    <div class="filter-section mb-6">
      <h4 class="font-medium mb-3">Availability</h4>
      <div class="space-y-2">
        <label class="flex items-center">
          <input 
            type="checkbox" 
            v-model="inStockOnly"
            @change="$emit('filter-change', getFilters())"
            class="mr-2"
          />
          <span class="text-sm">In Stock Only</span>
        </label>
      </div>
    </div>
    
    <!-- Clear Filters -->
    <button 
      @click="clearFilters"
      class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded transition-colors"
    >
      Clear All Filters
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  categories: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['filter-change']);

// State
const selectedCategories = ref([]);
const priceRange = ref({ min: null, max: null });
const inStockOnly = ref(false);

// Methods
const getFilters = () => {
  return {
    categories: selectedCategories.value,
    priceRange: priceRange.value,
    inStockOnly: inStockOnly.value
  };
};

const clearFilters = () => {
  selectedCategories.value = [];
  priceRange.value = { min: null, max: null };
  inStockOnly.value = false;
  emit('filter-change', getFilters());
};
</script>

<style scoped>
.filters-sidebar {
  @apply bg-white border border-gray-200 rounded-lg p-4;
}

.filter-section {
  @apply border-b border-gray-100 pb-4;
}

.filter-section:last-child {
  @apply border-b-0 pb-0;
}

input[type="checkbox"] {
  @apply rounded border-gray-300 text-blue-600 focus:ring-blue-500;
}

input[type="number"] {
  @apply border-gray-300 focus:border-blue-500 focus:ring-blue-500;
}
</style>
