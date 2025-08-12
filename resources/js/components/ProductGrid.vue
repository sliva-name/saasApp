<template>
    <div class="flex flex-col">
        <!-- Empty State -->
        <div v-if="products.length === 0" class="text-center py-16">
            <div class="w-24 h-24 mx-auto mb-6 bg-secondary-100 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-secondary-900 mb-2">Товары не найдены</h3>
            <p class="text-secondary-600 mb-6">Попробуйте изменить параметры поиска или фильтры</p>
            <button
                @click="$emit('reset-filters')"
                class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Сбросить фильтры
            </button>
        </div>

        <!-- Products Grid -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div
                v-for="(product, index) in products"
                :key="product.id"
                class="group bg-white rounded-xl overflow-hidden shadow-soft hover:shadow-medium transition-all duration-300 transform hover:-translate-y-1 flex flex-col"
                :style="{ animationDelay: `${index * 0.1}s` }"
            >
                <RouterLink
                    :to="`/product/${product.slug}`"
                    class="block flex-1 hover:no-underline text-inherit flex flex-col"
                >
                    <!-- Product Image -->
                    <div class="relative aspect-square overflow-hidden bg-secondary-100">
                        <img
                            :src="product.image_url || '/images/placeholder.png'"
                            :alt="product.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            loading="lazy"
                        />

                        <!-- Quick Actions Overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex space-x-2">
                                <button
                                    @click.prevent="addToWishlist(product)"
                                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-medium hover:shadow-large transition-all duration-200 transform hover:scale-110"
                                    :class="{ 'text-accent-500': isInWishlist(product.id) }"
                                >
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                </button>

                                <button
                                    @click.prevent="quickView(product)"
                                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-medium hover:shadow-large transition-all duration-200 transform hover:scale-110"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Sale Badge -->
                        <div v-if="product.sale_price" class="absolute top-3 left-3">
                            <span class="bg-accent-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                -{{ Math.round(((product.price - product.sale_price) / product.price) * 100) }}%
                            </span>
                        </div>

                        <!-- New Badge -->
                        <div v-if="product.is_new" class="absolute top-3 right-3">
                            <span class="bg-primary-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                Новинка
                            </span>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="font-semibold text-secondary-900 mb-2 line-clamp-2 group-hover:text-primary-600 transition-colors">
                            {{ product.name }}
                        </h3>

                        <p class="text-sm text-secondary-600 mb-3 line-clamp-2 flex-1">
                            {{ product.description }}
                        </p>

                        <!-- Price -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <span v-if="product.sale_price" class="text-lg font-bold text-accent-600">
                                    {{ formatPrice(product.sale_price) }}
                                </span>
                                <span class="text-lg font-bold text-secondary-900" :class="{ 'line-through text-secondary-400': product.sale_price }">
                                    {{ formatPrice(product.price) }}
                                </span>
                            </div>

                            <!-- Rating -->
                            <div v-if="product.rating" class="flex items-center space-x-1">
                                <div class="flex items-center">
                                    <svg
                                        v-for="star in 5"
                                        :key="star"
                                        class="w-4 h-4"
                                        :class="star <= product.rating ? 'text-yellow-400' : 'text-secondary-300'"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-secondary-500">({{ product.reviews_count || 0 }})</span>
                            </div>
                        </div>
                    </div>
                </RouterLink>

                <!-- Add to Cart Button -->
                <div class="p-4 pt-0">
                    <button
                        @click="addToCart(product)"
                        class="w-full bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700 transition-colors duration-200 flex items-center justify-center space-x-2 group-hover:shadow-medium"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
                        </svg>
                        <span>В корзину</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.current_page < pagination.last_page" class="flex justify-center mt-8">
            <button
                @click="$emit('load-more')"
                class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50"
            >
                Показать ещё
            </button>
        </div>
    </div>
</template>

<script>
import { computed } from 'vue'
import { useCart } from '@/stores/cart'
import { RouterLink } from 'vue-router'

export default {
    components: {
        RouterLink
    },
    props: {
        products: {
            type: Array,
            required: true,
        },
        pagination: {
            type: Object,
            required: true,
        },
    },
    emits: ['page-change', 'reset-filters'],
    setup(props) {
        const visiblePages = computed(() => {
            const current = props.pagination.current_page
            const last = props.pagination.last_page
            const delta = 2

            let start = Math.max(1, current - delta)
            let end = Math.min(last, current + delta)

            if (end - start < 4) {
                if (start === 1) {
                    end = Math.min(last, start + 4)
                } else {
                    start = Math.max(1, end - 4)
                }
            }

            const pages = []
            for (let i = start; i <= end; i++) {
                pages.push(i)
            }

            return pages
        })

        const formatPrice = (price) => {
            return new Intl.NumberFormat('ru-RU', {
                style: 'currency',
                currency: 'RUB',
                minimumFractionDigits: 0
            }).format(price)
        }

        const { addItem } = useCart()
        const addToCart = (product) => {
            addItem(product, 1)
        }

        const addToWishlist = (product) => {
            // Здесь будет логика добавления в избранное
            console.log('Adding to wishlist:', product)
        }

        const quickView = (product) => {
            // Здесь будет логика быстрого просмотра
            console.log('Quick view:', product)
        }

        const isInWishlist = (productId) => {
            // Здесь будет проверка, есть ли товар в избранном
            return false
        }

        return {
            visiblePages,
            formatPrice,
            addToCart,
            addToWishlist,
            quickView,
            isInWishlist
        }
    }
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Анимация появления карточек */
.grid > div {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
    transform: translateY(20px);
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
