# Руководство по созданию тем

Подробное руководство по созданию кастомных тем для SaaS E-commerce платформы.

## 📋 Содержание

- [Требования](#требования)
- [Пошаговое создание](#пошаговое-создание)
- [Структура компонентов](#структура-компонентов)
- [Стилизация](#стилизация)
- [Конфигурация](#конфигурация)
- [Тестирование](#тестирование)
- [Публикация](#публикация)

## ✅ Требования

### Системные требования

- **PHP:** 8.2+
- **Laravel:** 11+
- **Vue.js:** 3.0+
- **Node.js:** 18+
- **Vite:** 4.0+

### Знания

- Базовые знания **Vue.js 3** (Composition API)
- Понимание **CSS/SCSS**
- Опыт работы с **Laravel**
- Знание **REST API**

## 🚀 Пошаговое создание

### Шаг 1: Создание структуры

```bash
# Используйте Artisan команду для создания базовой структуры
php artisan make:theme "My Awesome Theme"

# Или создайте вручную
mkdir -p themes/themes/my-awesome-theme/resources/js/{components,pages,composables,styles}
mkdir -p themes/themes/my-awesome-theme/resources/assets/{images,fonts}
```

### Шаг 2: Настройка манифеста

Создайте `themes/themes/my-awesome-theme/theme.json`:

```json
{
  "name": "My Awesome Theme",
  "package_name": "themes/my-awesome-theme",
  "version": "1.0.0",
  "description": "Современная тема с минималистичным дизайном",
  "author": "Your Name <your.email@example.com>",
  "license": "MIT",
  "features": [
    "products",
    "categories",
    "search",
    "cart",
    "checkout",
    "user-account",
    "wishlist"
  ],
  "config_schema": {
    "colors": {
      "primary": "#6366f1",
      "secondary": "#64748b", 
      "accent": "#f59e0b",
      "background": "#ffffff",
      "surface": "#f8fafc",
      "text": "#1e293b",
      "success": "#10b981",
      "warning": "#f59e0b", 
      "error": "#ef4444"
    },
    "layout": {
      "header_fixed": true,
      "sidebar_enabled": false,
      "footer_enabled": true,
      "max_width": "1200px",
      "container_padding": "1rem"
    },
    "typography": {
      "font_family": "Inter, sans-serif",
      "font_size_base": "16px",
      "font_size_small": "14px",
      "font_size_large": "18px",
      "line_height": "1.6"
    },
    "branding": {
      "site_name": "Demo Store",
      "description": "Your amazing online store",
      "logo": null,
      "favicon": null
    },
    "features": {
      "dark_mode": true,
      "animations": true,
      "lazy_loading": true,
      "infinite_scroll": false
    }
  },
  "dependencies": {
    "vue": "^3.0.0",
    "vue-router": "^4.0.0"
  },
  "preview_image": "/assets/preview.jpg",
  "screenshots": [
    "/assets/screenshot-1.jpg",
    "/assets/screenshot-2.jpg"
  ]
}
```

### Шаг 3: Основной компонент приложения

Создайте `themes/themes/my-awesome-theme/resources/js/components/TenantApp.vue`:

```vue
<template>
  <div class="theme-my-awesome">
    <Header />
    
    <main class="main-content">
      <router-view />
    </main>
    
    <Footer />
  </div>
</template>

<script>
import { onMounted } from 'vue'
import Header from './Header.vue'
import Footer from './Footer.vue'
import { useThemeConfig } from '../composables/useThemeConfig.js'

export default {
  name: 'TenantApp',
  components: {
    Header,
    Footer
  },
  setup() {
    const { themeConfig, applyThemeToBody } = useThemeConfig()
    
    onMounted(() => {
      // Применяем класс темы к body
      applyThemeToBody('theme-my-awesome')
      
      // Применяем CSS переменные из конфигурации
      const root = document.documentElement
      Object.entries(themeConfig.colors).forEach(([key, value]) => {
        root.style.setProperty(`--color-${key}`, value)
      })
    })
    
    return {
      themeConfig
    }
  }
}
</script>

<style scoped>
.theme-my-awesome {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: var(--color-background);
  color: var(--color-text);
  font-family: var(--font-family);
}

.main-content {
  flex: 1;
  padding: 2rem 0;
}
</style>
```

### Шаг 4: Композиция для конфигурации

Создайте `themes/themes/my-awesome-theme/resources/js/composables/useThemeConfig.js`:

```javascript
import { reactive, computed } from 'vue'

const themeConfig = reactive({
  colors: {
    primary: '#6366f1',
    secondary: '#64748b',
    accent: '#f59e0b',
    background: '#ffffff',
    surface: '#f8fafc',
    text: '#1e293b'
  },
  layout: {
    header_fixed: true,
    sidebar_enabled: false,
    footer_enabled: true,
    max_width: '1200px'
  },
  typography: {
    font_family: 'Inter, sans-serif',
    font_size_base: '16px',
    line_height: '1.6'
  },
  branding: {
    site_name: 'Demo Store',
    description: 'Your amazing online store',
    logo: null
  },
  features: {
    dark_mode: true,
    animations: true,
    lazy_loading: true
  }
})

export function useThemeConfig() {
  const updateConfig = (path, value) => {
    const keys = path.split('.')
    let current = themeConfig
    
    for (let i = 0; i < keys.length - 1; i++) {
      current = current[keys[i]]
    }
    
    current[keys[keys.length - 1]] = value
  }
  
  const resetConfig = () => {
    Object.assign(themeConfig, {
      // Сброс к дефолтным значениям
      colors: {
        primary: '#6366f1',
        secondary: '#64748b',
        accent: '#f59e0b',
        background: '#ffffff',
        surface: '#f8fafc',
        text: '#1e293b'
      }
      // ... остальная конфигурация
    })
  }
  
  const applyThemeToBody = (themeClass) => {
    // Удаляем предыдущие классы тем
    document.body.classList.forEach(className => {
      if (className.startsWith('theme-')) {
        document.body.classList.remove(className)
      }
    })
    
    // Добавляем новый класс темы
    document.body.classList.add(themeClass)
  }
  
  const isDarkMode = computed(() => themeConfig.features.dark_mode)
  
  return {
    themeConfig,
    updateConfig,
    resetConfig,
    applyThemeToBody,
    isDarkMode
  }
}
```

### Шаг 5: Компоненты интерфейса

#### Header.vue

```vue
<template>
  <header class="header" :class="{ 'header--fixed': themeConfig.layout.header_fixed }">
    <div class="header__container">
      <div class="header__logo">
        <router-link to="/">
          <img 
            v-if="themeConfig.branding.logo" 
            :src="themeConfig.branding.logo" 
            :alt="themeConfig.branding.site_name"
            class="header__logo-img"
          />
          <span v-else class="header__logo-text">
            {{ themeConfig.branding.site_name }}
          </span>
        </router-link>
      </div>
      
      <Navigation class="header__nav" />
      
      <div class="header__actions">
        <SearchBar />
        <CartButton />
        <UserMenu />
      </div>
    </div>
  </header>
</template>

<script>
import { useThemeConfig } from '../composables/useThemeConfig.js'
import Navigation from './Navigation.vue'
import SearchBar from './SearchBar.vue'
import CartButton from './CartButton.vue'
import UserMenu from './UserMenu.vue'

export default {
  name: 'Header',
  components: {
    Navigation,
    SearchBar,
    CartButton,
    UserMenu
  },
  setup() {
    const { themeConfig } = useThemeConfig()
    
    return {
      themeConfig
    }
  }
}
</script>

<style scoped>
.header {
  background: var(--color-surface);
  border-bottom: 1px solid var(--color-secondary);
  z-index: 1000;
}

.header--fixed {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
}

.header__container {
  max-width: var(--max-width, 1200px);
  margin: 0 auto;
  padding: 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.header__logo-text {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--color-primary);
  text-decoration: none;
}

.header__logo-img {
  height: 40px;
  width: auto;
}

.header__actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}
</style>
```

#### ProductCard.vue

```vue
<template>
  <div class="product-card" :class="{ 'product-card--loading': loading }">
    <div class="product-card__image">
      <router-link :to="`/products/${product.id}`">
        <img 
          :src="product.image || '/placeholder.jpg'" 
          :alt="product.name"
          class="product-card__img"
          loading="lazy"
        />
      </router-link>
      
      <div v-if="product.discount" class="product-card__badge">
        -{{ product.discount }}%
      </div>
      
      <div v-if="product.stock === 0" class="product-card__overlay">
        Нет в наличии
      </div>
    </div>
    
    <div class="product-card__content">
      <h3 class="product-card__title">
        <router-link :to="`/products/${product.id}`">
          {{ product.name }}
        </router-link>
      </h3>
      
      <p v-if="product.category" class="product-card__category">
        {{ product.category.name }}
      </p>
      
      <div class="product-card__price">
        <span v-if="product.discount" class="product-card__price-old">
          {{ formatPrice(product.price) }}
        </span>
        <span class="product-card__price-current">
          {{ formatPrice(product.discounted_price || product.price) }}
        </span>
      </div>
      
      <div v-if="product.stock > 0 && product.stock < 10" class="product-card__stock-warning">
        Осталось {{ product.stock }} шт.
      </div>
      
      <button 
        class="product-card__add-to-cart"
        :disabled="product.stock === 0"
        @click="addToCart"
      >
        {{ product.stock === 0 ? 'Нет в наличии' : 'В корзину' }}
      </button>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'

export default {
  name: 'ProductCard',
  props: {
    product: {
      type: Object,
      required: true
    }
  },
  emits: ['add-to-cart'],
  setup(props, { emit }) {
    const loading = ref(false)
    
    const formatPrice = (price) => {
      return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB'
      }).format(price)
    }
    
    const addToCart = async () => {
      loading.value = true
      try {
        emit('add-to-cart', props.product)
      } finally {
        loading.value = false
      }
    }
    
    return {
      loading,
      formatPrice,
      addToCart
    }
  }
}
</script>

<style scoped>
.product-card {
  background: var(--color-surface);
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  position: relative;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.product-card__image {
  position: relative;
  width: 100%;
  height: 240px;
  overflow: hidden;
}

.product-card__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-card__img {
  transform: scale(1.05);
}

.product-card__badge {
  position: absolute;
  top: 8px;
  right: 8px;
  background: var(--color-error);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
}

.product-card__overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
}

.product-card__content {
  padding: 1rem;
}

.product-card__title a {
  color: var(--color-text);
  text-decoration: none;
  font-weight: 600;
  line-height: 1.4;
}

.product-card__title a:hover {
  color: var(--color-primary);
}

.product-card__category {
  color: var(--color-secondary);
  font-size: 0.875rem;
  margin: 0.25rem 0;
}

.product-card__price {
  margin: 0.5rem 0;
}

.product-card__price-old {
  text-decoration: line-through;
  color: var(--color-secondary);
  margin-right: 0.5rem;
}

.product-card__price-current {
  font-weight: 700;
  color: var(--color-primary);
  font-size: 1.125rem;
}

.product-card__stock-warning {
  color: var(--color-warning);
  font-size: 0.75rem;
  margin: 0.25rem 0;
}

.product-card__add-to-cart {
  width: 100%;
  padding: 0.75rem;
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.product-card__add-to-cart:hover:not(:disabled) {
  background: var(--color-accent);
}

.product-card__add-to-cart:disabled {
  background: var(--color-secondary);
  cursor: not-allowed;
}

.product-card--loading {
  opacity: 0.7;
  pointer-events: none;
}
</style>
```

### Шаг 6: Страницы

#### Home.vue

```vue
<template>
  <div class="home">
    <Hero />
    
    <section class="featured-products">
      <div class="container">
        <h2 class="section-title">Рекомендуемые товары</h2>
        
        <div v-if="loading" class="loading">
          Загрузка...
        </div>
        
        <div v-else-if="featuredProducts.length" class="products-grid">
          <ProductCard 
            v-for="product in featuredProducts"
            :key="product.id"
            :product="product"
            @add-to-cart="handleAddToCart"
          />
        </div>
        
        <div v-else class="empty-state">
          Нет товаров для отображения
        </div>
      </div>
    </section>
    
    <Newsletter />
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import Hero from '../components/Hero.vue'
import ProductCard from '../components/ProductCard.vue'
import Newsletter from '../components/Newsletter.vue'

export default {
  name: 'Home',
  components: {
    Hero,
    ProductCard,
    Newsletter
  },
  setup() {
    const featuredProducts = ref([])
    const loading = ref(true)
    
    const loadFeaturedProducts = async () => {
      try {
        const response = await fetch('/api/products/popular')
        const data = await response.json()
        featuredProducts.value = data.data || []
      } catch (error) {
        console.error('Failed to load featured products:', error)
      } finally {
        loading.value = false
      }
    }
    
    const handleAddToCart = (product) => {
      // Логика добавления в корзину
      console.log('Adding to cart:', product)
    }
    
    onMounted(() => {
      loadFeaturedProducts()
    })
    
    return {
      featuredProducts,
      loading,
      handleAddToCart
    }
  }
}
</script>

<style scoped>
.section-title {
  font-size: 2rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 2rem;
  color: var(--color-text);
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.container {
  max-width: var(--max-width, 1200px);
  margin: 0 auto;
  padding: 0 1rem;
}

.featured-products {
  padding: 4rem 0;
}

.loading, .empty-state {
  text-align: center;
  padding: 2rem;
  color: var(--color-secondary);
}
</style>
```

### Шаг 7: Стили темы

Создайте `themes/themes/my-awesome-theme/resources/js/styles/theme.css`:

```css
/* Тема My Awesome Theme */

/* CSS переменные */
:root {
  --color-primary: #6366f1;
  --color-secondary: #64748b;
  --color-accent: #f59e0b;
  --color-background: #ffffff;
  --color-surface: #f8fafc;
  --color-text: #1e293b;
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-error: #ef4444;
  
  --font-family: 'Inter', sans-serif;
  --font-size-base: 16px;
  --line-height: 1.6;
  
  --border-radius: 8px;
  --max-width: 1200px;
  
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  
  --transition: all 0.2s ease;
}

/* Темная тема */
.theme-my-awesome.dark {
  --color-primary: #818cf8;
  --color-background: #0f172a;
  --color-surface: #1e293b;
  --color-text: #f1f5f9;
  --color-secondary: #94a3b8;
}

/* Базовые стили */
.theme-my-awesome {
  font-family: var(--font-family);
  font-size: var(--font-size-base);
  line-height: var(--line-height);
  color: var(--color-text);
  background-color: var(--color-background);
}

/* Утилиты */
.container {
  max-width: var(--max-width);
  margin: 0 auto;
  padding: 0 1rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: var(--border-radius);
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: var(--transition);
}

.btn--primary {
  background: var(--color-primary);
  color: white;
}

.btn--primary:hover {
  background: var(--color-accent);
}

.btn--secondary {
  background: var(--color-secondary);
  color: white;
}

.btn--outline {
  background: transparent;
  border: 2px solid var(--color-primary);
  color: var(--color-primary);
}

.btn--outline:hover {
  background: var(--color-primary);
  color: white;
}

/* Формы */
.form-group {
  margin-bottom: 1rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: var(--color-text);
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--color-secondary);
  border-radius: var(--border-radius);
  font-size: var(--font-size-base);
  transition: var(--transition);
}

.form-input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

/* Сетки */
.grid {
  display: grid;
  gap: 1.5rem;
}

.grid--2 {
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
}

.grid--3 {
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
}

.grid--4 {
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
}

/* Карточки */
.card {
  background: var(--color-surface);
  border-radius: var(--border-radius);
  padding: 1.5rem;
  box-shadow: var(--shadow-sm);
  transition: var(--transition);
}

.card:hover {
  box-shadow: var(--shadow-md);
}

/* Анимации */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slideIn {
  from { transform: translateX(-100%); }
  to { transform: translateX(0); }
}

.animate-fade-in {
  animation: fadeIn 0.6s ease-out;
}

.animate-slide-in {
  animation: slideIn 0.4s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
  .container {
    padding: 0 0.5rem;
  }
  
  .grid {
    gap: 1rem;
  }
  
  .btn {
    padding: 0.625rem 1.25rem;
    font-size: 0.875rem;
  }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}

/* Focus styles */
.theme-my-awesome *:focus {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

/* Печать */
@media print {
  .theme-my-awesome {
    background: white !important;
    color: black !important;
  }
  
  .btn, .header, .footer {
    display: none !important;
  }
}
```

### Шаг 8: Точка входа темы

Создайте `themes/themes/my-awesome-theme/resources/js/index.js`:

```javascript
// Импорт стилей
import './styles/theme.css'

// Компоненты
export const components = {
  TenantApp: () => import('./components/TenantApp.vue'),
  Header: () => import('./components/Header.vue'),
  Footer: () => import('./components/Footer.vue'),
  Navigation: () => import('./components/Navigation.vue'),
  ProductCard: () => import('./components/ProductCard.vue'),
  ProductCatalog: () => import('./components/ProductCatalog.vue'),
  ProductDetail: () => import('./components/ProductDetail.vue'),
  SearchBar: () => import('./components/SearchBar.vue'),
  FiltersSidebar: () => import('./components/FiltersSidebar.vue'),
  Hero: () => import('./components/Hero.vue'),
  Newsletter: () => import('./components/Newsletter.vue'),
  CartButton: () => import('./components/CartButton.vue'),
  UserMenu: () => import('./components/UserMenu.vue')
}

// Страницы
export const pages = {
  Home: () => import('./pages/Home.vue'),
  Products: () => import('./pages/Products.vue'),
  ProductDetail: () => import('./pages/ProductDetail.vue'),
  Category: () => import('./pages/Category.vue'),
  Cart: () => import('./pages/Cart.vue'),
  Checkout: () => import('./pages/Checkout.vue'),
  Account: () => import('./pages/Account.vue'),
  Login: () => import('./pages/Login.vue'),
  Register: () => import('./pages/Register.vue'),
  About: () => import('./pages/About.vue'),
  Contact: () => import('./pages/Contact.vue')
}

// Маршруты
export const routes = [
  { 
    path: '/', 
    component: pages.Home,
    name: 'home',
    meta: { title: 'Главная' }
  },
  { 
    path: '/products', 
    component: pages.Products,
    name: 'products',
    meta: { title: 'Товары' }
  },
  { 
    path: '/products/:id', 
    component: pages.ProductDetail,
    name: 'product-detail',
    meta: { title: 'Товар' }
  },
  { 
    path: '/category/:slug', 
    component: pages.Category,
    name: 'category',
    meta: { title: 'Категория' }
  },
  { 
    path: '/cart', 
    component: pages.Cart,
    name: 'cart',
    meta: { title: 'Корзина' }
  },
  { 
    path: '/checkout', 
    component: pages.Checkout,
    name: 'checkout',
    meta: { title: 'Оформление заказа', requiresAuth: true }
  },
  { 
    path: '/account', 
    component: pages.Account,
    name: 'account',
    meta: { title: 'Личный кабинет', requiresAuth: true }
  },
  { 
    path: '/login', 
    component: pages.Login,
    name: 'login',
    meta: { title: 'Вход' }
  },
  { 
    path: '/register', 
    component: pages.Register,
    name: 'register',
    meta: { title: 'Регистрация' }
  },
  { 
    path: '/about', 
    component: pages.About,
    name: 'about',
    meta: { title: 'О нас' }
  },
  { 
    path: '/contact', 
    component: pages.Contact,
    name: 'contact',
    meta: { title: 'Контакты' }
  }
]

// Хуки жизненного цикла
export const hooks = {
  beforeMount() {
    console.log('[My Awesome Theme] Initializing theme...')
    
    // Загружаем Google Fonts
    const link = document.createElement('link')
    link.href = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap'
    link.rel = 'stylesheet'
    document.head.appendChild(link)
  },
  
  mounted() {
    console.log('[My Awesome Theme] Theme mounted successfully')
    
    // Инициализируем аналитику, если нужно
    if (window.gtag) {
      window.gtag('config', 'GA_MEASUREMENT_ID')
    }
  },
  
  beforeUnmount() {
    console.log('[My Awesome Theme] Theme unmounting...')
    
    // Очистка ресурсов
    document.body.classList.remove('theme-my-awesome')
  }
}

// Конфигурация темы по умолчанию
export const defaultConfig = {
  colors: {
    primary: '#6366f1',
    secondary: '#64748b',
    accent: '#f59e0b',
    background: '#ffffff',
    surface: '#f8fafc',
    text: '#1e293b'
  },
  layout: {
    header_fixed: true,
    sidebar_enabled: false,
    footer_enabled: true,
    max_width: '1200px'
  },
  branding: {
    site_name: 'Demo Store',
    description: 'Your amazing online store'
  }
}
```

## 🎨 Стилизация

### Использование CSS переменных

```css
/* Определите переменные для всех цветов и размеров */
:root {
  --color-primary: #6366f1;
  --color-secondary: #64748b;
  /* ... */
}

/* Используйте переменные во всех компонентах */
.button {
  background: var(--color-primary);
  color: white;
}
```

### Темная тема

```css
.theme-my-awesome.dark {
  --color-background: #0f172a;
  --color-surface: #1e293b;
  --color-text: #f1f5f9;
}
```

### Адаптивность

```css
/* Mobile First подход */
.product-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}

@media (min-width: 640px) {
  .product-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .product-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}
```

## ⚙️ Конфигурация

### Схема конфигурации

Определите в `theme.json` полную схему настроек:

```json
{
  "config_schema": {
    "colors": {
      "primary": {
        "type": "color",
        "default": "#6366f1",
        "description": "Основной цвет темы"
      }
    },
    "layout": {
      "header_fixed": {
        "type": "boolean",
        "default": true,
        "description": "Фиксированный заголовок"
      }
    }
  }
}
```

### Использование в компонентах

```vue
<script setup>
import { useThemeConfig } from '../composables/useThemeConfig.js'

const { themeConfig } = useThemeConfig()

// Реактивное использование настроек
const headerClass = computed(() => ({
  'header--fixed': themeConfig.layout.header_fixed
}))
</script>
```

## 🧪 Тестирование

### Регистрация в системе

```php
// database/seeders/ThemeSeeder.php
Theme::create([
    'name' => 'My Awesome Theme',
    'package_name' => 'themes/my-awesome-theme',
    'description' => 'Современная тема с минималистичным дизайном',
    'version' => '1.0.0',
    'author' => 'Your Name',
    'is_active' => true
]);
```

### Тестирование

1. **Запустите сидер:**
```bash
php artisan db:seed --class=ThemeSeeder
```

2. **Назначьте тему магазину:**
```php
$store = Store::first();
$theme = Theme::where('package_name', 'themes/my-awesome-theme')->first();
$store->theme_id = $theme->id;
$store->save();
```

3. **Проверьте в браузере:**
- Откройте магазин
- Убедитесь что загружается ваша тема
- Проверьте стили и функциональность

## 📦 Публикация

### Создание preview

1. Сделайте скриншоты темы
2. Создайте preview изображение (1200x800px)
3. Добавьте в `theme.json`:

```json
{
  "preview_image": "/assets/preview.jpg",
  "screenshots": [
    "/assets/screenshot-home.jpg",
    "/assets/screenshot-product.jpg",
    "/assets/screenshot-cart.jpg"
  ]
}
```

### README темы

Создайте `themes/themes/my-awesome-theme/README.md`:

```markdown
# My Awesome Theme

Современная и минималистичная тема для интернет-магазинов.

## Особенности

- ✅ Адаптивный дизайн
- ✅ Темная тема
- ✅ Анимации
- ✅ SEO оптимизация
- ✅ Accessibility

## Установка

1. Скопируйте тему в `themes/themes/my-awesome-theme/`
2. Выполните `php artisan theme:manage scan`
3. Назначьте тему магазину в админ-панели

## Настройка

Тема поддерживает настройку цветов, шрифтов и макета через админ-панель.

## Поддержка

Для вопросов и поддержки обращайтесь: support@example.com
```

### Архивирование

```bash
# Создайте архив темы для распространения
cd themes/themes/
tar -czf my-awesome-theme-v1.0.0.tar.gz my-awesome-theme/
```

## 📝 Чек-лист готовности

- [ ] ✅ Все компоненты созданы и работают
- [ ] ✅ Стили адаптивны и поддерживают темную тему  
- [ ] ✅ Конфигурация реактивна
- [ ] ✅ Маршруты определены корректно
- [ ] ✅ Хуки жизненного цикла настроены
- [ ] ✅ Тема протестирована в браузере
- [ ] ✅ Создана документация
- [ ] ✅ Preview изображения подготовлены
- [ ] ✅ Тема зарегистрирована в системе

Теперь ваша тема готова к использованию! 🎉
