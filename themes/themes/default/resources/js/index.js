/**
 * Default Theme - Entry Point
 * 
 * Этот файл является точкой входа для темы по умолчанию.
 * Здесь регистрируются все компоненты темы.
 */

// Импортируем основные компоненты
import TenantApp from './components/TenantApp.vue'
import Navigation from './components/Navigation.vue'
import ProductCatalog from './components/ProductCatalog.vue'
import ProductDetail from './components/ProductDetail.vue'
import ProductGrid from './components/ProductGrid.vue'
import ProductCard from './components/ProductCard.vue'
import SearchBar from './components/SearchBar.vue'
import FiltersSidebar from './components/FiltersSidebar.vue'

// Импортируем страницы
import AccountPage from './pages/AccountPage.vue'
import LoginPage from './pages/LoginPage.vue'
import RegisterPage from './pages/RegisterPage.vue'
import CartPage from './pages/CartPage.vue'
import CategoryPage from './pages/CategoryPage.vue'
import ForgotPasswordPage from './pages/ForgotPasswordPage.vue'
import ResetPasswordPage from './pages/ResetPasswordPage.vue'

// Импортируем стили
import './styles/theme.css'

/**
 * Карта компонентов темы
 * Ключ - название компонента, значение - Vue компонент
 */
export const components = {
  // Основные компоненты
  TenantApp,
  Navigation,
  ProductCatalog,
  ProductDetail,
  ProductGrid,
  ProductCard,
  SearchBar,
  FiltersSidebar,
  
  // Страницы
  AccountPage,
  LoginPage,
  RegisterPage,
  CartPage,
  CategoryPage,
  ForgotPasswordPage,
  ResetPasswordPage,
}

/**
 * Маршруты темы
 */
export const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('./pages/Home.vue'),
  },
  {
    path: '/products',
    name: 'Products',
    component: () => import('./pages/Products.vue'),
  },
  {
    path: '/catalog',
    name: 'Catalog',
    component: () => import('./pages/Products.vue'),
  },
  {
    path: '/product/:slug',
    name: 'ProductDetail',
    component: ProductDetail,
    props: true,
  },
  {
    path: '/category/:slug',
    name: 'CategoryPage',
    component: CategoryPage,
    props: true,
  },
  {
    path: '/about',
    name: 'About',
    component: () => import('./pages/About.vue'),
  },
  {
    path: '/contact',
    name: 'Contact',
    component: () => import('./pages/Contact.vue'),
  },
  {
    path: '/account',
    name: 'AccountPage',
    component: AccountPage,
  },
  {
    path: '/register',
    name: 'RegisterPage',
    component: RegisterPage,
  },
  {
    path: '/login',
    name: 'LoginPage',
    component: LoginPage,
  },
  {
    path: '/forgot-password',
    name: 'ForgotPasswordPage',
    component: ForgotPasswordPage,
  },
  {
    path: '/reset-password/:token',
    name: 'ResetPasswordPage',
    component: ResetPasswordPage,
    props: true,
  },
  {
    path: '/cart',
    name: 'CartPage',
    component: CartPage,
  },
]

/**
 * Хуки жизненного цикла темы
 */
export const hooks = {
  beforeMount(app) {
    // Код, выполняемый перед монтированием приложения
    console.log('[Default Theme] Initializing...')
  },
  
  mounted(app) {
    // Код, выполняемый после монтирования приложения
    console.log('[Default Theme] Mounted successfully')
  },
  
  beforeUnmount(app) {
    // Код, выполняемый перед размонтированием
    console.log('[Default Theme] Unmounting...')
  }
}

/**
 * Конфигурация темы
 */
export const config = {
  name: 'Default Theme',
  version: '1.0.0',
  author: 'SaaS E-commerce Team',
  features: [
    'product_catalog',
    'product_search',
    'shopping_cart',
    'user_authentication',
    'responsive_design',
    'basic_customization'
  ]
}

/**
 * Экспорт по умолчанию
 */
export default {
  components,
  routes,
  hooks,
  config
}
