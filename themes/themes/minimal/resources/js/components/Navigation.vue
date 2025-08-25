<template>
  <nav class="main-navigation">
    <div class="nav-container">
      <!-- Desktop Navigation -->
      <div class="desktop-nav">
        <ul class="nav-list">
          <li class="nav-item">
            <router-link to="/" class="nav-link" :class="{ active: $route.path === '/' }">
              Home
            </router-link>
          </li>
          <li class="nav-item dropdown" @mouseover="showDropdown = 'products'" @mouseleave="showDropdown = null">
            <router-link to="/products" class="nav-link" :class="{ active: $route.path.startsWith('/products') }">
              Products
              <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"></path>
              </svg>
            </router-link>
            <!-- Products Dropdown -->
            <div v-show="showDropdown === 'products'" class="dropdown-menu">
              <div class="dropdown-content">
                <div class="dropdown-section">
                  <h3 class="dropdown-title">Categories</h3>
                  <ul class="dropdown-links">
                    <li v-for="category in categories" :key="category.id">
                      <router-link :to="`/category/${category.slug}`" class="dropdown-link">
                        {{ category.name }}
                      </router-link>
                    </li>
                  </ul>
                </div>
                <div class="dropdown-section">
                  <h3 class="dropdown-title">Featured</h3>
                  <ul class="dropdown-links">
                    <li><router-link to="/products/new" class="dropdown-link">New Arrivals</router-link></li>
                    <li><router-link to="/products/bestsellers" class="dropdown-link">Best Sellers</router-link></li>
                    <li><router-link to="/products/sale" class="dropdown-link">Sale Items</router-link></li>
                  </ul>
                </div>
              </div>
            </div>
          </li>
          <li class="nav-item">
            <router-link to="/categories" class="nav-link" :class="{ active: $route.path === '/categories' }">
              Categories
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/deals" class="nav-link" :class="{ active: $route.path === '/deals' }">
              Deals
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/about" class="nav-link" :class="{ active: $route.path === '/about' }">
              About
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/contact" class="nav-link" :class="{ active: $route.path === '/contact' }">
              Contact
            </router-link>
          </li>
        </ul>
      </div>

      <!-- Mobile Menu Button -->
      <div class="mobile-menu-btn">
        <button @click="toggleMobileMenu" class="menu-toggle">
          <svg v-if="!showMobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
          <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Navigation -->
    <div v-show="showMobileMenu" class="mobile-nav">
      <ul class="mobile-nav-list">
        <li class="mobile-nav-item">
          <router-link to="/" @click="closeMobileMenu" class="mobile-nav-link">
            Home
          </router-link>
        </li>
        <li class="mobile-nav-item">
          <button @click="toggleMobileDropdown('products')" class="mobile-nav-link dropdown-toggle">
            Products
            <svg class="dropdown-icon" :class="{ 'rotate-180': showMobileDropdown === 'products' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"></path>
            </svg>
          </button>
          <div v-show="showMobileDropdown === 'products'" class="mobile-dropdown">
            <router-link to="/products" @click="closeMobileMenu" class="mobile-dropdown-link">
              All Products
            </router-link>
            <div class="mobile-dropdown-section">
              <h4 class="mobile-dropdown-title">Categories</h4>
              <router-link 
                v-for="category in categories" 
                :key="category.id"
                :to="`/category/${category.slug}`" 
                @click="closeMobileMenu"
                class="mobile-dropdown-link"
              >
                {{ category.name }}
              </router-link>
            </div>
          </div>
        </li>
        <li class="mobile-nav-item">
          <router-link to="/categories" @click="closeMobileMenu" class="mobile-nav-link">
            Categories
          </router-link>
        </li>
        <li class="mobile-nav-item">
          <router-link to="/deals" @click="closeMobileMenu" class="mobile-nav-link">
            Deals
          </router-link>
        </li>
        <li class="mobile-nav-item">
          <router-link to="/about" @click="closeMobileMenu" class="mobile-nav-link">
            About
          </router-link>
        </li>
        <li class="mobile-nav-item">
          <router-link to="/contact" @click="closeMobileMenu" class="mobile-nav-link">
            Contact
          </router-link>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useThemeConfig } from '../composables/useThemeConfig';

