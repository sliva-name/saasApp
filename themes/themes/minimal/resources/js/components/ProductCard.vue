<template>
  <div class="product-card">
    <router-link :to="`/product/${product.slug}`" class="product-image">
      <img 
        :src="product.image || '/images/placeholder.jpg'" 
        :alt="product.name"
        class="w-full h-48 object-cover"
      />
      <div v-if="!product.in_stock" class="out-of-stock-overlay">
        <span class="out-of-stock-text">Out of Stock</span>
      </div>
    </router-link>
    <div class="product-info">
      <router-link :to="`/product/${product.slug}`">
        <h3 class="product-title">{{ product.name }}</h3>
      </router-link>
      <p v-if="product.category" class="product-category">{{ product.category.name }}</p>
      <div class="product-price">
        <span class="current-price">${{ product.price.toFixed(2) }}</span>
        <span v-if="product.old_price" class="old-price">${{ product.old_price.toFixed(2) }}</span>
      </div>
      <div class="product-stock" v-if="product.stock <= 5 && product.stock > 0">
        <span class="low-stock">Only {{ product.stock }} left in stock</span>
      </div>
      <button 
        @click="addToCart" 
        class="add-to-cart-btn"
        :disabled="!product.in_stock"
      >
        {{ product.in_stock ? 'Add to Cart' : 'Out of Stock' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { useThemeConfig } from '../composables/useThemeConfig';

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['add-to-cart']);

const { themeConfig } = useThemeConfig();

const addToCart = () => {
  emit('add-to-cart', props.product);
};
</script>

<style scoped>
.product-card {
  @apply bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow;
  border: 1px solid var(--color-border, #e2e8f0);
}

.product-image {
  @apply relative block;
}

.out-of-stock-overlay {
  @apply absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center;
}

.out-of-stock-text {
  @apply text-white font-bold text-lg;
}

.product-category {
  @apply text-xs text-gray-500 mb-1 uppercase tracking-wide;
}

.product-stock {
  @apply mb-2;
}

.low-stock {
  @apply text-xs text-orange-500 font-medium;
}

.product-info {
  @apply p-4;
}

.product-title {
  @apply text-lg font-semibold mb-2;
  color: var(--color-text-primary, #1a202c);
}

.product-description {
  @apply text-sm mb-3;
  color: var(--color-text-secondary, #718096);
}

.product-price {
  @apply flex items-center gap-2 mb-3;
}

.current-price {
  @apply text-xl font-bold;
  color: var(--color-primary, #3b82f6);
}

.old-price {
  @apply text-sm line-through;
  color: var(--color-text-muted, #a0aec0);
}

.add-to-cart-btn {
  @apply w-full py-2 px-4 rounded-md font-medium transition-colors;
  background-color: var(--color-primary, #3b82f6);
  color: white;
}

.add-to-cart-btn:hover:not(:disabled) {
  background-color: var(--color-primary-dark, #2563eb);
}

.add-to-cart-btn:disabled {
  @apply bg-gray-300 cursor-not-allowed;
}
</style>
