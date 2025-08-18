# Frontend Documentation

## 🎨 Обзор фронтенда

Фронтенд приложения построен на Vue.js 3 с использованием современного стека технологий:

- **Vue.js 3** - основной фреймворк
- **Vue Router 4** - маршрутизация
- **Tailwind CSS** - стилизация
- **Alpine.js** - интерактивность
- **Vite** - сборщик

## 📁 Структура фронтенда

```
resources/js/
├── app.js                 # Точка входа приложения
├── bootstrap.js           # Инициализация
├── tenant.js              # Тенантское SPA приложение
├── components/            # Vue компоненты
│   ├── TenantApp.vue      # Основной компонент SPA
│   ├── ProductCatalog.vue # Каталог товаров
│   ├── ProductDetail.vue  # Детали товара
│   ├── ProductGrid.vue    # Сетка товаров
│   ├── SearchBar.vue      # Поисковая строка
│   ├── FiltersSidebar.vue # Фильтры
│   ├── CartPage.vue       # Корзина
│   ├── AccountPage.vue    # Личный кабинет
│   ├── LoginPage.vue      # Страница входа
│   ├── RegisterPage.vue   # Страница регистрации
│   ├── ForgotPasswordPage.vue # Восстановление пароля
│   └── ResetPasswordPage.vue  # Сброс пароля
├── pages/                 # Компоненты страниц
│   ├── Navigation.vue     # Навигация
│   ├── DropdownMenu.vue   # Выпадающее меню
│   ├── MobileMenu.vue     # Мобильное меню
│   └── MobileDropdownMenu.vue # Мобильное выпадающее меню
└── stores/                # Состояние приложения
    └── cart.js            # Состояние корзины
```

## 🚀 Инициализация приложения

### Тенантское SPA (`tenant.js`)

```javascript
import { createApp } from 'vue'
import TenantApp from './components/TenantApp.vue'
import { createRouter, createWebHistory } from 'vue-router'

// Определение маршрутов
const routes = [
    { path: '/', component: ProductCatalog },
    { path: '/product/:slug', component: ProductDetail },
    { path: '/category/:slug', component: CategoryPage },
    { path: '/account', component: AccountPage },
    { path: '/cart', component: CartPage },
    // ... другие маршруты
]

// Создание роутера
const router = createRouter({
    history: createWebHistory(),
    routes,
})

// Защита маршрутов
router.beforeEach(async (to, from, next) => {
    if (to.name === 'AccountPage') {
        // Проверка авторизации
        const res = await fetch('/api/me')
        if (res.status === 200) {
            return next()
        }
        return next('/register')
    }
    return next()
})

// Создание и монтирование приложения
const app = createApp(TenantApp)
app.use(router)
app.mount('#tenant-app')
```

## 🧩 Основные компоненты

### TenantApp.vue

Главный компонент SPA приложения:

```vue
<template>
  <div class="min-h-screen bg-gradient-to-br from-secondary-50 to-secondary-100">
    <!-- Навигация -->
    <Navigation />
    
    <!-- Основной контент -->
    <main class="container mx-auto px-4 py-8">
      <router-view />
    </main>
    
    <!-- Футер -->
    <footer class="bg-secondary-900 text-white py-8">
      <!-- Содержимое футера -->
    </footer>
  </div>
</template>

<script setup>
import Navigation from './pages/Navigation.vue'
</script>
```

### ProductCatalog.vue

Каталог товаров с поиском и фильтрацией:

```vue
<template>
  <div class="flex gap-8">
    <!-- Фильтры -->
    <FiltersSidebar 
      :categories="categories"
      :filters="filters"
      @update-filters="updateFilters"
    />
    
    <!-- Основной контент -->
    <div class="flex-1">
      <!-- Поисковая строка -->
      <SearchBar @search="handleSearch" />
      
      <!-- Сетка товаров -->
      <ProductGrid 
        :products="products"
        :loading="loading"
        :pagination="pagination"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import SearchBar from './SearchBar.vue'
import FiltersSidebar from './FiltersSidebar.vue'
import ProductGrid from './ProductGrid.vue'

const route = useRoute()
const products = ref([])
const categories = ref([])
const filters = ref({})
const loading = ref(false)
const pagination = ref({})

// Загрузка данных
const loadProducts = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams({
      ...filters.value,
      page: route.query.page || 1
    })
    
    const response = await fetch(`/api/products?${params}`)
    const data = await response.json()
    
    products.value = data.data
    pagination.value = data.pagination
  } catch (error) {
    console.error('Error loading products:', error)
  } finally {
    loading.value = false
  }
}

// Обработчики событий
const handleSearch = (query) => {
  filters.value.q = query
  loadProducts()
}

const updateFilters = (newFilters) => {
  filters.value = { ...filters.value, ...newFilters }
  loadProducts()
}

// Наблюдение за изменениями маршрута
watch(() => route.query, loadProducts, { immediate: true })

onMounted(() => {
  loadCategories()
})
</script>
```

