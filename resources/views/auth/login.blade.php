<x-guest-layout>
    <!-- Login Form -->
    <div class="w-full sm:max-w-md mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-10">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto h-16 w-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg mb-4">
                <i class="fas fa-store text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                Добро пожаловать
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Войдите в свой аккаунт для продолжения
            </p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800 dark:text-green-200">
                            {{ session('status') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm text-red-800 dark:text-red-200">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Email адрес
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                           placeholder="Введите ваш email">
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Пароль
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"
                           placeholder="Введите ваш пароль">
                </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_me" 
                           type="checkbox" 
                           name="remember"
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-700">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Запомнить меня
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" 
                       class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 font-medium transition-colors">
                        Забыли пароль?
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-indigo-300 group-hover:text-indigo-200 transition-colors"></i>
                    </span>
                    Войти в аккаунт
                </button>
            </div>

            <!-- Divider -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">или</span>
                </div>
            </div>

            <!-- Register Link -->
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Нет аккаунта? 
                    <a href="{{ route('register') }}" 
                       class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">
                        Зарегистрироваться
                    </a>
                </p>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <div class="text-center mt-8">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            © {{ date('Y') }} {{ config('app.name') }}. Все права защищены.
        </p>
    </div>
</x-guest-layout>
