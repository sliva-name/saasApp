<template>
    <div class="relative" @mouseleave="isOpen = false">
        <button
            @mouseenter="isOpen = true"
            @focus="isOpen = true"
            @blur="closeWithDelay"
            :aria-expanded="isOpen.toString()"
            aria-haspopup="true"
            type="button"
            class="inline-flex items-center px-3 py-2 text-gray-700 hover:text-indigo-600 focus:outline-none"
        >
            {{ label }}
            <svg
                class="ml-1 h-4 w-4"
                fill="currentColor"
                viewBox="0 0 20 20"
                aria-hidden="true"
            >
                <path
                    fill-rule="evenodd"
                    d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                    clip-rule="evenodd"
                />
            </svg>
        </button>

        <transition name="fade">
            <div
                v-if="isOpen"
                class="absolute left-0 mt-2 w-56 bg-white shadow-lg rounded-md border border-gray-200 z-50"
                @mouseenter="isOpen = true"
                @mouseleave="isOpen = false"
            >
                <div class="p-4 grid grid-cols-2 gap-4">
                    <div v-for="(section, index) in items" :key="index">
                        <p class="font-semibold text-gray-900 mb-2">{{ section.title }}</p>
                        <ul class="space-y-1">
                            <li v-for="(link, idx) in section.links" :key="idx">
                                <a href="#" class="block px-2 py-1 text-gray-700 hover:bg-indigo-100 rounded">{{ link }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    label: String,
    items: Array,
})

const isOpen = ref(false)
let closeTimeout = null

function closeWithDelay() {
    closeTimeout = setTimeout(() => {
        isOpen.value = false
    }, 150)
}

function clearCloseTimeout() {
    if (closeTimeout) {
        clearTimeout(closeTimeout)
        closeTimeout = null
    }
}
</script>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
