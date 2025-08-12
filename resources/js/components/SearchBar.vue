<template>
    <div class="relative">
        <!-- Search Input -->
        <div class="relative">
            <input
                ref="searchInput"
                v-model="query"
                type="text"
                :placeholder="placeholder"
                class="w-full pl-12 pr-4 py-3 border border-secondary-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 bg-white shadow-soft focus:shadow-medium text-secondary-900 placeholder-secondary-400"
                @input="handleInput"
                @focus="showSuggestions = true"
                @keydown.enter="performSearch"
                @keydown.escape="hideSuggestions"
                @keydown.down.prevent="navigateSuggestions(1)"
                @keydown.up.prevent="navigateSuggestions(-1)"
            />

            <!-- Search Icon -->
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Clear Button -->
            <button
                v-if="query"
                @click="clearSearch"
                class="absolute inset-y-0 right-0 pr-4 flex items-center text-secondary-400 hover:text-secondary-600 transition-colors"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Search Button -->
            <button
                v-else
                @click="performSearch"
                class="absolute inset-y-0 right-0 pr-4 flex items-center text-primary-600 hover:text-primary-700 transition-colors"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </div>

        <!-- Search Suggestions -->
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-1"
        >
            <div
                v-if="showSuggestions && (suggestions.length > 0 || recentSearches.length > 0)"
                class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-large border border-secondary-200 py-2 z-50 max-h-96 overflow-y-auto"
            >
                <!-- Recent Searches -->
                <div v-if="recentSearches.length > 0 && !query" class="px-4 py-2">
                    <h3 class="text-xs font-semibold text-secondary-500 uppercase tracking-wide mb-2">
                        Недавние поиски
                    </h3>
                    <div class="space-y-1">
                        <button
                            v-for="(search, index) in recentSearches.slice(0, 5)"
                            :key="index"
                            @click="selectRecentSearch(search)"
                            class="w-full text-left px-3 py-2 text-sm text-secondary-700 hover:bg-secondary-50 rounded-lg transition-colors duration-150 flex items-center space-x-2"
                        >
                            <svg class="w-4 h-4 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ search }}</span>
                        </button>
                    </div>
                </div>

                <!-- Search Suggestions -->
                <div v-if="suggestions.length > 0" class="px-4 py-2">
                    <h3 v-if="!query" class="text-xs font-semibold text-secondary-500 uppercase tracking-wide mb-2">
                        Популярные запросы
                    </h3>
                    <div class="space-y-1">
                        <button
                            v-for="(suggestion, index) in suggestions"
                            :key="index"
                            @click="selectSuggestion(suggestion)"
                            @mouseenter="highlightedIndex = index"
                            class="w-full text-left px-3 py-2 text-sm rounded-lg transition-colors duration-150 flex items-center space-x-2"
                            :class="index === highlightedIndex
                                ? 'bg-primary-50 text-primary-700'
                                : 'text-secondary-700 hover:bg-secondary-50'"
                        >
                            <svg class="w-4 h-4 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>{{ suggestion }}</span>
                        </button>
                    </div>
                </div>

                <!-- Quick Filters -->
                <div v-if="!query" class="px-4 py-2 border-t border-secondary-100">
                    <h3 class="text-xs font-semibold text-secondary-500 uppercase tracking-wide mb-2">
                        Быстрые фильтры
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="filter in quickFilters"
                            :key="filter.label"
                            @click="applyQuickFilter(filter)"
                            class="px-3 py-1 text-xs bg-secondary-100 text-secondary-700 rounded-full hover:bg-primary-100 hover:text-primary-700 transition-colors duration-150"
                        >
                            {{ filter.label }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Loading Indicator -->
        <div
            v-if="isLoading"
            class="absolute inset-y-0 right-0 pr-12 flex items-center"
        >
            <div class="animate-spin rounded-full h-4 w-4 border-2 border-primary-500 border-t-transparent"></div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import axios from 'axios'

export default {
    props: {
        placeholder: {
            type: String,
            default: 'Поиск товаров...'
        },
        initialQuery: {
            type: String,
            default: ''
        }
    },
    emits: ['search', 'filter-change'],
    setup(props, { emit }) {
        const query = ref(props.initialQuery)
        const showSuggestions = ref(false)
        const suggestions = ref([])
        const recentSearches = ref([])
        const highlightedIndex = ref(-1)
        const isLoading = ref(false)
        const searchInput = ref(null)
        let debounceTimer = null

        // Популярные запросы
        const popularSearches = [
            'Смартфоны',
            'Ноутбуки',
            'Одежда',
            'Обувь',
            'Аксессуары',
            'Электроника',
            'Книги',
            'Спорт'
        ]

        // Быстрые фильтры
        const quickFilters = [
            { label: 'Новинки', value: 'new' },
            { label: 'Акции', value: 'sale' },
            { label: 'Популярное', value: 'popular' },
            { label: 'Доставка сегодня', value: 'fast-delivery' }
        ]

        const handleInput = () => {
            if (debounceTimer) {
                clearTimeout(debounceTimer)
            }

            const typed = query.value.trim()

            if (typed.length < 2) {
                suggestions.value = []
                isLoading.value = false
                return
            }

            isLoading.value = true

            debounceTimer = setTimeout(async () => {
                try {
                    const { data } = await axios.get('/search/suggest', { params: { q: typed } })
                    const names = Array.isArray(data) ? data.map(item => item?.name).filter(Boolean) : []
                    // Уникальные варианты, максимум 5
                    showSuggestions.value = suggestions.value.length > 0
                    suggestions.value = Array.from(new Set(names)).slice(0, 5)
                } catch (error) {
                    console.error('Error fetching suggestions:', error)
                    suggestions.value = []
                } finally {
                    isLoading.value = false
                }
            }, 250)
        }

        const performSearch = () => {
            if (query.value.trim()) {
                addToRecentSearches(query.value.trim())
                emit('search', query.value.trim())
                hideSuggestions()
            }
        }

        const selectSuggestion = (suggestion) => {
            query.value = suggestion
            performSearch()
        }

        const selectRecentSearch = (search) => {
            query.value = search
            performSearch()
        }

        const applyQuickFilter = (filter) => {
            emit('filter-change', filter.value)
            hideSuggestions()
        }

        const clearSearch = () => {
            query.value = ''
            suggestions.value = []
            emit('search', '')
            searchInput.value?.focus()
        }

        const hideSuggestions = () => {
            showSuggestions.value = false
            highlightedIndex.value = -1
        }

        const navigateSuggestions = (direction) => {
            const items = [...suggestions.value, ...recentSearches.value]
            if (items.length === 0) return

            highlightedIndex.value += direction

            if (highlightedIndex.value >= items.length) {
                highlightedIndex.value = 0
            } else if (highlightedIndex.value < 0) {
                highlightedIndex.value = items.length - 1
            }
        }

        const addToRecentSearches = (search) => {
            const searches = recentSearches.value.filter(s => s !== search)
            searches.unshift(search)
            recentSearches.value = searches.slice(0, 10)

            // Сохранение в localStorage
            localStorage.setItem('recentSearches', JSON.stringify(recentSearches.value))
        }

        const loadRecentSearches = () => {
            try {
                const saved = localStorage.getItem('recentSearches')
                if (saved) {
                    recentSearches.value = JSON.parse(saved)
                }
            } catch (error) {
                console.error('Error loading recent searches:', error)
            }
        }

        // Обработка клика вне компонента
        const handleClickOutside = (event) => {
            if (!event.target.closest('.relative')) {
                hideSuggestions()
            }
        }

        onMounted(() => {
            loadRecentSearches()
            document.addEventListener('click', handleClickOutside)
        })

        onUnmounted(() => {
            document.removeEventListener('click', handleClickOutside)
            if (debounceTimer) {
                clearTimeout(debounceTimer)
            }
        })

        return {
            query,
            showSuggestions,
            suggestions,
            recentSearches,
            highlightedIndex,
            isLoading,
            searchInput,
            quickFilters,
            handleInput,
            performSearch,
            selectSuggestion,
            selectRecentSearch,
            applyQuickFilter,
            clearSearch,
            hideSuggestions,
            navigateSuggestions
        }
    }
}
</script>

<style scoped>
/* Кастомный скроллбар для выпадающего списка */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
