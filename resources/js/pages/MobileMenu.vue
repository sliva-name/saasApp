<template>
    <!-- Mobile Menu -->
    <dialog id="mobile-menu" class="fixed inset-0 z-30 flex bg-white p-6" style="right: var(--el-top-layer-scrollbar-offset, 0px);">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/25 transition-opacity duration-300 ease-in-out" inert></div>

        <!-- Panel -->
        <div tabindex="0" class="fixed inset-0 flex outline-none">
            <div class="relative flex w-full max-w-xs flex-col overflow-y-auto bg-white pb-6 shadow-xl transition duration-300 ease-in-out">
                <!-- Close button -->
                <div class="flex justify-end px-4 pt-5 pb-2">
                    <button type="button" command="close" commandfor="mobile-menu" class="relative -m-2 inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close menu</span>
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                <!-- Tabs and navigation will go here -->
                <!-- Tabs -->
                <div class="border-b border-gray-200 px-4">
                    <nav class="flex space-x-4" role="tablist" aria-orientation="horizontal">
                        <button class="flex-1 border-b-2 py-2 text-base font-medium text-indigo-600 border-indigo-600" id="tab-women" role="tab" aria-selected="true" aria-controls="tab-panel-women">
                            Women
                        </button>
                        <button class="flex-1 border-b-2 py-2 text-base font-medium text-gray-500 hover:text-gray-700 border-transparent" id="tab-men" role="tab" aria-selected="false" aria-controls="tab-panel-men">
                            Men
                        </button>
                    </nav>
                </div>

                <!-- Panel: Women -->
                <div id="tab-panel-women" class="space-y-10 px-4 pt-6 pb-8" role="tabpanel" aria-labelledby="tab-women">
                    <div class="grid grid-cols-1 gap-y-10 gap-x-6">
                        <div>
                            <p id="mobile-featured-heading-0" class="text-sm font-medium text-gray-900">Featured</p>
                            <ul role="list" aria-labelledby="mobile-featured-heading-0" class="mt-6 space-y-2">
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">Sleep</a></li>
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">Swimwear</a></li>
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">Underwear</a></li>
                            </ul>
                        </div>

                        <div>
                            <p id="mobile-categories-heading" class="text-sm font-medium text-gray-900">Categories</p>
                            <ul role="list" aria-labelledby="mobile-categories-heading" class="mt-6 space-y-2">
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">Basic Tees</a></li>
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">Artwork Tees</a></li>
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">Bottoms</a></li>
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">Underwear</a></li>
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">Accessories</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-y-10 gap-x-6">
                        <div>
                            <p id="mobile-collection-heading" class="text-sm font-medium text-gray-900">Collection</p>
                            <ul role="list" aria-labelledby="mobile-collection-heading" class="mt-6 space-y-2">
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">Everything</a></li>
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">Core</a></li>
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">New Arrivals</a></li>
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">Sale</a></li>
                            </ul>
                        </div>

                        <div>
                            <p id="mobile-brand-heading" class="text-sm font-medium text-gray-900">Brands</p>
                            <ul role="list" aria-labelledby="mobile-brand-heading" class="mt-6 space-y-2">
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">Full Nelson</a></li>
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">My Way</a></li>
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">Re-Arranged</a></li>
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">Counterfeit</a></li>
                                <li><a href="#" class="text-gray-700 hover:text-gray-900">Significant Other</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Static Links: Company / Stores -->
                <div class="space-y-6 border-t border-gray-200 px-4 py-6">
                    <div>
                        <a href="#" class="block text-sm font-medium text-gray-900 hover:text-gray-700">Company</a>
                    </div>
                    <div>
                        <a href="#" class="block text-sm font-medium text-gray-900 hover:text-gray-700">Stores</a>
                    </div>
                </div>

                <!-- Auth Links: Create account / Sign in -->
                <div class="space-y-6 border-t border-gray-200 px-4 py-6">
                    <div>
                        <a href="#" class="block text-sm font-medium text-gray-900 hover:text-gray-700">Create an account</a>
                    </div>
                    <div>
                        <a href="#" class="block text-sm font-medium text-gray-900 hover:text-gray-700">Sign in</a>
                    </div>
                </div>

                <!-- Currency Selector -->
                <div class="border-t border-gray-200 px-4 py-6">
                    <form>
                        <div class="relative">
                            <select id="mobile-currency" name="currency" aria-label="Currency"
                                    class="block w-full rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-base font-medium text-gray-700 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                <option>CAD</option>
                                <option>USD</option>
                                <option>AUD</option>
                                <option>EUR</option>
                                <option>GBP</option>
                            </select>

                            <!-- Dropdown icon -->
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z" />
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Мы добавим их в следующем шаге -->

            </div>
        </div>
    </dialog>
</template>
<script>

</script>
