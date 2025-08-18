@extends('layouts.app')

@section('title', $store->name ?? 'Магазин ' . $store->id)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-store text-white text-2xl"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $store->name ?? 'Магазин ' . $store->id }}
        </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        Создан {{ $store->created_at->format('d.m.Y в H:i') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('stores.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Назад к списку
                </a>
                <button class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Редактировать
            </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Статус</p>
                    <div class="flex items-center mt-2">
                        <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">Активен</span>
                    </div>
                </div>
                <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">План</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-2">{{ $store->plan ?? 'Free' }}</p>
                </div>
                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <i class="fas fa-crown text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Товары</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-2">0</p>
                </div>
                <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                    <i class="fas fa-box text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Заказы</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white mt-2">0</p>
                </div>
                <div class="p-3 rounded-lg bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Store Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Store Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Информация о магазине</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Название магазина</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $store->name ?? 'Не указано' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Slug</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $store->slug ?? 'Не указано' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">План</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                @if($store->plan === 'Pro') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400
                                @elseif($store->plan === 'Basic') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400 @endif">
                                {{ $store->plan ?? 'Free' }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Тема</label>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $store->theme->name ?? 'По умолчанию' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Domain Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Домены</h3>
                </div>
                <div class="p-6">
                    @if($store->domains->count() > 0)
                        <div class="space-y-4">
                            @foreach($store->domains as $domain)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $domain->domain }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Основной домен</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="http://{{ $domain->domain }}" target="_blank" 
                                       class="text-indigo-600 hover:text-indigo-700 dark:hover:text-indigo-400 p-2 rounded hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <button class="text-gray-600 hover:text-gray-700 dark:hover:text-gray-300 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                <i class="fas fa-globe text-gray-400 text-xl"></i>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Домены не настроены</p>
                            <button class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Добавить домен
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Быстрые действия</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <button class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-box text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-gray-900 dark:text-white">Добавить товар</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Создать новый товар</p>
                            </div>
                        </button>

                        <button class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-palette text-green-600 dark:text-green-400"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-gray-900 dark:text-white">Настроить тему</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Изменить дизайн</p>
                            </div>
                        </button>

                        <button class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-chart-bar text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-gray-900 dark:text-white">Аналитика</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Просмотр статистики</p>
                            </div>
                        </button>

                        <button class="flex items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-cog text-orange-600 dark:text-orange-400"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-gray-900 dark:text-white">Настройки</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Конфигурация магазина</p>
                            </div>
                        </button>

                        <button class="flex items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-trash text-red-600 dark:text-red-400"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-gray-900 dark:text-white">Удалить магазин</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Безвозвратное удаление</p>
                            </div>
                        </button>

                        <button class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-download text-gray-600 dark:text-gray-400"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-gray-900 dark:text-white">Экспорт данных</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Скачать информацию</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Store Status Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Статус магазина</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Статус</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                Активен
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Последняя активность</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ now()->format('d.m.Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Версия</span>
                            <span class="text-sm text-gray-900 dark:text-white">1.0.0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Owner Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Владелец</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-lg">{{ substr($store->owner->name ?? 'U', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $store->owner->name ?? 'Неизвестно' }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $store->owner->email ?? 'Не указан' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Последняя активность</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">Магазин создан</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $store->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">Домен настроен</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $store->created_at->addMinutes(5)->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">Тема применена</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $store->created_at->addMinutes(10)->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
        </div>
    </div>

            <!-- Support Card -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-6 text-white">
                <div class="text-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Нужна помощь?</h3>
                    <p class="text-indigo-100 mb-4">Наша команда поддержки готова помочь вам с любыми вопросами</p>
                    <button class="w-full bg-white/20 hover:bg-white/30 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Связаться с поддержкой
                    </button>
                </div>
        </div>
        </div>
    </div>
</div>
@endsection