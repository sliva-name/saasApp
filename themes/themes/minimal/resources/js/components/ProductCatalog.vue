<template>
  <div class="product-catalog">
    <div class="catalog-header mb-8">
      <h1 class="text-3xl font-bold mb-4">Our Products</h1>
      <p class="text-gray-600">Discover our amazing collection of products</p>
    </div>

    <!-- Filters -->
    <div class="filters-section mb-8">
      <div class="flex flex-wrap gap-4 items-center">
        <select v-model="selectedCategory" class="px-4 py-2 border rounded-lg">
          <option value="">All Categories</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        
        <select v-model="sortBy" class="px-4 py-2 border rounded-lg">
          <option value="name">Sort by Name</option>
          <option value="price_asc">Price: Low to High</option>
          <option value="price_desc">Price: High to Low</option>
          <option value="newest">Newest First</option>
        </select>
        
        <div class="search-box">
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Search products..."
            class="px-4 py-2 border rounded-lg"
          />
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <div class="products-grid">
      <div v-if="error" class="text-center py-8">
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 max-w-md mx-auto">
          <p class="text-red-700">Failed to load products: {{ error }}</p>
          <button 
            @click="loadProducts" 
            class="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
          >
            Try Again
          </button>
        </div>
      </div>
      
      <div v-else-if="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Loading products...</p>
      </div>
      
      <div v-else-if="products.length === 0" class="text-center py-8">
        <p class="text-gray-500">No products found.</p>
        <p class="text-sm text-gray-400 mt-2">Try adjusting your search or filters.</p>
      </div>
      
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <ProductCard 
          v-for="product in products" 
          :key="product.id"
          :product="product"
          @add-to-cart="addToCart"
        />
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="pagination mt-8 flex justify-center">
      <nav class="flex space-x-2">
        <button 
          v-for="page in totalPages" 
          :key="page"
          @click="currentPage = page"
          :class="[
            'px-4 py-2 border rounded',
            page === currentPage ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'
          ]"
        >
          {{ page }}
        </button>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import ProductCard from './ProductCard.vue';

// State
const loading = ref(true);
const products = ref([]);
const categories = ref([]);
const selectedCategory = ref('');
const sortBy = ref('name');
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 12;

// Additional state for API handling
const totalItems = ref(0);
const hasMorePages = ref(false);
const error = ref(null);

// Computed
const totalPages = computed(() => {
  return Math.ceil(totalItems.value / itemsPerPage);
});

const sortOptions = computed(() => {
  const sortMap = {
    'name': { field: 'name', direction: 'asc' },
    'price_asc': { field: 'price', direction: 'asc' },
    'price_desc': { field: 'price', direction: 'desc' },
    'newest': { field: 'created_at', direction: 'desc' }
  };
  return sortMap[sortBy.value] || { field: 'name', direction: 'asc' };
});

// Methods
const addToCart = (product) => {
  console.log('Adding to cart:', product);
  alert(`Added ${product.name} to cart!`);
};

const loadProducts = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    // Build query parameters
    const params = new URLSearchParams({
      page: currentPage.value,
      per_page: itemsPerPage,
      sort_by: sortOptions.value.field,
      sort_direction: sortOptions.value.direction
    });
    
    if (selectedCategory.value) {
      params.append('category_id', selectedCategory.value);
    }
    
    if (searchQuery.value) {
      params.append('search', searchQuery.value);
    }
    
    const response = await fetch(`/api/products?${params}`);
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const data = await response.json();
    products.value = data.data || [];
    totalItems.value = data.meta?.total || 0;
    hasMorePages.value = data.meta?.current_page < data.meta?.last_page;
    
  } catch (err) {
    console.error('Failed to load products:', err);
    error.value = err.message;
    products.value = [];
    totalItems.value = 0;
  } finally {
    loading.value = false;
  }
};

const loadCategories = async () => {
  try {
    const response = await fetch('/api/categories?only_with_products=true');
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const data = await response.json();
    categories.value = data.data || [];
    
  } catch (err) {
    console.error('Failed to load categories:', err);
    categories.value = [];
  }
};

// Watchers
watch([selectedCategory, sortBy, searchQuery], () => {
  currentPage.value = 1; // Reset to first page when filters change
  loadProducts();
});

watch(currentPage, () => {
  loadProducts();
});

// Lifecycle
onMounted(async () => {
  await Promise.all([
    loadCategories(),
    loadProducts()
  ]);
});
</script>

<style scoped>
.product-catalog {
  max-width: 1200px;
  margin: 0 auto;
}

.search-box input:focus {
  outline: none;
  border-color: var(--color-primary, #3b82f6);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

select:focus {
  outline: none;
  border-color: var(--color-primary, #3b82f6);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>
