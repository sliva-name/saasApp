<template>
  <div class="products-page">
    <!-- Page Header -->
    <div class="page-header bg-gray-50 py-8">
      <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold mb-2">All Products</h1>
        <p class="text-gray-600">Discover our complete collection of amazing products</p>
      </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Filters Sidebar -->
        <div class="lg:w-1/4">
          <div class="bg-white rounded-lg shadow-sm border p-6 sticky top-4">
            <h3 class="text-lg font-semibold mb-4">Filters</h3>
            
            <!-- Search -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Search Products</label>
              <SearchBar 
                v-model="searchQuery"
                @search="handleSearch"
                placeholder="Search products..."
              />
            </div>

            <!-- Categories -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
              <select 
                v-model="selectedCategory" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">All Categories</option>
                <option 
                  v-for="category in categories" 
                  :key="category.id" 
                  :value="category.id"
                >
                  {{ category.name }} ({{ category.products_count }})
                </option>
              </select>
            </div>

            <!-- Price Range -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
              <div class="grid grid-cols-2 gap-2">
                <input 
                  v-model.number="priceRange.min"
                  type="number" 
                  placeholder="Min"
                  class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <input 
                  v-model.number="priceRange.max"
                  type="number" 
                  placeholder="Max"
                  class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>

            <!-- Stock Filter -->
            <div class="mb-6">
              <label class="flex items-center">
                <input 
                  v-model="inStockOnly"
                  type="checkbox" 
                  class="mr-2 rounded text-blue-600 focus:ring-blue-500"
                />
                <span class="text-sm text-gray-700">In Stock Only</span>
              </label>
            </div>

            <!-- Sort Options -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
              <select 
                v-model="sortBy" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="name">Name A-Z</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
                <option value="newest">Newest First</option>
              </select>
            </div>

            <!-- Clear Filters -->
            <button 
              @click="clearFilters"
              class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors"
            >
              Clear All Filters
            </button>
          </div>
        </div>

        <!-- Products Grid -->
        <div class="lg:w-3/4">
          <!-- Results Header -->
          <div class="flex justify-between items-center mb-6">
            <div>
              <p class="text-gray-600">
                <span v-if="!loading">
                  Showing {{ products.length }} of {{ totalProducts }} products
                </span>
                <span v-else>Loading...</span>
              </p>
            </div>
          </div>

          <!-- Products -->
          <ProductGrid 
            :products="products"
            :loading="loading"
            @add-to-cart="addToCart"
          />

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="mt-8">
            <nav class="flex justify-center space-x-2">
              <button 
                @click="goToPage(page)"
                v-for="page in visiblePages" 
                :key="page"
                :class="[
                  'px-4 py-2 border rounded-md',
                  page === currentPage 
                    ? 'bg-blue-600 text-white border-blue-600' 
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ProductGrid from '../components/ProductGrid.vue';
import SearchBar from '../components/SearchBar.vue';

const route = useRoute();
const router = useRouter();

// State
const products = ref([]);
const categories = ref([]);
const loading = ref(false);
const totalProducts = ref(0);
const currentPage = ref(1);
const totalPages = ref(1);
const itemsPerPage = 12;

// Filters
const searchQuery = ref('');
const selectedCategory = ref('');
const priceRange = ref({ min: '', max: '' });
const inStockOnly = ref(false);
const sortBy = ref('name');

// Computed
const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 5;
  const half = Math.floor(maxVisible / 2);
  
  let start = Math.max(1, currentPage.value - half);
  let end = Math.min(totalPages.value, start + maxVisible - 1);
  
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1);
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
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
const loadProducts = async () => {
  loading.value = true;
  
  try {
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
    
    if (priceRange.value.min) {
      params.append('min_price', priceRange.value.min);
    }
    
    if (priceRange.value.max) {
      params.append('max_price', priceRange.value.max);
    }
    
    if (inStockOnly.value) {
      params.append('in_stock', 'true');
    }
    
    const response = await fetch(`/api/products?${params}`);
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const data = await response.json();
    products.value = data.data || [];
    totalProducts.value = data.meta?.total || 0;
    totalPages.value = data.meta?.last_page || 1;
    
    // Update URL with current filters
    updateURL();
    
  } catch (error) {
    console.error('Failed to load products:', error);
    products.value = [];
    totalProducts.value = 0;
  } finally {
    loading.value = false;
  }
};

const loadCategories = async () => {
  try {
    const response = await fetch('/api/categories?only_with_products=true');
    
    if (response.ok) {
      const data = await response.json();
      categories.value = data.data || [];
    }
  } catch (error) {
    console.error('Failed to load categories:', error);
    categories.value = [];
  }
};

const addToCart = (product) => {
  console.log('Adding to cart:', product);
  // TODO: Implement cart functionality
  alert(`Added ${product.name} to cart!`);
};

const handleSearch = (query) => {
  searchQuery.value = query;
  currentPage.value = 1;
};

const goToPage = (page) => {
  currentPage.value = page;
};

const clearFilters = () => {
  searchQuery.value = '';
  selectedCategory.value = '';
  priceRange.value = { min: '', max: '' };
  inStockOnly.value = false;
  sortBy.value = 'name';
  currentPage.value = 1;
};

const updateURL = () => {
  const query = {};
  
  if (searchQuery.value) query.search = searchQuery.value;
  if (selectedCategory.value) query.category = selectedCategory.value;
  if (priceRange.value.min) query.min_price = priceRange.value.min;
  if (priceRange.value.max) query.max_price = priceRange.value.max;
  if (inStockOnly.value) query.in_stock = 'true';
  if (sortBy.value !== 'name') query.sort = sortBy.value;
  if (currentPage.value > 1) query.page = currentPage.value;
  
  router.replace({ query });
};

const loadFromURL = () => {
  const query = route.query;
  
  searchQuery.value = query.search || '';
  selectedCategory.value = query.category || '';
  priceRange.value.min = query.min_price || '';
  priceRange.value.max = query.max_price || '';
  inStockOnly.value = query.in_stock === 'true';
  sortBy.value = query.sort || 'name';
  currentPage.value = parseInt(query.page) || 1;
};

// Watchers
watch([selectedCategory, priceRange, inStockOnly, sortBy], () => {
  currentPage.value = 1;
  loadProducts();
}, { deep: true });

watch(currentPage, () => {
  loadProducts();
});

watch(searchQuery, () => {
  currentPage.value = 1;
  loadProducts();
});

// Lifecycle
onMounted(async () => {
  loadFromURL();
  await loadCategories();
  await loadProducts();
});
</script>

<style scoped>
.products-page {
  min-height: 100vh;
  background-color: #fafafa;
}

.container {
  max-width: 1200px;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
  .lg\:w-1\/4 {
    width: 100%;
  }
  
  .lg\:w-3\/4 {
    width: 100%;
  }
  
  .sticky {
    position: relative !important;
  }
}
</style>
