import { createApp } from 'vue'
import TenantApp from './components/TenantApp.vue'

import { createRouter, createWebHistory } from 'vue-router'
import ProductCatalog from './components/ProductCatalog.vue'
import ProductDetail from './components/ProductDetail.vue'
import CategoryPage from "./pages/CategoryPage.vue";
import AccountPage from './pages/AccountPage.vue'
import RegisterPage from './pages/RegisterPage.vue'
import LoginPage from './pages/LoginPage.vue'
import ForgotPasswordPage from './pages/ForgotPasswordPage.vue'
import ResetPasswordPage from './pages/ResetPasswordPage.vue'
import CartPage from './pages/CartPage.vue'

// Определяем маршруты
const routes = [
    {
        path: '/',
        name: 'Catalog',
        component: ProductCatalog,
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
        props: true
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

// Создаём роутер
const router = createRouter({
    history: createWebHistory(),
    routes,
})

// Простая защита маршрута аккаунта: редирект на регистрацию, если не авторизован
router.beforeEach(async (to, from, next) => {
    if (to.name === 'AccountPage') {
        try {
            const res = await fetch('/api/me', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            if (res.status === 200) {
                return next()
            }
            return next('/register')
        } catch (e) {
            return next('/register')
        }
    }
    return next()
})

// Создаём приложение
const app = createApp(TenantApp)

// Подключаем роутер
app.use(router)

// Монтируем в DOM
app.mount('#tenant-app')
