<template>
  <div class="product-detail">
    <div v-if="loading" class="text-center py-8">
      <p>Loading product...</p>
    </div>
    
    <div v-else-if="!product" class="text-center py-8">
      <p class="text-red-500">Product not found</p>
    </div>
    
    <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Product Images -->
      <div class="product-images">
        <div class="main-image mb-4">
          <img 
            :src="product.image || '/images/placeholder.jpg'" 
            :alt="product.name"
            class="w-full h-96 object-cover rounded-lg"
          />
        </div>
        <div v-if="product.gallery && product.gallery.length" class="gallery grid grid-cols-4 gap-2">
          <img 
            v-for="(image, index) in product.gallery" 
            :key="index"
            :src="image" 
            :alt="`${product.name} ${index + 1}`"
            class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75"
            @click="selectImage(image)"
          />
        </div>
      </div>
      
      <!-- Product Info -->
      <div class="product-info">
        <h1 class="text-3xl font-bold mb-4">{{ product.name }}</h1>
        
        <div class="price-section mb-6">
          <div class="flex items-center gap-4">
            <span class="text-3xl font-bold text-blue-600">${{ product.price }}</span>
            <span v-if="product.old_price" class="text-xl text-gray-500 line-through">
              ${{ product.old_price }}
            </span>
            <span v-if="product.old_price" class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">
              Save ${{ (product.old_price - product.price).toFixed(2) }}
            </span>
          </div>
        </div>
        
        <div class="description mb-6">
          <h3 class="text-lg font-semibold mb-2">Description</h3>
          <p class="text-gray-700 leading-relaxed">{{ product.description }}</p>
        </div>
        
        <div v-if="product.features" class="features mb-6">
          <h3 class="text-lg font-semibold mb-2">Features</h3>
          <ul class="list-disc list-inside space-y-1 text-gray-700">
            <li v-for="feature in product.features" :key="feature">{{ feature }}</li>
          </ul>
        </div>
        
        <div class="stock-status mb-6">
          <span 
            :class="[
              'px-3 py-1 rounded-full text-sm font-medium',
              product.in_stock 
                ? 'bg-green-100 text-green-800' 
                : 'bg-red-100 text-red-800'
            ]"
          >
            {{ product.in_stock ? 'In Stock' : 'Out of Stock' }}
          </span>
        </div>
        
        <div class="actions space-y-4">
          <div class="quantity-selector flex items-center gap-3">
            <label for="quantity" class="font-medium">Quantity:</label>
            <div class="flex items-center border rounded">
              <button 
                @click="decreaseQuantity"
                :disabled="quantity <= 1"
                class="px-3 py-1 hover:bg-gray-100 disabled:opacity-50"
              >
                -
              </button>
              <span class="px-4 py-1">{{ quantity }}</span>
              <button 
                @click="increaseQuantity"
                :disabled="quantity >= (product.max_quantity || 10)"
                class="px-3 py-1 hover:bg-gray-100 disabled:opacity-50"
              >
                +
              </button>
            </div>
          </div>
          
          <div class="buttons flex gap-3">
            <button 
              @click="addToCart"
              :disabled="!product.in_stock"
              class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
            >
              {{ product.in_stock ? 'Add to Cart' : 'Out of Stock' }}
            </button>
            <button 
              @click="toggleWishlist"
              class="px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
              :class="{ 'text-red-500 border-red-300': isWishlisted }"
            >
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

// State
const loading = ref(true);
const product = ref(null);
const quantity = ref(1);
const isWishlisted = ref(false);

// Mock product data
const mockProduct = {
  id: 1,
  name: 'Wireless Headphones Pro',
  description: 'Premium wireless headphones with active noise cancellation, superior sound quality, and 30-hour battery life. Perfect for music lovers and professionals.',
  price: 199.99,
  old_price: 249.99,
  image: '/images/headphones-main.jpg',
  gallery: [
    '/images/headphones-1.jpg',
    '/images/headphones-2.jpg',
    '/images/headphones-3.jpg',
    '/images/headphones-4.jpg'
  ],
  features: [
    'Active Noise Cancellation',
    '30-hour battery life',
    'Quick charge: 5 min = 3 hours',
    'Premium build quality',
    'Comfortable over-ear design',
    'Bluetooth 5.0 connectivity'
  ],
  in_stock: true,
  max_quantity: 5
};

// Methods
const loadProduct = async () => {
  loading.value = true;
  try {
    // TODO: Replace with real API call
    // const response = await fetch(`/api/products/${route.params.slug}`);
    // product.value = await response.json();
    
    // Mock delay
    await new Promise(resolve => setTimeout(resolve, 800));
    product.value = mockProduct;
  } catch (error) {
    console.error('Failed to load product:', error);
    product.value = null;
  } finally {
    loading.value = false;
  }
};

const increaseQuantity = () => {
  if (quantity.value < (product.value?.max_quantity || 10)) {
    quantity.value++;
  }
};

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
  }
};

const addToCart = () => {
  console.log('Adding to cart:', { product: product.value, quantity: quantity.value });
  alert(`Added ${quantity.value} x ${product.value.name} to cart!`);
};

const toggleWishlist = () => {
  isWishlisted.value = !isWishlisted.value;
  console.log('Wishlist toggled:', isWishlisted.value);
};

const selectImage = (image) => {
  product.value.image = image;
};

// Lifecycle
onMounted(() => {
  loadProduct();
});
</script>

<style scoped>
.product-detail {
  max-width: 1200px;
  margin: 0 auto;
}

.main-image img {
  transition: transform 0.3s ease;
}

.main-image:hover img {
  transform: scale(1.02);
}

.quantity-selector button:hover:not(:disabled) {
  background-color: #f3f4f6;
}

.buttons button {
  transition: all 0.2s ease;
}

.buttons button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>
