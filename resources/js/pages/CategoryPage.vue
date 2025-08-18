<template>
    <div class="space-y-8">
        <!-- Breadcrumbs -->
        <nav class="flex items-center space-x-2 text-sm text-secondary-600" aria-label="breadcrumb">
            <RouterLink to="/" class="text-primary-600 hover:text-primary-700 transition-colors">
                Главная
            </RouterLink>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-secondary-900 font-medium">{{ category?.name || 'Категория' }}</span>
        </nav>

        <!-- Category Header -->
        <div v-if="category" class="bg-gradient-to-r from-secondary-50 to-primary-50 rounded-2xl p-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-secondary-900 mb-4">{{ category.name }}</h1>
                <p v-if="category.description" class="text-lg text-secondary-600 max-w-2xl mx-auto">
                    {{ category.description }}
                </p>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto">
            <SearchBar 
                @search="onSearch" 
                :initial-query="query"
                placeholder="Поиск в категории..."
            />
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
                @click="fetchCategoryProducts"
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
                    :products="products"
                    :pagination="pagination"
                    @page-change="onPageChange"
                    @reset-filters="resetFilters"
                />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import ProductGrid from '../components/ProductGrid.vue'
import SearchBar from '../components/SearchBar.vue'
import FiltersSidebar from '../components/FiltersSidebar.vue'

const route = useRoute()

const category = ref(null)
const products = ref([])
const pagination = ref({})
const categories = ref([])
const loading = ref(true)
const error = ref(null)
const page = ref(1)
const query = ref('')
const filters = ref({})
const sortBy = ref('relevance')

const resultsTitle = computed(() => {
    if (query.value) {
        return `Результаты поиска: "${query.value}"`
    }
    return category.value?.name || 'Товары категории'
})

const fetchCategoryProducts = async () => {
    loading.value = true
    error.value = null
    try {
        const params = {
            page: page.value,
            sort: sortBy.value,
            ...filters.value
        }
        const { data } = await axios.get(`/api/categories/${route.params.slug}`, { params })
        category.value = data.category
        products.value = data.products || []
        pagination.value = data.pagination || {}
        categories.value = data.categories || []
    } catch (err) {
        console.error('Error fetching category products:', err)
        error.value = 'Не удалось загрузить товары категории. Попробуйте позже.'
        products.value = []
        pagination.value = {}
    } finally {
        loading.value = false
    }
}

const onSearch = (searchQuery) => {
    query.value = searchQuery
    page.value = 1
    fetchCategoryProducts()
}

const handleFilterChange = (newFilters) => {
    filters.value = newFilters
    page.value = 1
    fetchCategoryProducts()
}

const onPageChange = (newPage) => {
    page.value = newPage
    fetchCategoryProducts()
}

const onSortChange = (newSort) => {
    sortBy.value = newSort
    page.value = 1
    fetchCategoryProducts()
}

const resetFilters = () => {
    filters.value = {}
    sortBy.value = 'relevance'
    page.value = 1
    fetchCategoryProducts()
}

// Следим за изменением slug
watch(() => route.params.slug, () => {
    page.value = 1
    filters.value = {}
    sortBy.value = 'relevance'
    fetchCategoryProducts()
})

onMounted(() => {
    fetchCategoryProducts()
})
</script>
