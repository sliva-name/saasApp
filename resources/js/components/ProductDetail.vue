<template>
    <div class="p-4 space-y-6">
        <!-- Назад -->
        <button @click="$router.back()" class="text-sm text-blue-600 hover:underline">
            ← Назад
        </button>
        <nav class="text-sm text-gray-500 space-x-1 mb-4" aria-label="breadcrumb">
            <RouterLink to="/" class="hover:underline text-blue-600">Главная</RouterLink>
            <span>/</span>

            <template v-if="product?.category_name">
                <RouterLink
                    v-if="product?.category_slug"
                    :to="`/category/${product.category_slug}`"
                    class="hover:underline text-blue-600"
                >
                    {{ product.category_name }}
                </RouterLink>
                <span v-else class="text-gray-600">{{ product.category_name }}</span>
                <span>/</span>
            </template>

            <span class="text-gray-800 font-medium">{{ product?.name || '...' }}</span>
        </nav>


        <!-- Ошибка -->
        <div v-if="error" class="text-center text-red-600 text-sm">
            {{ error }}
        </div>

        <!-- Загрузка -->
        <div v-else-if="loading" class="text-center text-gray-500 text-sm animate-pulse">
            Загрузка товара...
        </div>

        <!-- Контент -->
        <div v-else-if="product" class="flex flex-col md:flex-row gap-6 items-start">
            <!-- Изображение и галерея -->
            <div class="md:w-1/2 space-y-3">
                <img
                    :src="product.image_url || '/images/placeholder.png'"
                    :alt="product.name"
                    class="w-full rounded-md border object-cover max-h-80"
                />

                <!-- Миниатюры, если есть media -->
                <div v-if="product.media?.length > 1" class="flex gap-2 overflow-x-auto">
                    <img
                        v-for="media in product.media"
                        :key="media.id"
                        :src="media.original_url"
                        @click="product.image_url = media.original_url"
                        class="w-16 h-16 object-cover rounded border cursor-pointer hover:ring ring-blue-400 transition"
                    />
                </div>
            </div>

            <!-- Информация о товаре -->
            <div class="md:flex-1 space-y-4">
                <h1 class="text-xl font-semibold text-gray-900">{{ product.name }}</h1>

                <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">
                    {{ product.description }}
                </p>

                <div class="text-base font-medium text-gray-800">
                    Категория: <span class="text-gray-600">{{ product.category_name || '—' }}</span>
                </div>

                <div class="text-lg font-semibold text-blue-600">
                    Цена: {{ formatPrice(product.price) }} ₽
                </div>

                <button
                    @click="addToCart"
                    :disabled="isAdding"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md disabled:opacity-50"
                >
                    {{ isAdding ? 'Добавляем...' : 'В корзину' }}
                </button>
            </div>
        </div>

        <!-- Если пусто -->
        <div v-else class="text-center text-gray-500 text-sm">Товар не найден</div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const slug = route.params.slug

const product = ref(null)
const loading = ref(true)
const error = ref(null)
const isAdding = ref(false)

function formatPrice(value) {
    return Number(value).toLocaleString('ru-RU', { minimumFractionDigits: 2 })
}

async function fetchProduct() {
    loading.value = true
    error.value = null
    try {
        const { data } = await axios.get(`/api/products/${slug}`)
        product.value = data.data || data
        // fallback: если image_url отсутствует, использовать media[0]
        if (!product.value.image_url && product.value.media?.length) {
            product.value.image_url = product.value.media[0].original_url
        }
    } catch (e) {
        error.value = 'Не удалось загрузить товар.'
        product.value = null
    } finally {
        loading.value = false
    }
}

async function addToCart() {
    if (!product.value) return
    isAdding.value = true
    try {
        // Здесь может быть реальная логика добавления
        alert(`Товар "${product.value.name}" добавлен в корзину`)
    } catch {
        alert('Ошибка при добавлении')
    } finally {
        isAdding.value = false
    }
}

onMounted(fetchProduct)
</script>
