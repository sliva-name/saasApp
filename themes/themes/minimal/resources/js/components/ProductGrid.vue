<template>
  <div class="product-grid">
    <div v-if="loading" class="text-center py-8">
      <p>Loading products...</p>
    </div>
    
    <div v-else-if="products.length === 0" class="text-center py-8">
      <p class="text-gray-500">No products available.</p>
    </div>
    
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <ProductCard 
        v-for="product in products" 
        :key="product.id"
        :product="product"
        @add-to-cart="$emit('add-to-cart', $event)"
      />
    </div>
  </div>
</template>

<script setup>
import ProductCard from './ProductCard.vue';

defineProps({
  products: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
});

defineEmits(['add-to-cart']);
</script>
