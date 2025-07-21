<template>
    <div class="relative w-full max-w-xl mx-auto rounded">
        <input
            v-model="query"
            @input="onInput"
            type="search"
            placeholder="🔍 Поиск товаров..."
            class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500"
        />

        <ul
            v-if="suggestions.length"
            class="absolute z-20 w-full bg-white border border-gray-200 rounded-xl mt-2 shadow-lg max-h-52 overflow-y-auto"
        >
            <li
                v-for="item in suggestions"
                :key="item.id"
                @click="selectSuggestion(item)"
                class="px-4 py-2 cursor-pointer hover:bg-blue-100 transition"
            >
                {{ item.name }}
            </li>
        </ul>
    </div>
</template>



<script>
import axios from 'axios'
import { debounce } from 'lodash-es'

export default {
    data() {
        return {
            query: '',
            suggestions: [],
        }
    },
    methods: {
        // Создаем отдельно функцию с debounce, чтобы можно было в ней эмитить
        onInput: debounce(function () {
            this.$emit('search', this.query)  // Эмитим текущий текст запроса

            if (this.query.length < 2) {
                this.suggestions = []
                return
            }

            axios
                .get('/search/suggest', { params: { q: this.query } })
                .then(({ data }) => {
                    this.suggestions = data
                })
        }, 300),

        selectSuggestion(item) {
            this.query = item.name
            this.suggestions = []
            this.$emit('search', this.query)
        },
    },
}
</script>
