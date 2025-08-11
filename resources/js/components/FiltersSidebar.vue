<template>
    <div class="bg-white rounded-xl shadow-soft border border-secondary-200 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-secondary-900">Фильтры</h2>
            <button
                @click="clearAllFilters"
                class="text-sm text-primary-600 hover:text-primary-700 transition-colors"
                :disabled="!hasActiveFilters"
            >
                Сбросить все
            </button>
        </div>

        <!-- Search within filters -->
        <div class="mb-6">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Поиск в фильтрах..."
                class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
            />
        </div>

        <!-- Filter Sections -->
        <div class="space-y-6">
            <!-- Categories Filter -->
            <div class="filter-section">
                <button
                    @click="toggleSection('categories')"
                    class="flex items-center justify-between w-full text-left mb-3"
                >
                    <h3 class="font-medium text-secondary-900">Категории</h3>
                    <svg 
                        class="w-4 h-4 text-secondary-400 transition-transform duration-200" 
                        :class="{ 'rotate-180': openSections.categories }"
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 max-h-0"
                    enter-to-class="opacity-100 max-h-96"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 max-h-96"
                    leave-to-class="opacity-0 max-h-0"
                >
                    <div v-if="openSections.categories" class="space-y-2 overflow-hidden">
                        <label
                            v-for="category in filteredCategories"
                            :key="category.id"
                            class="flex items-center space-x-3 cursor-pointer group"
                        >
                            <input
                                type="checkbox"
                                :value="category.id"
                                v-model="selectedCategories"
                                @change="applyFilters"
                                class="w-4 h-4 text-primary-600 border-secondary-300 rounded focus:ring-primary-500"
                            />
                            <span class="text-sm text-secondary-700 group-hover:text-secondary-900 transition-colors">
                                {{ category.name }}
                            </span>
                            <span class="text-xs text-secondary-400">({{ category.count }})</span>
                        </label>
                    </div>
                </transition>
            </div>

            <!-- Price Range Filter -->
            <div class="filter-section">
                <button
                    @click="toggleSection('price')"
                    class="flex items-center justify-between w-full text-left mb-3"
                >
                    <h3 class="font-medium text-secondary-900">Цена</h3>
                    <svg 
                        class="w-4 h-4 text-secondary-400 transition-transform duration-200" 
                        :class="{ 'rotate-180': openSections.price }"
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 max-h-0"
                    enter-to-class="opacity-100 max-h-96"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 max-h-96"
                    leave-to-class="opacity-0 max-h-0"
                >
                    <div v-if="openSections.price" class="space-y-4 overflow-hidden">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-secondary-600 mb-1">От</label>
                                <input
                                    v-model.number="priceRange.min"
                                    type="number"
                                    placeholder="0"
                                    class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                                    @input="applyPriceFilter"
                                />
                            </div>
                            <div>
                                <label class="block text-xs text-secondary-600 mb-1">До</label>
                                <input
                                    v-model.number="priceRange.max"
                                    type="number"
                                    placeholder="100000"
                                    class="w-full px-3 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                                    @input="applyPriceFilter"
                                />
                            </div>
                        </div>
                        
                        <!-- Price Presets -->
                        <div class="space-y-2">
                            <button
                                v-for="preset in pricePresets"
                                :key="preset.label"
                                @click="applyPricePreset(preset)"
                                class="w-full text-left px-3 py-2 text-sm text-secondary-700 hover:bg-secondary-50 rounded-lg transition-colors"
                            >
                                {{ preset.label }}
                            </button>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- Availability Filter -->
            <div class="filter-section">
                <button
                    @click="toggleSection('availability')"
                    class="flex items-center justify-between w-full text-left mb-3"
                >
                    <h3 class="font-medium text-secondary-900">Наличие</h3>
                    <svg 
                        class="w-4 h-4 text-secondary-400 transition-transform duration-200" 
                        :class="{ 'rotate-180': openSections.availability }"
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 max-h-0"
                    enter-to-class="opacity-100 max-h-96"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 max-h-96"
                    leave-to-class="opacity-0 max-h-0"
                >
                    <div v-if="openSections.availability" class="space-y-2 overflow-hidden">
                        <label
                            v-for="option in availabilityOptions"
                            :key="option.value"
                            class="flex items-center space-x-3 cursor-pointer group"
                        >
                            <input
                                type="checkbox"
                                :value="option.value"
                                v-model="selectedAvailability"
                                @change="applyFilters"
                                class="w-4 h-4 text-primary-600 border-secondary-300 rounded focus:ring-primary-500"
                            />
                            <span class="text-sm text-secondary-700 group-hover:text-secondary-900 transition-colors">
                                {{ option.label }}
                            </span>
                        </label>
                    </div>
                </transition>
            </div>
        </div>

        <!-- Active Filters Summary -->
        <div v-if="hasActiveFilters" class="mt-6 pt-6 border-t border-secondary-200">
            <h4 class="text-sm font-medium text-secondary-900 mb-3">Активные фильтры:</h4>
            <div class="flex flex-wrap gap-2">
                <span
                    v-for="filter in activeFiltersList"
                    :key="filter.key"
                    class="inline-flex items-center px-2 py-1 bg-primary-100 text-primary-700 text-xs rounded-full"
                >
                    {{ filter.label }}
                    <button
                        @click="removeFilter(filter.key)"
                        class="ml-1 hover:text-primary-900 transition-colors"
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, watch } from 'vue'

