<template>
    <div class="relative" @mouseenter="isOpen = true" @mouseleave="isOpen = false">
        <button
            class="flex items-center space-x-1 text-secondary-700 hover:text-primary-600 font-medium transition-colors duration-200 relative group"
            :class="buttonClass"
        >
            <span>{{ label }}</span>
            <svg 
                class="w-4 h-4 transition-transform duration-200" 
                :class="{ 'rotate-180': isOpen }"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-primary-500 group-hover:w-full transition-all duration-200"></span>
        </button>

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-1"
        >
            <div
                v-if="isOpen"
                class="absolute top-full left-0 mt-2 w-64 bg-white rounded-xl shadow-large border border-secondary-200 py-2 z-50"
            >
                <div class="px-4 py-2">
                    <div class="grid grid-cols-1 gap-1">
                        <RouterLink
                            v-for="item in items"
                            :key="item.name"
                            :to="item.href"
                            class="flex items-center px-3 py-2 text-sm text-secondary-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors duration-150 group"
                        >
                            <span class="flex-1">{{ item.name }}</span>
                            <svg 
                                class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity duration-150" 
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </RouterLink>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'

export default {
    components: {
        RouterLink
    },
    props: {
        label: {
            type: String,
            required: true
        },
        items: {
            type: Array,
            required: true,
            default: () => []
        },
        buttonClass: {
            type: String,
            default: ''
        }
    },
    setup() {
        const isOpen = ref(false)

        return {
            isOpen
        }
    }
}
</script>

<style scoped>
/* Дополнительные стили для плавных переходов */
.transition-transform {
    transition-property: transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

.rotate-180 {
    transform: rotate(180deg);
}
</style>
