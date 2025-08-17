<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'SaaS Platform') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="h-full bg-neutral-50 dark:bg-neutral-900 text-gray-600 dark:text-gray-400 transition-colors duration-200">
<div class="min-h-full flex flex-col">
    <!-- Header -->
    <header class="bg-white dark:bg-neutral-800 shadow-sm z-10 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="md:hidden mr-2 text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a href="/" class="text-xl font-bold text-indigo-600 dark:text-indigo-400 flex items-center">
                        <i class="fas fa-cubes mr-2"></i> SaaS Platform
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <button id="themeToggle" class="p-2 rounded-full text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700">
                        <i class="fas fa-moon dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:inline"></i>
                    </button>
                    @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                            <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-300">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span class="hidden md:inline text-gray-900 dark:text-white">{{ auth()->user()->name }}</span>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-neutral-800 rounded-md shadow-lg py-1 z-50 border border-gray-200 dark:border-gray-700">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700">Профиль</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-700">Настройки</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-neutral-700">Выйти</button>
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex flex-1 overflow-hidden bg-white dark:bg-neutral-800">
        <!-- Sidebar будет вставляться только там, где это нужно -->
        @yield('sidebar')

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-6 bg-neutral-50 dark:bg-neutral-900 transition-colors duration-200">
            @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 rounded-lg flex items-center border border-green-200 dark:border-green-800/50">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button class="ml-auto text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            @yield('content')
        </main>
    </div>
</div>

<script>
    // Toggle sidebar on mobile
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.toggle('-translate-x-full');
        }
    });

    // Theme toggle
    document.getElementById('themeToggle')?.addEventListener('click', function() {
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            localStorage.theme = 'light';
            document.documentElement.classList.remove('dark');
        } else {
            localStorage.theme = 'dark';
            document.documentElement.classList.add('dark');
        }
    });

    // Initialize theme
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
</script>

<style>
    /* Плавное открытие/закрытие сайдбара */
    #sidebar {
        transition: transform 0.3s ease-in-out;
        will-change: transform;
    }
    
    /* Затемнение контента при открытом сайдбаре на мобильных */
    @media (max-width: 767px) {
        #sidebar:not(.translate-x-full) + main {
            filter: brightness(0.95);
            transition: filter 0.3s ease;
        }
        #sidebar:not(.translate-x-full) {
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
        }
    }
    
    /* Улучшенный скроллбар */
    #sidebar::-webkit-scrollbar {
        width: 6px;
    }
    #sidebar::-webkit-scrollbar-thumb {
        background-color: rgba(165, 180, 252, 0.5);
        border-radius: 3px;
    }
    .dark #sidebar::-webkit-scrollbar-thumb {
        background-color: rgba(99, 102, 241, 0.5);
    }
    
    /* Анимация элементов сайдбара */
    #sidebar a {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Подсветка активного элемента */
    #sidebar .router-link-active {
        background-color: rgba(99, 102, 241, 0.1);
        border-left: 3px solid #6366f1;
    }
</style>
</body>
</html>