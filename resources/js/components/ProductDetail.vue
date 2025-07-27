<template>
    <main class="bg-white" v-if="product">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Product -->
            <div class="flex flex-col lg:flex-row gap-12 py-12">
                <!-- Image gallery -->
                <div class="w-full lg:w-1/2">
                    <div>
                        <div role="tablist" aria-orientation="horizontal" class="flex space-x-4 mb-4 overflow-x-auto">
                            <button
                                v-for="(mediaItem, index) in product.media"
                                :key="mediaItem.id"
                                :id="`tab-${index}`"
                                :aria-controls="`panel-${index}`"
                                role="tab"
                                :tabindex="index === 0 ? 0 : -1"
                                :aria-selected="index === 0"
                                class="focus:outline-none border rounded-md p-1"
                                :class="{ 'border-2 border-indigo-600': index === 0 }"
                            >
                                <span class="sr-only">Image {{ index + 1 }}</span>
                                <img :src="mediaItem.original_url" alt="Product thumbnail" class="w-24 h-24 object-cover rounded" />
                            </button>
                        </div>
                        <div>
                            <div
                                v-for="(mediaItem, index) in product.media"
                                :key="mediaItem.id"
                                :id="`panel-${index}`"
                                role="tabpanel"
                                :aria-labelledby="`tab-${index}`"
                                :class="index === 0 ? 'block' : 'hidden'"
                            >
                                <img :src="mediaItem.original_url" alt="Product image" class="w-full rounded-lg object-cover" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product info -->
                <div class="w-full lg:w-1/2 flex flex-col space-y-8">
                    <h1 class="text-3xl font-extrabold text-gray-900">{{ product.name }}</h1>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 mb-1">Информация о продукте</h2>
                        <p class="text-2xl font-bold text-gray-900">${{ product.price }}</p>
                    </div>

                    <!-- Reviews (заглушка) -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Отзывы</h3>
                        <div class="flex items-center space-x-1">
                            <template v-for="n in 4">
                                <svg class="w-6 h-6 text-indigo-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" />
                                </svg>
                            </template>
                            <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" />
                            </svg>
                            <p class="ml-2 text-gray-700 font-medium">4 out of 5 stars</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Описание</h3>
                        <p class="text-gray-700">{{ product.description }}</p>
                    </div>

                    <!-- Add to bag -->
                    <form class="space-y-6">
                        <div class="flex space-x-4">
                            <button
                                type="submit"
                                class="flex-grow bg-indigo-600 text-white font-semibold py-3 rounded-md hover:bg-indigo-700 transition"
                            >
                                Add to bag
                            </button>
                            <button type="button" class="flex items-center space-x-2 px-4 rounded-md hover:bg-gray-100 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    <!-- Additional details -->
                    <section aria-labelledby="details-heading" class="mt-8">
                        <h2 id="details-heading" class="text-xl font-semibold text-gray-900 mb-4">Дополнительные сведения</h2>
                        <div class="space-y-6">
                            <details open class="border rounded-md p-4">
                                <summary class="font-semibold cursor-pointer">Features</summary>
                                <ul class="list-disc list-inside mt-2 text-gray-700 space-y-1">
                                    <li>Double stitched construction</li>
                                    <li>Water-resistant</li>
                                    <li>Multiple strap configurations</li>
                                    <li>Interior dividers</li>
                                </ul>
                            </details>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const product = ref(null)

onMounted(async () => {
    const slug = route.params.slug
    const { data } = await axios.get(`/api/products/${slug}`)
    product.value = data.data
})
</script>
