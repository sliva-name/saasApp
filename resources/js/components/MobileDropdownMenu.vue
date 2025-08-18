<template>
    <div>
        <button
            type="button"
            @click="toggle"
            class="w-full flex justify-between items-center px-2 py-2 text-gray-700 hover:bg-indigo-100 rounded focus:outline-none"
            :aria-expanded="isOpen.toString()"
        >
            {{ label }}
            <svg
                :class="{'transform rotate-180': isOpen}"
                class="h-5 w-5 transition-transform duration-200"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <transition name="fade">
            <div v-if="isOpen" class="mt-2 pl-4 space-y-4 border-l border-gray-300">
                <div v-for="(section, idx) in items" :key="idx">
                    <p class="font-semibold text-gray-900 mb-1">{{ section.title }}</p>
                    <ul class="space-y-1">
                        <li v-for="(link, i) in section.links" :key="i">
                            <a href="#" class="block px-2 py-1 text-gray-700 hover:bg-indigo-100 rounded">{{ link }}</a>
                        </li>
                    </ul>
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

function toggle() {
    isOpen.value = !isOpen.value
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
