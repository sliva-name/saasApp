<template>
    <header>
        <nav aria-label="Primary" class="bg-indigo-50 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <a href="#" class="flex items-center space-x-2">
                        <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600" alt="Logo" class="h-8 w-auto" />
                        <span class="font-bold text-indigo-700 text-lg">Your Company</span>
                    </a>

                    <!-- Desktop menu -->
                    <nav class="hidden lg:flex space-x-8">
                        <DropdownMenu
                            label="Women"
                            :items="womenMenu"
                        />
                        <DropdownMenu
                            label="Men"
                            :items="menMenu"
                        />
                        <a href="#" class="text-gray-700 hover:text-indigo-600 flex items-center">Company</a>
                        <a href="#" class="text-gray-700 hover:text-indigo-600 flex items-center">Stores</a>
                    </nav>

                    <!-- Mobile menu & search buttons -->
                    <div class="flex lg:hidden items-center space-x-4">
                        <button
                            @click="isMobileMenuOpen = true"
                            aria-label="Open menu"
                            class="text-gray-700 hover:text-indigo-600 focus:outline-none"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <button
                            @click="isSearchOpen = true"
                            aria-label="Search"
                            class="text-gray-700 hover:text-indigo-600 focus:outline-none"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M21 21l-5.197-5.197a7.5 7.5 0 10-10.607-10.607 7.5 7.5 0 0010.607 10.607z"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- Account & Cart (desktop only) -->
                    <div class="hidden lg:flex items-center space-x-6">
                        <a href="#" class="text-gray-700 hover:text-indigo-600 flex items-center space-x-1">
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.12a7.5 7.5 0 0 1 15 0 17.93 17.93 0 0 1-7.5 1.63 17.93 17.93 0 0 1-7.5-1.63Z"
                                />
                            </svg>
                            <span>Account</span>
                        </a>

                        <span class="h-6 w-px bg-gray-300"></span>

                        <a href="#" class="relative text-gray-700 hover:text-indigo-600 flex items-center space-x-1" aria-label="View cart">
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.12-2.3 2.1-4.68 2.924-7.13a60.11 60.11 0 0 0-16.54-1.84M7.5 14.25L5.1 5.27M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
                                />
                            </svg>
                            <span class="text-sm font-medium">0</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mobile menu panel -->
            <transition name="fade">
                <div
                    v-if="isMobileMenuOpen"
                    class="fixed inset-0 z-40 bg-black bg-opacity-25 lg:hidden"
                    @click.self="isMobileMenuOpen = false"
                >
                    <div class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg p-4 overflow-y-auto">
                        <button
                            @click="isMobileMenuOpen = false"
                            aria-label="Close menu"
                            class="mb-4 text-gray-700 hover:text-indigo-600 focus:outline-none"
                        >
                            ✕ Close
                        </button>

                        <nav class="space-y-4">
                            <MobileDropdownMenu label="Women" :items="womenMenu" />
                            <MobileDropdownMenu label="Men" :items="menMenu" />
                            <a href="#" class="block px-2 py-1 text-gray-700 hover:bg-indigo-100 rounded">Company</a>
                            <a href="#" class="block px-2 py-1 text-gray-700 hover:bg-indigo-100 rounded">Stores</a>
                        </nav>
                    </div>
                </div>
            </transition>

            <!-- Search modal placeholder -->
            <transition name="fade">
                <div
                    v-if="isSearchOpen"
                    class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center"
                    @click.self="isSearchOpen = false"
                >
                    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
                        <button
                            @click="isSearchOpen = false"
                            aria-label="Close search"
                            class="mb-4 text-gray-700 hover:text-indigo-600 focus:outline-none"
                        >
                            ✕ Close
                        </button>
                        <input
                            type="search"
                            placeholder="Search products..."
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600"
                            v-model="searchQuery"
                            @keyup.enter="onSearch"
                            autofocus
                        />
                    </div>
                </div>
            </transition>
        </nav>
    </header>
</template>

<script setup>
import { ref } from 'vue'
import DropdownMenu from "./DropdownMenu.vue";
import MobileDropdownMenu from "./MobileDropdownMenu.vue";

// Sample menu items
const womenMenu = [
    {
        title: 'Featured',
        links: ['Sleep', 'Swimwear', 'Underwear'],
    },
    {
        title: 'Categories',
        links: ['Basic Tees', 'Artwork Tees', 'Bottoms', 'Underwear', 'Accessories'],
    },
    {
        title: 'Collection',
        links: ['Everything', 'Core', 'New Arrivals', 'Sale'],
    },
    {
        title: 'Brands',
        links: ['Full Nelson', 'My Way', 'Re-Arranged', 'Counterfeit', 'Significant Other'],
    },
]

const menMenu = [
    {
        title: 'Featured',
        links: ['Casual', 'Boxers', 'Outdoor'],
    },
    {
        title: 'Categories',
        links: ['Artwork Tees', 'Pants', 'Accessories', 'Boxers', 'Basic Tees'],
    },
    {
        title: 'Collection',
        links: ['Everything', 'Core', 'New Arrivals', 'Sale'],
    },
    {
        title: 'Brands',
        links: ['Significant Other', 'My Way', 'Counterfeit', 'Re-Arranged', 'Full Nelson'],
    },
]

const isMobileMenuOpen = ref(false)
const isSearchOpen = ref(false)
const searchQuery = ref('')

function onSearch() {
    alert(`Search for: ${searchQuery.value}`)
}

// DropdownMenu и MobileDropdownMenu — компоненты для выпадающих меню,
// ниже пример реализации, если нужно — могу сделать.
</script>
