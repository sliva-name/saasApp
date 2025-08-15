<template>
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-secondary-200 shadow-soft">
        <nav aria-label="Primary" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <RouterLink to="/" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 bg-gradient-to-r from-primary-500 to-accent-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM5.94 5.5c.944-.945 2.56-.276 2.56 1.06V10l5.5-5.5a1.5 1.5 0 012.12 2.12L10 12.5l-4.06-4.06a1.5 1.5 0 012.12-2.12L10 10V6.56c0-1.336 1.616-2.005 2.56-1.06a8 8 0 11-11.12 0z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-bold text-xl text-secondary-900 group-hover:text-primary-600 transition-colors">SaaS Store</span>
                        <p class="text-xs text-secondary-500 -mt-1">Ваш бизнес онлайн</p>
                    </div>
                </RouterLink>

                <!-- Desktop menu -->
                <nav class="hidden lg:flex items-center space-x-8">
                    <RouterLink 
                        to="/" 
                        class="text-secondary-700 hover:text-primary-600 font-medium transition-colors duration-200 relative group"
                        active-class="text-primary-600"
                    >
                        Главная
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-primary-500 group-hover:w-full transition-all duration-200"></span>
                    </RouterLink>
                    
                    <DropdownMenu
                        label="Каталог"
                        :items="catalogMenu"
                        class="text-secondary-700 hover:text-primary-600 font-medium transition-colors duration-200"
                    />
                    
                    <RouterLink 
                        to="/about" 
                        class="text-secondary-700 hover:text-primary-600 font-medium transition-colors duration-200 relative group"
                        active-class="text-primary-600"
                    >
                        О нас
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-primary-500 group-hover:w-full transition-all duration-200"></span>
                    </RouterLink>
                    
                    <RouterLink 
                        to="/contact" 
                        class="text-secondary-700 hover:text-primary-600 font-medium transition-colors duration-200 relative group"
                        active-class="text-primary-600"
                    >
                        Контакты
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-primary-500 group-hover:w-full transition-all duration-200"></span>
                    </RouterLink>
                </nav>

                <!-- Mobile menu & search buttons -->
                <div class="flex lg:hidden items-center space-x-4">
                    <button
                        @click="isSearchOpen = true"
                        aria-label="Search"
                        class="text-secondary-700 hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-lg p-2 transition-colors"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>

                    <button
                        @click="isMobileMenuOpen = true"
                        aria-label="Open menu"
                        class="text-secondary-700 hover:text-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-lg p-2 transition-colors"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Account & Cart (desktop only) -->
                <div class="hidden lg:flex items-center space-x-6">
                    <RouterLink 
                        to="/account" 
                        class="text-secondary-700 hover:text-primary-600 flex items-center space-x-2 group transition-colors"
                    >
                        <div class="w-8 h-8 bg-secondary-100 rounded-full flex items-center justify-center group-hover:bg-primary-100 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.12a7.5 7.5 0 0 1 15 0 17.93 17.93 0 0 1-7.5 1.63 17.93 17.93 0 0 1-7.5-1.63Z" />
                            </svg>
                        </div>
                        <span class="font-medium">Аккаунт</span>
                    </RouterLink>

                    <div class="h-6 w-px bg-secondary-300"></div>

                    <RouterLink 
                        to="/cart" 
                        class="relative text-secondary-700 hover:text-primary-600 flex items-center space-x-2 group transition-colors"
                        aria-label="View cart"
                    >
                        <div class="w-8 h-8 bg-secondary-100 rounded-full flex items-center justify-center group-hover:bg-primary-100 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.12-2.3 2.1-4.68 2.924-7.13a60.11 60.11 0 0 0-16.54-1.84M7.5 14.25L5.1 5.27M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                            </svg>
                        </div>
                        <span class="font-medium">Корзина</span>
                        <span 
                            v-if="cartCount > 0"
                            class="absolute -top-2 -right-2 bg-accent-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium animate-bounce-gentle"
                        >
                            {{ cartCount }}
                        </span>
                    </RouterLink>
                </div>
            </div>
        </nav>

        <!-- Mobile Search Modal -->
        <transition name="fade">
            <div
                v-if="isSearchOpen"
                class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-start justify-center p-4"
                @click="isSearchOpen = false"
            >
                <div 
                    class="bg-white rounded-xl shadow-large w-full max-w-md mt-20 p-4"
                    @click.stop
                >
                    <div class="relative">
                        <input
                            type="text"
                            placeholder="Поиск товаров..."
                            class="w-full pl-10 pr-4 py-3 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            v-model="searchQuery"
                            @keyup.enter="performSearch"
                            ref="searchInput"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Mobile menu panel -->
        <transition name="slide">
            <div
                v-if="isMobileMenuOpen"
                class="fixed inset-0 z-50 bg-black bg-opacity-25 lg:hidden min-h-screen h-screen"
                @click="isMobileMenuOpen = false"
            >
                <div 
                    class="fixed inset-y-0 right-0 w-full bg-white shadow-large"
                    @click.stop
                >
                    <div class="flex items-center justify-between p-4 border-b border-secondary-200">
                        <h2 class="text-lg font-semibold text-secondary-900">Меню</h2>
                        <button
                            @click="isMobileMenuOpen = false"
                            class="text-secondary-400 hover:text-secondary-600 focus:outline-none"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="px-4 py-6 space-y-4 bg-white">
                        <RouterLink 
                            to="/" 
                            class="block text-secondary-700 hover:text-primary-600 font-medium transition-colors"
                            @click="isMobileMenuOpen = false"
                        >
                            Главная
                        </RouterLink>
                        
                        <RouterLink 
                            to="/catalog" 
                            class="block text-secondary-700 hover:text-primary-600 font-medium transition-colors"
                            @click="isMobileMenuOpen = false"
                        >
                            Каталог
                        </RouterLink>
                        
                        <RouterLink 
                            to="/about" 
                            class="block text-secondary-700 hover:text-primary-600 font-medium transition-colors"
                            @click="isMobileMenuOpen = false"
                        >
                            О нас
                        </RouterLink>
                        
                        <RouterLink 
                            to="/contact" 
                            class="block text-secondary-700 hover:text-primary-600 font-medium transition-colors"
                            @click="isMobileMenuOpen = false"
                        >
                            Контакты
                        </RouterLink>
                        
                        <div class="border-t border-secondary-200 pt-4">
                            <RouterLink 
                                to="/account" 
                                class="block text-secondary-700 hover:text-primary-600 font-medium transition-colors"
                                @click="isMobileMenuOpen = false"
                            >
                                Аккаунт
                            </RouterLink>
                            
                            <RouterLink 
                                to="/cart" 
                                class="block text-secondary-700 hover:text-primary-600 font-medium transition-colors mt-2"
                                @click="isMobileMenuOpen = false"
                            >
                                Корзина
                                <span v-if="cartCount > 0" class="ml-2 bg-accent-500 text-white text-xs rounded-full px-2 py-1">
                                    {{ cartCount }}
                                </span>
                            </RouterLink>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </header>
