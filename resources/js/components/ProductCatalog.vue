<template>
    <div class="mx-auto p-4">
        <SearchBar @search="onSearch" />

        <div class="flex flex-col lg:flex-row gap-8 mt-6">
            <FiltersSidebar
                :categories="categories"
                @filter-change="onFilterChange"
                class="lg:w-1/4"
            />

            <main class="lg:w-3/4">
                <ProductGrid
                    :products="products"
                    :pagination="pagination"
                    @page-change="onPageChange"
                />
            </main>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import SearchBar from './SearchBar.vue'
import FiltersSidebar from './FiltersSidebar.vue'
import ProductGrid from './ProductGrid.vue'

export default {
    components: { SearchBar, FiltersSidebar, ProductGrid },

    data() {
        return {
            query: '',
            filters: {},
            page: 1,
            products: [],
            pagination: {},
            categories: [],
        }
    },

    mounted() {
        this.fetchProducts()
    },

    methods: {
        fetchProducts() {
            const params = { query: this.query, page: this.page, ...this.filters };
            axios.get('/api/products', { params }).then(({ data }) => {
                this.products = data.products;
                this.pagination = data.pagination;
                this.categories = data.categories;
            });
        },

        onSearch(query) {
            this.query = query
            this.page = 1
            this.fetchProducts()
        },

        onFilterChange(filters) {
            this.filters = filters
            this.page = 1
            this.fetchProducts()
        },

        onPageChange(newPage) {
            this.page = newPage
            this.fetchProducts()
        },
    },
}
</script>