### SearchBar.vue

Поисковая строка с автодополнением:

```vue
<template>
  <div class="relative">
    <div class="relative">
      <input
        v-model="query"
        @input="handleInput"
        @keydown="handleKeydown"
        @focus="showSuggestions = true"
        @blur="hideSuggestions"
        type="text"
        placeholder="Поиск товаров..."
        class="w-full px-4 py-3 pl-12 pr-4 text-secondary-900 placeholder-secondary-400 bg-white border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
      />
      
      <!-- Иконка поиска -->
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>
      
      <!-- Индикатор загрузки -->
      <div v-if="loading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary-600"></div>
      </div>
    </div>
    
    <!-- Подсказки -->
    <div v-if="showSuggestions && suggestions.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-secondary-300 rounded-lg shadow-lg">
      <ul>
        <li
          v-for="(suggestion, index) in suggestions"
          :key="index"
          @click="selectSuggestion(suggestion)"
          @mouseenter="selectedIndex = index"
          :class="[
            'px-4 py-2 cursor-pointer hover:bg-secondary-50',
            selectedIndex === index ? 'bg-secondary-50' : ''
          ]"
        >
          {{ suggestion }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const emit = defineEmits(['search'])

const query = ref('')
const suggestions = ref([])
const loading = ref(false)
const showSuggestions = ref(false)
const selectedIndex = ref(-1)

// Debounce для поиска
let searchTimeout

const handleInput = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    if (query.value.length >= 2) {
      loadSuggestions()
    } else {
      suggestions.value = []
    }
  }, 300)
}

const loadSuggestions = async () => {
  loading.value = true
  try {
    const response = await fetch(`/search/suggest?q=${encodeURIComponent(query.value)}`)
    const data = await response.json()
    suggestions.value = data.suggestions || []
  } catch (error) {
    console.error('Error loading suggestions:', error)
  } finally {
    loading.value = false
  }
}

const selectSuggestion = (suggestion) => {
  query.value = suggestion
  showSuggestions.value = false
  emit('search', suggestion)
}

const hideSuggestions = () => {
  setTimeout(() => {
    showSuggestions.value = false
  }, 200)
}

const handleKeydown = (event) => {
  if (event.key === 'ArrowDown') {
    event.preventDefault()
    selectedIndex.value = Math.min(selectedIndex.value + 1, suggestions.value.length - 1)
  } else if (event.key === 'ArrowUp') {
    event.preventDefault()
    selectedIndex.value = Math.max(selectedIndex.value - 1, -1)
  } else if (event.key === 'Enter') {
    event.preventDefault()
    if (selectedIndex.value >= 0) {
      selectSuggestion(suggestions.value[selectedIndex.value])
    } else {
      emit('search', query.value)
    }
  } else if (event.key === 'Escape') {
    showSuggestions.value = false
  }
}

// Сброс при изменении query
watch(query, () => {
  selectedIndex.value = -1
})
</script>
```

### FiltersSidebar.vue

Боковая панель с фильтрами:

