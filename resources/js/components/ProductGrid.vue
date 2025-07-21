<template>
    <div class="flex flex-col">
        <div v-if="products.length === 0" class="text-center py-12 text-gray-500 text-lg">
            Нет товаров
        </div>

        <ul
            v-else
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"
        >
            <li
                v-for="product in products"
                :key="product.id"
                class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition"
            >
                <div class="p-4">
                    <img
                        :src="product.image_url || '/images/placeholder.png'"
                        :alt="product.name"
                        class="w-full h-40 object-cover rounded"
                    />
                    <h4 class="text-lg font-semibold text-gray-900 mb-1 truncate">{{ product.name }}</h4>
                    <p class="text-sm text-gray-600 mb-3 line-clamp-3">{{ product.description }}</p>
                    <p class="text-blue-600 font-bold text-md">Цена: {{ product.price }} ₽</p>
                </div>
            </li>
        </ul>

        <nav
            v-if="pagination.last_page > 1"
            class="flex flex-wrap justify-center items-center mt-10 gap-4"
            aria-label="Pagination"
        >
            <button
                :disabled="pagination.current_page === 1"
                @click="$emit('page-change', pagination.current_page - 1)"
                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
            >
                ← Назад
            </button>

            <span class="text-gray-600 text-sm font-medium">
                Страница {{ pagination.current_page }} из {{ pagination.last_page }}
            </span>

            <button
                :disabled="pagination.current_page === pagination.last_page"
                @click="$emit('page-change', pagination.current_page + 1)"
                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Вперёд →
            </button>
        </nav>
    </div>
</template>


<script>
export default {
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
}
</script>
