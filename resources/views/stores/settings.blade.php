@extends('layouts.app')

@section('title', 'Настройки магазинов')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Настройки магазинов</h2>
                    <a href="{{ route('stores.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Создать магазин
                    </a>
                </div>

                @if($stores->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($stores as $store)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $store->name }}</h3>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            @if($store->status === 'active') bg-green-100 text-green-800
                                            @elseif($store->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($store->status ?? 'active') }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <p><strong>Домен:</strong> {{ $store->domains->first()->domain ?? 'Не настроен' }}</p>
                                        <p><strong>Тема:</strong> {{ $store->theme->name ?? 'По умолчанию' }}</p>
                                        <p><strong>Создан:</strong> {{ $store->created_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                    
                                    <div class="mt-4 flex space-x-2">
                                        <a href="{{ route('stores.show', $store) }}" 
                                           class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 text-center py-2 px-4 rounded text-sm transition-colors">
                                            Просмотр
                                        </a>
                                        <a href="#" 
                                           class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center py-2 px-4 rounded text-sm transition-colors">
                                            Настройки
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">У вас пока нет магазинов</h3>
                        <p class="text-gray-600 mb-6">Создайте свой первый магазин, чтобы начать продажи</p>
                        <a href="{{ route('stores.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                            Создать первый магазин
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