export default {
    props: {
        categories: {
            type: Array,
            default: () => []
        },
        priceRange: {
            type: Object,
            default: () => ({ min: 0, max: 100000 })
        }
    },
    emits: ['filter-change'],
    setup(props, { emit }) {
        const searchQuery = ref('')
        const selectedCategories = ref([])
        const selectedAvailability = ref([])
        const priceRange = ref({ min: null, max: null })
        
        const openSections = ref({
            categories: true,
            price: true,
            availability: true
        })

        const pricePresets = [
            { label: 'До 1 000 ₽', min: 0, max: 1000 },
            { label: '1 000 - 5 000 ₽', min: 1000, max: 5000 },
            { label: '5 000 - 10 000 ₽', min: 5000, max: 10000 },
            { label: '10 000 - 50 000 ₽', min: 10000, max: 50000 },
            { label: 'Более 50 000 ₽', min: 50000, max: null }
        ]

        const availabilityOptions = [
            { value: 'in_stock', label: 'В наличии' },
            { value: 'pre_order', label: 'Под заказ' },
            { value: 'out_of_stock', label: 'Нет в наличии' }
        ]

        const filteredCategories = computed(() => {
            if (!searchQuery.value) return props.categories
            return props.categories.filter(category =>
                category.name.toLowerCase().includes(searchQuery.value.toLowerCase())
            )
        })

        const hasActiveFilters = computed(() => {
            return selectedCategories.value.length > 0 ||
                   selectedAvailability.value.length > 0 ||
                   priceRange.value.min !== null ||
                   priceRange.value.max !== null
        })

        const activeFiltersList = computed(() => {
            const filters = []
            
            if (selectedCategories.value.length > 0) {
                const categoryNames = props.categories
                    .filter(cat => selectedCategories.value.includes(cat.id))
                    .map(cat => cat.name)
                filters.push({
                    key: 'categories',
                    label: `Категории: ${categoryNames.join(', ')}`
                })
            }
            
            if (selectedAvailability.value.length > 0) {
                const availabilityNames = availabilityOptions
                    .filter(opt => selectedAvailability.value.includes(opt.value))
                    .map(opt => opt.label)
                filters.push({
                    key: 'availability',
                    label: `Наличие: ${availabilityNames.join(', ')}`
                })
            }
            
            if (priceRange.value.min !== null || priceRange.value.max !== null) {
                let label = 'Цена: '
                if (priceRange.value.min !== null && priceRange.value.max !== null) {
                    label += `${priceRange.value.min} - ${priceRange.value.max} ₽`
                } else if (priceRange.value.min !== null) {
                    label += `от ${priceRange.value.min} ₽`
                } else {
                    label += `до ${priceRange.value.max} ₽`
                }
                filters.push({
                    key: 'price',
                    label
                })
            }
            
            return filters
        })

        const toggleSection = (section) => {
            openSections.value[section] = !openSections.value[section]
        }

        const applyFilters = () => {
            emit('filter-change', {
                categories: selectedCategories.value,
                availability: selectedAvailability.value,
                priceRange: priceRange.value
            })
        }

        const applyPriceFilter = () => {
            // Debounce для цены
            clearTimeout(priceTimeout.value)
            priceTimeout.value = setTimeout(applyFilters, 500)
        }

        const applyPricePreset = (preset) => {
            priceRange.value = { min: preset.min, max: preset.max }
            applyFilters()
        }

        const clearAllFilters = () => {
            selectedCategories.value = []
            selectedAvailability.value = []
            priceRange.value = { min: null, max: null }
            applyFilters()
        }

        const removeFilter = (filterKey) => {
            switch (filterKey) {
                case 'categories':
                    selectedCategories.value = []
                    break
                case 'availability':
                    selectedAvailability.value = []
                    break
                case 'price':
                    priceRange.value = { min: null, max: null }
                    break
            }
            applyFilters()
        }

        const priceTimeout = ref(null)

        return {
            searchQuery,
            selectedCategories,
            selectedAvailability,
            priceRange,
            openSections,
            pricePresets,
            availabilityOptions,
            filteredCategories,
            hasActiveFilters,
            activeFiltersList,
            toggleSection,
            applyFilters,
            applyPriceFilter,
            applyPricePreset,
            clearAllFilters,
            removeFilter
        }
    }
}
</script>

<style scoped>
.filter-section {
    @apply border-b border-secondary-100 pb-4 last:border-b-0 last:pb-0;
}

/* Кастомный скроллбар для списка брендов */
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
