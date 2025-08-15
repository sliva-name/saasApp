<template>
    <div class="space-y-8">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-primary-600 to-accent-600 rounded-2xl p-8 text-white">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl font-bold mb-4">Найдите то, что ищете</h1>
                <p class="text-xl opacity-90 mb-8">Широкий ассортимент товаров для ваших потребностей</p>
                <div class="max-w-2xl mx-auto">
                    <SearchBar
                        :initial-query="query" @search="onSearch"
                        placeholder="Поиск товаров..."
                        class="w-full"
                    />
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-primary-500 border-t-transparent"></div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
            <div class="w-24 h-24 mx-auto mb-6 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-secondary-900 mb-2">Ошибка загрузки</h3>
            <p class="text-secondary-600 mb-6">{{ error }}</p>
            <button
                @click="fetchProducts"
                class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Попробовать снова
            </button>
        </div>

        <!-- Content -->
        <div v-else class="flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <aside class="lg:w-1/4">
                <FiltersSidebar
                    :categories="categories"
                    v-model:selectedCategoriesProp="selectedCategories"
                    v-model:selectedAvailabilityProp="selectedAvailability"
                    v-model:priceRange="priceRange"
                    @filter-change="handleFilterChange"
                />
            </aside>

            <!-- Main Content -->
            <main class="lg:w-3/4">
                <!-- Results Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-secondary-900">
                            {{ resultsTitle }}
                        </h2>
                        <p class="text-secondary-600">
                            Найдено {{ pagination.total || 0 }} товаров
                        </p>
                    </div>

                    <!-- Sort Options -->
                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-secondary-700">Сортировка:</label>
                        <select
                            v-model="sortBy"
                            @change="onSortChange"
                            class="px-3 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                        >
                            <option value="relevance">По релевантности</option>
                            <option value="price_asc">По цене (возрастание)</option>
                            <option value="price_desc">По цене (убывание)</option>
                            <option value="name_asc">По названию (А-Я)</option>
                            <option value="name_desc">По названию (Я-А)</option>
                            <option value="newest">Сначала новые</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <ProductGrid
                    v-if="!loading"
                    :products="products"
                    :pagination="pagination"
                    @load-more="loadMore"
                    @reset-filters="resetFilters"
                />
                <div v-else>Загрузка...</div>
            </main>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import SearchBar from './SearchBar.vue'
import FiltersSidebar from './FiltersSidebar.vue'
import ProductGrid from './ProductGrid.vue'

export default {
    components: { SearchBar, FiltersSidebar, ProductGrid },

    setup() {
        const query = ref(localStorage.getItem('query') || '')
        const filters = ref(JSON.parse(localStorage.getItem('filters') || '{}'))
        const sortBy = ref(localStorage.getItem('sortBy') || 'relevance')
        const page = ref(parseInt(localStorage.getItem('currentPage')) || 1)
        const priceRange = ref({min: null, max: null})
        const products = ref([])
        const selectedCategories = ref([])
        const selectedAvailability = ref([])
        const pagination = ref({})
        const categories = ref([])
        const brands = ref([])
        const loading = ref(false)
        const error = ref(null)

        const resultsTitle = computed(() => {
            return query.value ? `Результаты поиска: "${query.value}"` : 'Все товары'
        })

        // Автосохранение состояния
        watch([query, filters, sortBy, page], () => {
            localStorage.setItem('query', query.value)
            localStorage.setItem('filters', JSON.stringify(filters.value))
            localStorage.setItem('sortBy', sortBy.value)
            localStorage.setItem('currentPage', page.value)
        }, { deep: true })

        const fetchProducts = async (append = false) => {
            loading.value = true
            error.value = null

            try {
                const params = {
                    query: query.value,
                    page: page.value,
                    sort: sortBy.value,
                    ...filters.value
                }

                const { data } = await axios.get('/api/products', { params })

                if (append) {
                    products.value = [...products.value, ...(data.products || [])]
                } else {
                    products.value = data.products || []
                }

                pagination.value = data.pagination || {}
                categories.value = data.categories || []
                brands.value = data.brands || []

            } catch (err) {
                console.error('Error fetching products:', err)
                error.value = 'Не удалось загрузить товары. Попробуйте позже.'
                if (!append) {
                    products.value = []
                    pagination.value = {}
                }
            } finally {
                loading.value = false
            }
        }

        const initialLoad = async () => {
            // Загружаем первую страницу
            await fetchProducts(false)

            // Если есть вторая страница — догружаем
            if (pagination.value.current_page < pagination.value.last_page) {
                page.value++
                await fetchProducts(true)
            }
        }

        const onSearch = (searchQuery) => {
            query.value = searchQuery
            page.value = 1
            fetchProducts(false)
        }

        const handleFilterChange = (newFilters) => {
            filters.value = newFilters
            page.value = 1
            fetchProducts(false)
        }

        const loadMore = () => {
            if (page.value < pagination.value.last_page) {
                page.value++
                fetchProducts(true)
            }
        }

        const onSortChange = () => {
            page.value = 1
            fetchProducts(false)
        }

        const resetFilters = () => {
            // Сбрасываем состояние фильтров
            filters.value = {}
            query.value = ''
            page.value = 1
            sortBy.value = 'relevance'

            // Сбрасываем значения, которые связаны с FiltersSidebar через v-model
            selectedCategories.value = []
            selectedAvailability.value = []
            priceRange.value = { min: null, max: null }

            fetchProducts(false)
        }

        onMounted(initialLoad)

        return {
            query,
            filters,
            page,
            products,
            pagination,
            categories,
            brands,
            loading,
            error,
            sortBy,
            resultsTitle,
            selectedCategories,
            selectedAvailability,
            priceRange,
            fetchProducts,
            onSearch,
            handleFilterChange,
            loadMore,
            onSortChange,
            resetFilters
        }
    }
}

</script>
