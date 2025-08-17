@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Заголовок формы -->
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-neutral-700/50">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-store mr-3 text-indigo-500"></i> Создать новый магазин
            </h2>
        </div>
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <!-- Тело формы -->
        <div class="p-6">
            <form method="POST" action="{{ route('stores.store') }}">
                @csrf

                <!-- Информация о магазине -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-indigo-500"></i> Информация о магазине
                    </h3>

                    <div class="space-y-4">
                        <!-- Название магазина -->
                        <div>
                            <label for="shop_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Название магазина</label>
                            <input id="shop_name" type="text" 
                                   class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-neutral-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('shop_name') border-red-500 @enderror" 
                                   name="shop_name" value="{{ old('shop_name') }}" required>
                            @error('shop_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Домен магазина -->
                        <div>
                            <label for="custom_domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Адрес магазина</label>
                            <div class="flex rounded-lg shadow-sm">
                                <input id="custom_domain" type="text" 
                                       class="flex-1 min-w-0 block w-full px-4 py-2 rounded-l-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-neutral-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('shop_domain') border-red-500 @enderror" 
                                       name="custom_domain" value="{{ old('custom_domain') }}">
                                <span class="inline-flex items-center px-4 rounded-r-lg border border-l-0 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-neutral-700 text-gray-500 dark:text-gray-400">
                                    .{{ config('tenancy.central_domains')[0] }}
                                </span>
                            </div>
                            @error('custom_domain')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Тарифный план -->
                        <div>
                            <label for="plan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Тарифный план</label>
                            <select id="plan" name="plan" 
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-neutral-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                                    required>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->name }}">{{ $plan->name }} - ${{ $plan->price }}/мес</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- THEME -->
                         @if (App\Models\Theme::all()->count() > 0)
                         <div>
                            <label for="theme_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Выберите тему</label>
                            <select id="theme_id" name="theme_id" 
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-neutral-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                                    required>
                                @foreach(App\Models\Theme::all() as $theme)
                                    <option value="{{ $theme->id ?? 1 }}">{{ $theme->name ?? 'free' }}</option>
                                @endforeach
                            </select>
                        </div>
                         @endif
                        
                    </div>
                </div>

                <!-- Кнопка отправки -->
                <div class="mt-8">
                    <button type="submit" 
                            class="w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-neutral-800 flex items-center justify-center">
                        <i class="fas fa-plus-circle mr-2"></i> Создать магазин
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection