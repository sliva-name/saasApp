import { createApp } from 'vue'
import TenantApp from './components/TenantApp.vue'

import { createRouter, createWebHistory } from 'vue-router'
import ProductCatalog from './components/ProductCatalog.vue'
import ProductDetail from './components/ProductDetail.vue'
import CategoryPage from "./components/CategoryPage.vue";

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
    }
]

// Создаём роутер
const router = createRouter({
    history: createWebHistory(),
    routes,
})

// Создаём приложение
const app = createApp(TenantApp)

// Подключаем роутер
app.use(router)

// Монтируем в DOM
app.mount('#tenant-app')
