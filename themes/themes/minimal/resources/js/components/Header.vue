<template>
  <header class="site-header">
    <div class="header-container">
      <!-- Logo -->
      <div class="logo-section">
        <router-link to="/" class="logo-link">
          <img 
            v-if="branding.logo" 
            :src="branding.logo" 
            :alt="branding.site_name || 'Logo'"
            class="logo-img"
          />
          <span v-else class="logo-text">
            {{ branding.site_name || 'Store' }}
          </span>
        </router-link>
      </div>

      <!-- Search -->
      <div class="search-section">
        <div class="search-container">
          <input 
            v-model="searchQuery"
            @keyup.enter="handleSearch"
            type="text" 
            placeholder="Search products..."
            class="search-input"
          />
          <button @click="handleSearch" class="search-btn">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Actions -->
      <div class="actions-section">
        <!-- User Menu -->
        <div class="user-menu">
          <button v-if="!user" @click="showLogin = true" class="login-btn">
            Login
          </button>
          <div v-else class="user-dropdown">
            <button @click="toggleUserMenu" class="user-btn">
              <span>{{ user.name }}</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"></path>
              </svg>
            </button>
            <div v-show="showUserMenu" class="dropdown-menu">
              <router-link to="/profile" class="dropdown-item">Profile</router-link>
              <router-link to="/orders" class="dropdown-item">Orders</router-link>
              <button @click="logout" class="dropdown-item">Logout</button>
            </div>
          </div>
        </div>

        <!-- Cart -->
        <router-link to="/cart" class="cart-link">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
          </svg>
          <span v-if="cartCount > 0" class="cart-badge">{{ cartCount }}</span>
        </router-link>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useThemeConfig } from '../composables/useThemeConfig';

const router = useRouter();
const { config: themeConfig, branding } = useThemeConfig();

// State
const searchQuery = ref('');
const showUserMenu = ref(false);
const showLogin = ref(false);

// Mock user data (replace with real auth system)
const user = ref(null);
const cartCount = ref(0);

// Methods
const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'search', query: { q: searchQuery.value } });
  }
};

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value;
};

const logout = () => {
  user.value = null;
  showUserMenu.value = false;
  // Implement logout logic
};
</script>

<style scoped>
.site-header {
  @apply bg-white shadow-sm border-b;
  background-color: var(--color-background, #ffffff);
  border-color: var(--color-border, #e2e8f0);
}

.header-container {
  @apply max-w-7xl mx-auto px-4 sm:px-6 lg:px-8;
  @apply flex items-center justify-between h-16;
}

.logo-section {
  @apply flex-shrink-0;
}

.logo-link {
  @apply flex items-center;
}

.logo-img {
  @apply h-8 w-auto;
}

.logo-text {
  @apply text-xl font-bold;
  color: var(--color-primary, #3b82f6);
}

.search-section {
  @apply flex-1 max-w-lg mx-8;
}

.search-container {
  @apply relative;
}

.search-input {
  @apply w-full pl-4 pr-10 py-2 border rounded-lg;
  @apply focus:outline-none focus:ring-2 focus:ring-opacity-50;
  border-color: var(--color-border, #e2e8f0);
  color: var(--color-text-primary, #1a202c);
}

.search-input:focus {
  border-color: var(--color-primary, #3b82f6);
  box-shadow: 0 0 0 3px var(--color-primary-light, rgba(59, 130, 246, 0.1));
}

.search-btn {
  @apply absolute right-2 top-1/2 transform -translate-y-1/2;
  @apply p-1 text-gray-400 hover:text-gray-600;
}

.actions-section {
  @apply flex items-center space-x-4;
}

.login-btn {
  @apply px-4 py-2 rounded-md font-medium;
  background-color: var(--color-primary, #3b82f6);
  color: white;
}

.login-btn:hover {
  background-color: var(--color-primary-dark, #2563eb);
}

.user-dropdown {
  @apply relative;
}

.user-btn {
  @apply flex items-center space-x-1 px-3 py-2 rounded-md;
  @apply text-gray-700 hover:bg-gray-100;
}

.dropdown-menu {
  @apply absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg;
  @apply border border-gray-200 py-1 z-50;
}

.dropdown-item {
  @apply block w-full text-left px-4 py-2 text-sm;
  @apply text-gray-700 hover:bg-gray-100;
}

.cart-link {
  @apply relative p-2 text-gray-700 hover:text-gray-900;
}

.cart-badge {
  @apply absolute -top-1 -right-1 bg-red-500 text-white;
  @apply text-xs rounded-full h-5 w-5 flex items-center justify-center;
}
</style>