```vue
<template>
  <aside class="w-80 bg-white rounded-lg shadow-soft p-6 h-fit">
    <h3 class="text-lg font-semibold text-secondary-900 mb-6">Фильтры</h3>
    
    <!-- Категории -->
    <div class="mb-6">
      <h4 class="font-medium text-secondary-700 mb-3">Категории</h4>
      <div class="space-y-2">
        <label
          v-for="category in categories"
          :key="category.id"
          class="flex items-center cursor-pointer"
        >
          <input
            type="checkbox"
            :value="category.id"
            v-model="selectedCategories"
            @change="updateFilters"
            class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
          />
          <span class="ml-2 text-secondary-700">{{ category.name }}</span>
        </label>
      </div>
    </div>
    
    <!-- Цена -->
    <div class="mb-6">
      <h4 class="font-medium text-secondary-700 mb-3">Цена</h4>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm text-secondary-600 mb-1">От</label>
          <input
            v-model.number="priceRange.min"
            @input="updateFilters"
            type="number"
            min="0"
            class="w-full px-3 py-2 border border-secondary-300 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent"
          />
        </div>
        <div>
          <label class="block text-sm text-secondary-600 mb-1">До</label>
          <input
            v-model.number="priceRange.max"
            @input="updateFilters"
            type="number"
            min="0"
            class="w-full px-3 py-2 border border-secondary-300 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent"
          />
        </div>
      </div>
    </div>
    
    <!-- Наличие -->
    <div class="mb-6">
      <h4 class="font-medium text-secondary-700 mb-3">Наличие</h4>
      <label class="flex items-center cursor-pointer">
        <input
          type="checkbox"
          v-model="inStock"
          @change="updateFilters"
          class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
        />
        <span class="ml-2 text-secondary-700">В наличии</span>
      </label>
    </div>
    
    <!-- Сброс фильтров -->
    <button
      @click="resetFilters"
      class="w-full px-4 py-2 text-secondary-600 border border-secondary-300 rounded-md hover:bg-secondary-50 transition-colors"
    >
      Сбросить фильтры
    </button>
  </aside>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  categories: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['update-filters'])

const selectedCategories = ref([])
const priceRange = ref({ min: null, max: null })
const inStock = ref(false)

// Синхронизация с внешними фильтрами
watch(() => props.filters, (newFilters) => {
  selectedCategories.value = newFilters.categories || []
  priceRange.value = {
    min: newFilters.price_min || null,
    max: newFilters.price_max || null
  }
  inStock.value = newFilters.in_stock || false
}, { immediate: true })

const updateFilters = () => {
  const filters = {}
  
  if (selectedCategories.value.length > 0) {
    filters.categories = selectedCategories.value
  }
  
  if (priceRange.value.min !== null) {
    filters.price_min = priceRange.value.min
  }
  
  if (priceRange.value.max !== null) {
    filters.price_max = priceRange.value.max
  }
  
  if (inStock.value) {
    filters.in_stock = true
  }
  
  emit('update-filters', filters)
}

const resetFilters = () => {
  selectedCategories.value = []
  priceRange.value = { min: null, max: null }
  inStock.value = false
  updateFilters()
}
</script>
```

## 🎨 Стилизация

### Tailwind CSS конфигурация

```javascript
// tailwind.config.js
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
        },
        secondary: {
          50: '#f8fafc',
          100: '#f1f5f9',
          300: '#cbd5e1',
          400: '#94a3b8',
          600: '#475569',
          700: '#334155',
          900: '#0f172a',
        },
        accent: {
          500: '#ec4899',
          600: '#db2777',
        }
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-up': 'slideUp 0.3s ease-out',
        'scale-in': 'scaleIn 0.2s ease-out',
        'bounce-gentle': 'bounceGentle 0.6s ease-in-out',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        scaleIn: {
          '0%': { transform: 'scale(0.95)', opacity: '0' },
          '100%': { transform: 'scale(1)', opacity: '1' },
        },
        bounceGentle: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-5px)' },
        }
      },
      boxShadow: {
        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
        'medium': '0 4px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
        'large': '0 10px 40px -10px rgba(0, 0, 0, 0.15), 0 2px 10px -2px rgba(0, 0, 0, 0.05)',
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/aspect-ratio'),
    require('@tailwindcss/typography'),
  ],
}
```

### Кастомные CSS классы

```css
/* resources/css/app.css */

@tailwind base;
@tailwind components;
@tailwind utilities;

@layer components {
  .btn-primary {
    @apply px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors;
  }
  
  .btn-secondary {
    @apply px-4 py-2 bg-secondary-100 text-secondary-700 rounded-lg hover:bg-secondary-200 focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 transition-colors;
  }
  
  .card {
    @apply bg-white rounded-lg shadow-soft border border-secondary-200;
  }
  
  .input-field {
    @apply w-full px-3 py-2 border border-secondary-300 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent;
  }
}

@layer utilities {
  .text-gradient {
    @apply bg-gradient-to-r from-primary-600 to-accent-600 bg-clip-text text-transparent;
  }
  
  .bg-gradient-primary {
    @apply bg-gradient-to-r from-primary-500 to-accent-500;
  }
}
```

## 🔄 Состояние приложения

### Корзина (cart.js)

