<template>
    <aside class="w-full lg:w-64 p-4 bg-white border border-gray-200 rounded-xl shadow-md">
        <h3 class="text-xl font-semibold mb-6 text-gray-900">Фильтры</h3>

        <div class="space-y-5">
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Категория</label>
                <select
                    v-model="selectedCategory"
                    @change="applyFilters"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Все</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                        {{ cat.name }}
                    </option>
                </select>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Цена от</label>
                <input
                    type="number"
                    v-model.number="priceMin"
                    @input="applyFilters"
                    min="0"
                    placeholder="Мин."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Цена до</label>
                <input
                    type="number"
                    v-model.number="priceMax"
                    @input="applyFilters"
                    min="0"
                    placeholder="Макс."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                />
            </div>
        </div>
    </aside>
</template>


<script>
export default {
    props: {
        categories: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            selectedCategory: '',
            priceMin: null,
            priceMax: null,
        }
    },
    methods: {
        applyFilters() {
            this.$emit('filter-change', {
                category_id: this.selectedCategory || null,
                price_min: this.priceMin,
                price_max: this.priceMax,
            })
        },
    },
}
</script>
