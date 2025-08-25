<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
      <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-6xl font-bold mb-6">
          Welcome to {{ branding.site_name || 'Our Store' }}
        </h1>
        <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
          {{ branding.description || 'Discover amazing products at unbeatable prices' }}
        </p>
        <div class="space-x-4">
          <router-link 
            to="/products" 
            class="btn-primary bg-white text-blue-600 hover:bg-gray-100 px-8 py-3 rounded-lg text-lg font-semibold inline-block transition-colors"
          >
            Shop Now
          </router-link>
          <router-link 
            to="/about" 
            class="btn-secondary border-2 border-white text-white hover:bg-white hover:text-blue-600 px-8 py-3 rounded-lg text-lg font-semibold inline-block transition-colors"
          >
            Learn More
          </router-link>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-16 bg-gray-50">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Why Choose Us?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="feature-card text-center p-6">
            <div class="feature-icon bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">Fast Delivery</h3>
            <p class="text-gray-600">Get your orders delivered quickly and safely to your doorstep.</p>
          </div>
          
          <div class="feature-card text-center p-6">
            <div class="feature-icon bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">Quality Guarantee</h3>
            <p class="text-gray-600">All our products come with a satisfaction guarantee.</p>
          </div>
          
          <div class="feature-card text-center p-6">
            <div class="feature-icon bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM12 18a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V18.75A.75.75 0 0112 18z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">24/7 Support</h3>
            <p class="text-gray-600">Our customer support team is here to help you anytime.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products py-16">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Featured Products</h2>
        
        <div v-if="loading" class="text-center py-8">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="mt-4 text-gray-600">Loading featured products...</p>
        </div>
        
        <div v-else-if="featuredProducts.length === 0" class="text-center py-8">
          <p class="text-gray-500">No featured products available at the moment.</p>
        </div>
        
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <ProductCard 
            v-for="product in featuredProducts" 
            :key="product.id"
            :product="product"
            @add-to-cart="addToCart"
          />
        </div>
        
        <div class="text-center mt-8">
          <router-link 
            to="/products" 
            class="btn-primary bg-blue-600 text-white hover:bg-blue-700 px-6 py-3 rounded-lg text-lg font-semibold inline-block transition-colors"
          >
            View All Products
          </router-link>
        </div>
      </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section bg-gray-900 text-white py-16">
      <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Stay Updated</h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">
          Subscribe to our newsletter and be the first to know about new products and exclusive offers.
        </p>
        <form @submit.prevent="subscribeNewsletter" class="max-w-md mx-auto">
          <div class="flex">
            <input 
              v-model="email"
              type="email" 
              placeholder="Enter your email"
              class="flex-1 px-4 py-3 rounded-l-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            />
            <button 
              type="submit"
              class="bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-r-lg font-semibold transition-colors"
            >
              Subscribe
            </button>
          </div>
        </form>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useThemeConfig } from '../composables/useThemeConfig';
import ProductCard from '../components/ProductCard.vue';

const { config: themeConfig, branding } = useThemeConfig();

// State
const email = ref('');
const featuredProducts = ref([]);

// Loading state
const loading = ref(false);

// Methods
const addToCart = (product) => {
  console.log('Adding to cart:', product);
  // TODO: Implement cart functionality
  alert(`Added ${product.name} to cart!`);
};

const subscribeNewsletter = () => {
  console.log('Newsletter subscription:', email.value);
  // TODO: Implement newsletter subscription
  alert('Thank you for subscribing!');
  email.value = '';
};

// Load featured products from API
const loadFeaturedProducts = async () => {
  loading.value = true;
  try {
    const response = await fetch('/api/products/popular?limit=4');
    
    if (response.ok) {
      const data = await response.json();
      featuredProducts.value = data.data || [];
    } else {
      console.error('Failed to load featured products');
      featuredProducts.value = [];
    }
  } catch (error) {
    console.error('Error loading featured products:', error);
    featuredProducts.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadFeaturedProducts();
});
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, var(--color-primary, #3b82f6) 0%, var(--color-secondary, #8b5cf6) 100%);
}

.feature-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-secondary:hover {
  transform: translateY(-1px);
}

@media (max-width: 768px) {
  .hero-section h1 {
    font-size: 2.5rem;
  }
  
  .hero-section p {
    font-size: 1.125rem;
  }
}
</style>
