@if (session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info') || $errors->any())
    <div class="fixed top-4 right-4 z-50 w-full max-w-xs space-y-4 notification-container">
        <!-- Success Notification -->
        @if (session()->has('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 class="relative p-4 bg-green-50 dark:bg-green-900/30 border border-green-100 dark:border-green-800/50 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-green-500 dark:text-green-400">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="absolute top-3 right-3 text-green-500 hover:text-green-700 dark:hover:text-green-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Error Notification -->
        @if (session()->has('error'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 class="relative p-4 bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-800/50 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-red-500 dark:text-red-400">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="absolute top-3 right-3 text-red-500 hover:text-red-700 dark:hover:text-red-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 class="relative p-4 bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-800/50 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-red-500 dark:text-red-400">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">Ошибка валидации</p>
                        <ul class="mt-1 text-xs text-red-700 dark:text-red-300 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button @click="show = false" class="absolute top-3 right-3 text-red-500 hover:text-red-700 dark:hover:text-red-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Warning Notification -->
        @if (session()->has('warning'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 class="relative p-4 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-100 dark:border-yellow-800/50 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-yellow-500 dark:text-yellow-400">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ session('warning') }}</p>
                    </div>
                    <button @click="show = false" class="absolute top-3 right-3 text-yellow-500 hover:text-yellow-700 dark:hover:text-yellow-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Info Notification -->
        @if (session()->has('info'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 class="relative p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800/50 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0 text-blue-500 dark:text-blue-400">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-200">{{ session('info') }}</p>
                    </div>
                    <button @click="show = false" class="absolute top-3 right-3 text-blue-500 hover:text-blue-700 dark:hover:text-blue-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif
    </div>
@endif