```javascript
// resources/js/stores/cart.js
import { reactive, computed } from 'vue'

const cart = reactive({
  items: [],
  loading: false
})

export const useCart = () => {
  const addToCart = (product, quantity = 1) => {
    const existingItem = cart.items.find(item => item.id === product.id)
    
    if (existingItem) {
      existingItem.quantity += quantity
    } else {
      cart.items.push({
        id: product.id,
        name: product.name,
        price: product.price,
        image: product.image_url,
        quantity
      })
    }
    
    // Сохранение в localStorage
    localStorage.setItem('cart', JSON.stringify(cart.items))
  }
  
  const removeFromCart = (productId) => {
    const index = cart.items.findIndex(item => item.id === productId)
    if (index > -1) {
      cart.items.splice(index, 1)
      localStorage.setItem('cart', JSON.stringify(cart.items))
    }
  }
  
  const updateQuantity = (productId, quantity) => {
    const item = cart.items.find(item => item.id === productId)
    if (item) {
      item.quantity = Math.max(1, quantity)
      localStorage.setItem('cart', JSON.stringify(cart.items))
    }
  }
  
  const clearCart = () => {
    cart.items = []
    localStorage.removeItem('cart')
  }
  
  const loadCart = () => {
    const saved = localStorage.getItem('cart')
    if (saved) {
      cart.items = JSON.parse(saved)
    }
  }
  
  const totalItems = computed(() => {
    return cart.items.reduce((sum, item) => sum + item.quantity, 0)
  })
  
  const totalPrice = computed(() => {
    return cart.items.reduce((sum, item) => sum + (item.price * item.quantity), 0)
  })
  
  return {
    cart,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    loadCart,
    totalItems,
    totalPrice
  }
}
```

## 📱 Адаптивность

### Breakpoints

```css
/* Мобильные устройства */
@media (max-width: 640px) {
  .container {
    @apply px-4;
  }
}

/* Планшеты */
@media (min-width: 641px) and (max-width: 1024px) {
  .container {
    @apply px-6;
  }
}

/* Десктопы */
@media (min-width: 1025px) {
  .container {
    @apply px-8;
  }
}
```

### Мобильная навигация

```vue
<template>
  <div class="lg:hidden">
    <!-- Мобильное меню -->
    <MobileMenu 
      :is-open="isMobileMenuOpen"
      @close="isMobileMenuOpen = false"
    />
    
    <!-- Кнопка меню -->
    <button
      @click="isMobileMenuOpen = true"
      class="p-2 text-secondary-600 hover:text-secondary-900"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
  </div>
</template>
```

## 🚀 Оптимизация производительности

### Ленивая загрузка компонентов

```javascript
// Ленивая загрузка маршрутов
const routes = [
  {
    path: '/',
    component: () => import('./components/ProductCatalog.vue')
  },
  {
    path: '/product/:slug',
    component: () => import('./components/ProductDetail.vue')
  }
]
```

### Виртуализация списков

```vue
<template>
  <div class="product-grid">
    <div
      v-for="product in visibleProducts"
      :key="product.id"
      class="product-card"
    >
      <ProductCard :product="product" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  products: Array
})

const scrollTop = ref(0)
const containerHeight = ref(0)

const visibleProducts = computed(() => {
  // Логика виртуализации
  return props.products.slice(0, 20) // Показываем только первые 20
})

const handleScroll = () => {
  scrollTop.value = window.scrollY
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  containerHeight.value = window.innerHeight
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>
```

## 🧪 Тестирование

### Unit тесты для компонентов

```javascript
// tests/Unit/Components/ProductCard.test.js
import { mount } from '@vue/test-utils'
import ProductCard from '@/components/ProductCard.vue'

describe('ProductCard', () => {
  it('renders product information correctly', () => {
    const product = {
      id: 1,
      name: 'Test Product',
      price: 100,
      image_url: '/test-image.jpg'
    }
    
    const wrapper = mount(ProductCard, {
      props: { product }
    })
    
    expect(wrapper.text()).toContain('Test Product')
    expect(wrapper.text()).toContain('100')
  })
  
  it('emits add-to-cart event when button is clicked', async () => {
    const wrapper = mount(ProductCard, {
      props: { product: { id: 1, name: 'Test' } }
    })
    
    await wrapper.find('.add-to-cart-btn').trigger('click')
    
    expect(wrapper.emitted('add-to-cart')).toBeTruthy()
  })
})
```

## 🔧 Инструменты разработки

### Vite конфигурация

```javascript
// vite.config.js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/tenant.js'],
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  resolve: {
    alias: {
      '@': '/resources/js',
    },
  },
  server: {
    hmr: {
      host: 'localhost',
    },
  },
})
```

### ESLint и Prettier

```javascript
// .eslintrc.js
module.exports = {
  extends: [
    '@vue/eslint-config-prettier',
    'plugin:vue/vue3-essential'
  ],
  rules: {
    'vue/multi-word-component-names': 'off',
    'vue/no-unused-vars': 'error'
  }
}
```

```json
// .prettierrc
{
  "semi": false,
  "singleQuote": true,
  "tabWidth": 2,
  "trailingComma": "es5"
}
```

## 📚 Дополнительные ресурсы

- [Vue.js 3 Documentation](https://vuejs.org/guide/)
- [Vue Router 4 Documentation](https://router.vuejs.org/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Vite Documentation](https://vitejs.dev/guide/)
- [Vue Test Utils](https://test-utils.vuejs.org/)

