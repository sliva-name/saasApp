<template>
    <div class="p-4 space-y-6">
        <!-- Хлебные крошки -->
        <nav class="text-sm text-gray-500 space-x-1 mb-2" aria-label="breadcrumb">
            <RouterLink to="/" class="text-blue-600 hover:underline">Главная</RouterLink>
            <span>/</span>
            <span class="text-gray-800 font-medium">{{ category?.name || 'Категория' }}</span>
        </nav>

        <!-- Поиск -->
        <SearchBar @search="onSearch" />

        <!-- Заголовок -->
        <h1 class="text-xl font-semibold text-gray-900">
            {{ category?.name || 'Категория' }}
        </h1>

        <!-- Ошибка -->
        <div v-if="error" class="text-red-600 text-sm">{{ error }}</div>

        <!-- Загрузка -->
        <div v-else-if="loading" class="text-gray-500 text-sm animate-pulse">
            Загрузка товаров...
        </div>

        <!-- Список товаров -->
        <ProductGrid
            v-else
            :products="products"
            :pagination="pagination"
            @page-change="onPageChange"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import ProductGrid from '@/components/ProductGrid.vue'
import SearchBar from '@/components/SearchBar.vue'

const route = useRoute()
const slug = ref(route.params.slug)

const category = ref(null)
const products = ref([])
const pagination = ref({})
const loading = ref(true)
const error = ref(null)
const page = ref(1)
const query = ref('')

async function fetchCategoryProducts() {
    loading.value = true
    error.value = null
    try {
        const { data } = await axios.get(`/api/categories/${slug.value}`, {
            params: { page: page.value, query: query.value }
        })
        category.value = data.category
        products.value = data.products
        pagination.value = data.pagination
    } catch (e) {
        error.value = 'Не удалось загрузить товары этой категории'
    } finally {
        loading.value = false
    }
}

function onSearch(value) {
    query.value = value
    page.value = 1
    fetchCategoryProducts()
}

function onPageChange(newPage) {
    page.value = newPage
    fetchCategoryProducts()
}

onMounted(fetchCategoryProducts)

watch(() => route.params.slug, (newSlug) => {
    slug.value = newSlug
    page.value = 1
    query.value = ''
    fetchCategoryProducts()
})
</script>
