<aside id="sidebar" class="hidden md:flex md:flex-col w-64 border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-neutral-800 transition-all duration-300 transform -translate-x-full md:translate-x-0 fixed md:static inset-y-0 left-0 z-20">
    <div class="flex flex-col flex-grow pt-6 pb-4 overflow-y-auto">
        <!-- Логотип для мобильной версии -->
        <div class="flex items-center px-4 pb-6 md:hidden">
            <i class="fas fa-cubes text-2xl text-indigo-600 dark:text-indigo-400 mr-2"></i>
            <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">SaaS Platform</span>
        </div>

        <!-- Контент сайдбара -->
        <div class="px-4">
            <a href="{{ route('stores.create') }}" class="w-full mb-6 flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors duration-200 shadow-sm">
                <i class="fas fa-plus mr-2"></i> Новый магазин
            </a>

            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-3 flex items-center text-gray-800 dark:text-gray-200">
                    <i class="fas fa-store mr-2 text-indigo-500"></i> Мои магазины
                </h2>
                <div class="relative mb-3">
                    <input type="text" placeholder="Поиск магазинов..." class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                </div>
            </div>

            <nav class="flex-1 space-y-1">
                @isset($stores)
                    @foreach($stores as $store)
                        <div class="group">
                            @foreach($store->domains as $domain)
                                <a href="http://{{ $domain->domain }}:8000" target="_blank" class="flex items-center justify-between px-3 py-2.5 text-sm rounded-lg transition-colors duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/70 group-hover:bg-gray-50 dark:group-hover:bg-gray-700/50">
                                    <div class="flex items-center min-w-0">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-300 mr-3">
                                            <i class="fas fa-store text-sm"></i>
                                        </div>
                                        <span class="truncate">{{ $domain->domain }}</span>
                                    </div>
                                    <span class="text-xs bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 px-2 py-1 rounded-full whitespace-nowrap">Активен</span>
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                @endisset
            </nav>
        </div>
    </div>

    <!-- Нижняя часть сайдбара -->
    <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            <div class="h-9 w-9 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-300 mr-3">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>
</aside>