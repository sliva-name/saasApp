<aside id="sidebar" class="hidden md:flex md:flex-col w-72 border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-neutral-800 transition-all duration-300 transform -translate-x-full md:translate-x-0 fixed md:static inset-y-0 left-0 z-20">
    <div class="flex flex-col flex-grow pt-6 pb-4 overflow-y-auto">
        <!-- Лого и переключатель темы -->
        <div class="px-6 mb-8 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600 dark:text-indigo-400 flex items-center">
                <i class="fas fa-cubes mr-2"></i> SaaS Platform
            </a>
            <button id="sidebarToggle" class="md:hidden text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Основное меню -->
        <div class="px-4 space-y-6">
            <!-- Кнопка создания магазина -->
            <a href="{{ route('stores.create') }}" class="group flex items-center justify-between px-4 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                <div class="flex items-center">
                    <i class="fas fa-plus-circle mr-3 text-indigo-100"></i>
                    <span class="font-medium">Новый магазин</span>
                </div>
                <i class="fas fa-arrow-right text-xs opacity-70 group-hover:opacity-100 transition-opacity"></i>
            </a>

            <!-- Навигация -->
            <nav class="space-y-1">
                <x-nav-item href="{{ route('dashboard') }}" icon="fas fa-home" active="{{ request()->routeIs('dashboard') }}">
                    Главная
                </x-nav-item>

                <x-nav-item href="{{ route('stores.index') }}" icon="fas fa-store" active="{{ request()->routeIs('stores.*') }}">
                    Мои магазины
                    <span class="ml-auto bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200 text-xs px-2 py-0.5 rounded-full">
                        {{ auth()->user()->stores->count() }}
                    </span>
                </x-nav-item>

                <x-nav-item href="{{ route('settings.index') }}" icon="fas fa-cog" active="{{ request()->routeIs('settings') }}">
                    Настройки
                </x-nav-item>
            </nav>

            <!-- Список магазинов -->
            <div class="pt-4">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 px-3">
                    <i class="fas fa-store mr-1"></i> Ваши магазины
                </h3>

                <div class="space-y-1.5">
                    @forelse(auth()->user()->stores as $store)
                        <x-store-item :store="$store" />
                    @empty
                        <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                            У вас пока нет магазинов
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Профиль пользователя -->
    <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center group">
            <div class="h-9 w-9 rounded-full bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/50 dark:to-indigo-800/50 flex items-center justify-center text-indigo-600 dark:text-indigo-300 mr-3 font-medium group-hover:from-indigo-200 group-hover:to-indigo-300 transition-colors">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
            </div>
            <a href="{{ route('logout') }}" class="ml-auto text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 opacity-0 group-hover:opacity-100 transition-opacity" title="Выйти">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</aside>