</template>

<script>
import { ref, onMounted, nextTick, watch } from 'vue'
import { RouterLink } from 'vue-router'
import DropdownMenu from './DropdownMenu.vue'

export default {
    components: {
        RouterLink,
        DropdownMenu
    },
    setup() {
        const isMobileMenuOpen = ref(false)
        const isSearchOpen = ref(false)
        const searchQuery = ref('')
        const searchInput = ref(null)
        const cartCount = ref(0) // В реальном приложении это будет из store
        // динамический импорт, чтобы избежать циклических зависимостей
        import('@/stores/cart')
            .then(({ useCart }) => {
                const { count } = useCart()
                cartCount.value = count.value
                watch(count, (val) => { cartCount.value = val }, { immediate: true })
            })
            .catch(() => {})

        const catalogMenu = [
            { name: 'Все товары', href: '/catalog' },
            { name: 'Новинки', href: '/catalog?new=true' },
            { name: 'Популярное', href: '/catalog?popular=true' },
            { name: 'Акции', href: '/catalog?sale=true' },
        ]

        const performSearch = () => {
            if (searchQuery.value.trim()) {
                // Здесь будет логика поиска
                console.log('Searching for:', searchQuery.value)
                isSearchOpen.value = false
            }
        }

        onMounted(() => {
            // Фокус на поиск при открытии модального окна
            if (isSearchOpen.value && searchInput.value) {
                nextTick(() => {
                    searchInput.value.focus()
                })
            }
        })

        return {
            isMobileMenuOpen,
            isSearchOpen,
            searchQuery,
            searchInput,
            cartCount,
            catalogMenu,
            performSearch
        }
    }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}

.slide-enter-from {
    transform: translateX(100%);
}

.slide-leave-to {
    transform: translateX(100%);
}
</style>
