import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import { useThemeLoader } from './themeLoader.js'
import ThemeComponent from './components/ThemeComponent.vue'

// Функция для инициализации приложения с темой
async function initializeApp() {
    const { loadTheme, getComponent, getRoutes, getCurrentThemeInfo } = useThemeLoader()
    
    // Получаем информацию об активной теме тенанта
    let activeTheme = null
    try {
        const response = await fetch('/api/tenant/active-theme')
        if (response.ok) {
            const themeData = await response.json()
            activeTheme = themeData.package_name || 'themes/default'
        }
    } catch (error) {
        console.warn('Failed to fetch active theme, using default:', error)
        activeTheme = 'themes/default'
    }
    
    // Загружаем тему
    let theme = null
    try {
        theme = await loadTheme(activeTheme)
        console.log(`[TenantApp] Loaded theme: ${activeTheme}`)
    } catch (error) {
        console.error(`[TenantApp] Failed to load theme ${activeTheme}, falling back to default:`, error)
        try {
            theme = await loadTheme('themes/default')
        } catch (fallbackError) {
            console.error('[TenantApp] Failed to load default theme:', fallbackError)
        }
    }
    
    // Определяем маршруты
    let routes = []
    
    if (theme) {
        // Используем маршруты из темы
        routes = getRoutes()
    } else {
        // Fallback маршруты
        const ProductCatalog = (await import('./components/ProductCatalog.vue')).default
        const ProductDetail = (await import('./components/ProductDetail.vue')).default
        const CategoryPage = (await import('./pages/CategoryPage.vue')).default
        const AccountPage = (await import('./pages/AccountPage.vue')).default
        const RegisterPage = (await import('./pages/RegisterPage.vue')).default
        const LoginPage = (await import('./pages/LoginPage.vue')).default
        const ForgotPasswordPage = (await import('./pages/ForgotPasswordPage.vue')).default
        const ResetPasswordPage = (await import('./pages/ResetPasswordPage.vue')).default
        const CartPage = (await import('./pages/CartPage.vue')).default
        
        routes = [
            { path: '/', name: 'Catalog', component: ProductCatalog },
            { path: '/product/:slug', name: 'ProductDetail', component: ProductDetail, props: true },
            { path: '/category/:slug', name: 'CategoryPage', component: CategoryPage, props: true },
            { path: '/account', name: 'AccountPage', component: AccountPage },
            { path: '/register', name: 'RegisterPage', component: RegisterPage },
            { path: '/login', name: 'LoginPage', component: LoginPage },
            { path: '/forgot-password', name: 'ForgotPasswordPage', component: ForgotPasswordPage },
            { path: '/reset-password/:token', name: 'ResetPasswordPage', component: ResetPasswordPage, props: true },
            { path: '/cart', name: 'CartPage', component: CartPage },
        ]
    }

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

    // Определяем главный компонент приложения
    let AppComponent = null
    if (theme) {
        AppComponent = getComponent('TenantApp')
    }
    
    if (!AppComponent) {
        // Fallback к стандартному компоненту
        AppComponent = (await import('./components/TenantApp.vue')).default
    }

    // Создаём приложение
    const app = createApp(AppComponent)

    // Регистрируем глобальные компоненты
    app.component('ThemeComponent', ThemeComponent)

    // Подключаем роутер
    app.use(router)

    // Выполняем хук beforeMount темы если есть
    if (theme?.hooks?.beforeMount) {
        await theme.hooks.beforeMount(app)
    }

    // Монтируем в DOM
    app.mount('#tenant-app')

    // Выполняем хук mounted темы если есть
    if (theme?.hooks?.mounted) {
        await theme.hooks.mounted(app)
    }

    return app
}

// Инициализируем приложение
initializeApp().catch(error => {
    console.error('[TenantApp] Failed to initialize application:', error)
    
    // Fallback инициализация без темы
    import('./components/TenantApp.vue').then(({ default: TenantApp }) => {
        const app = createApp(TenantApp)
        
        // Создаём базовые маршруты
        const router = createRouter({
            history: createWebHistory(),
            routes: [
                { path: '/', component: () => import('./components/ProductCatalog.vue') },
                { path: '/product/:slug', component: () => import('./components/ProductDetail.vue'), props: true },
                { path: '/category/:slug', component: () => import('./pages/CategoryPage.vue'), props: true },
                { path: '/account', component: () => import('./pages/AccountPage.vue') },
                { path: '/register', component: () => import('./pages/RegisterPage.vue') },
                { path: '/login', component: () => import('./pages/LoginPage.vue') },
                { path: '/forgot-password', component: () => import('./pages/ForgotPasswordPage.vue') },
                { path: '/reset-password/:token', component: () => import('./pages/ResetPasswordPage.vue'), props: true },
                { path: '/cart', component: () => import('./pages/CartPage.vue') },
            ]
        })
        
        app.use(router)
        app.mount('#tenant-app')
    })
})
