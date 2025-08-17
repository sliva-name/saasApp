@extends('layouts.app')
@section('sidebar')
<!-- Sidebar -->
@include('components.sidebar', ['stores' => $stores])
@endsection
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-store mr-3 text-indigo-500"></i> Управление магазинами
        </h1>
        <div class="flex space-x-3">
            <div class="relative">
                <input type="text" placeholder="Поиск магазинов..." class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-64">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors duration-200 flex items-center">
                <i class="fas fa-filter mr-2"></i> Фильтры
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-neutral-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Всего магазинов</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-900 dark:text-white">{{ count($stores) }}</h3>
                </div>
                <div class="p-3 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                    <i class="fas fa-store"></i>
                </div>
            </div>
            <p class="text-xs text-green-500 dark:text-green-400 mt-3 flex items-center">
                <i class="fas fa-arrow-up mr-1"></i><span>12% за месяц</span>
            </p>
        </div>
        
        <div class="bg-white dark:bg-neutral-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Активные</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-900 dark:text-white">{{ count($stores) }}</h3>
                </div>
                <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <p class="text-xs text-green-500 dark:text-green-400 mt-3 flex items-center">
                <i class="fas fa-arrow-up mr-1"></i><span>5% за месяц</span>
            </p>
        </div>
        
        <div class="bg-white dark:bg-neutral-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Посещений</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-900 dark:text-white">1,248</h3>
                </div>
                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <p class="text-xs text-red-500 dark:text-red-400 mt-3 flex items-center">
                <i class="fas fa-arrow-down mr-1"></i><span>3% за месяц</span>
            </p>
        </div>
        
        <div class="bg-white dark:bg-neutral-800 rounded-xl p-5 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Доход</p>
                    <h3 class="text-2xl font-bold mt-1 text-gray-900 dark:text-white">$3,845</h3>
                </div>
                <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <p class="text-xs text-green-500 dark:text-green-400 mt-3 flex items-center">
                <i class="fas fa-arrow-up mr-1"></i><span>18% за месяц</span>
            </p>
        </div>
    </div>

    <!-- Stores Table -->
    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-neutral-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Магазин</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Домен</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Статус</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Дата создания</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Активность</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Действия</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-neutral-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($stores as $store)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-indigo-300 flex items-center justify-center text-white">
                                    <i class="fas fa-store text-sm"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $store->name ?? 'Магазин ' . $store->id }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $store->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $store->domains->first()->domain }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $store->domains->count() }} поддоменов</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 flex items-center w-fit">
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span> Активен
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $store->created_at->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">Сегодня, {{ now()->format('H:i') }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ rand(50, 500) }} посещений</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-400 p-2 rounded hover:bg-indigo-50/50 dark:hover:bg-indigo-900/20 transition-colors" title="Просмотр">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="#" class="text-gray-600 hover:text-gray-900 dark:hover:text-gray-300 p-2 rounded hover:bg-gray-100 dark:hover:bg-neutral-700 transition-colors" title="Редактировать">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="text-purple-600 hover:text-purple-900 dark:hover:text-purple-400 p-2 rounded hover:bg-purple-50/50 dark:hover:bg-purple-900/20 transition-colors" title="Настройки">
                                    <i class="fas fa-cog"></i>
                                </a>
                                <a href="#" class="text-red-600 hover:text-red-900 dark:hover:text-red-400 p-2 rounded hover:bg-red-50/50 dark:hover:bg-red-900/20 transition-colors" title="Удалить">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between mt-6">
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Показано с <span class="font-medium">1</span> по <span class="font-medium">10</span> из <span class="font-medium">{{ count($stores) }}</span> магазинов
        </div>
        <div class="flex space-x-2">
            {{ $stores->links() }}
        </div>
    </div>
</div>
@endsection