const { themeConfig } = useThemeConfig();

// State
const showDropdown = ref(null);
const showMobileMenu = ref(false);
const showMobileDropdown = ref(null);

// Mock categories data (replace with real API call)
const categories = ref([
  { id: 1, name: 'Electronics', slug: 'electronics' },
  { id: 2, name: 'Clothing', slug: 'clothing' },
  { id: 3, name: 'Home & Garden', slug: 'home-garden' },
  { id: 4, name: 'Sports', slug: 'sports' },
  { id: 5, name: 'Books', slug: 'books' }
]);

// Methods
const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value;
  if (!showMobileMenu.value) {
    showMobileDropdown.value = null;
  }
};

const closeMobileMenu = () => {
  showMobileMenu.value = false;
  showMobileDropdown.value = null;
};

const toggleMobileDropdown = (menu) => {
  showMobileDropdown.value = showMobileDropdown.value === menu ? null : menu;
};

// Load categories on mount
onMounted(async () => {
  try {
    // TODO: Fetch categories from API
    // const response = await fetch('/api/categories');
    // categories.value = await response.json();
  } catch (error) {
    console.error('Failed to load categories:', error);
  }
});
</script>

<style scoped>
.main-navigation {
  @apply bg-white border-b;
  background-color: var(--color-nav-bg, #ffffff);
  border-color: var(--color-border, #e2e8f0);
}

.nav-container {
  @apply max-w-7xl mx-auto px-4 sm:px-6 lg:px-8;
}

/* Desktop Navigation */
.desktop-nav {
  @apply hidden md:block;
}

.nav-list {
  @apply flex space-x-8;
}

.nav-item {
  @apply relative;
}

.nav-link {
  @apply flex items-center px-3 py-4 text-sm font-medium transition-colors;
  color: var(--color-nav-link, #374151);
}

.nav-link:hover,
.nav-link.active {
  color: var(--color-primary, #3b82f6);
}

.dropdown-icon {
  @apply w-4 h-4 ml-1;
}

/* Dropdown Menu */
.dropdown {
  @apply relative;
}

.dropdown-menu {
  @apply absolute top-full left-0 mt-1 w-96 bg-white rounded-lg shadow-lg border z-50;
  border-color: var(--color-border, #e2e8f0);
}

.dropdown-content {
  @apply p-6 grid grid-cols-2 gap-6;
}

.dropdown-section {
  @apply space-y-3;
}

.dropdown-title {
  @apply text-sm font-semibold text-gray-900 mb-3;
}

.dropdown-links {
  @apply space-y-2;
}

.dropdown-link {
  @apply block text-sm text-gray-600 hover:text-gray-900 transition-colors;
}

/* Mobile Menu */
.mobile-menu-btn {
  @apply md:hidden flex items-center justify-end py-4;
}

.menu-toggle {
  @apply p-2 text-gray-600 hover:text-gray-900;
}

.mobile-nav {
  @apply md:hidden bg-white border-t;
  border-color: var(--color-border, #e2e8f0);
}

.mobile-nav-list {
  @apply py-4 space-y-1;
}

.mobile-nav-item {
  @apply px-4;
}

.mobile-nav-link {
  @apply flex items-center justify-between py-3 text-base font-medium;
  color: var(--color-nav-link, #374151);
}

.mobile-nav-link:hover {
  color: var(--color-primary, #3b82f6);
}

.dropdown-toggle {
  @apply w-full text-left;
}

.mobile-dropdown {
  @apply mt-2 pl-4 space-y-2;
}

.mobile-dropdown-section {
  @apply mt-4 pt-4 border-t border-gray-200;
}

.mobile-dropdown-title {
  @apply text-sm font-semibold text-gray-900 mb-2;
}

.mobile-dropdown-link {
  @apply block py-2 text-sm text-gray-600;
}

.rotate-180 {
  transform: rotate(180deg);
}
</style>
