@extends('layouts.app')

@section('title', 'Главная')

@section('content')
<div class="space-y-6">
    <!-- Приветствие -->
    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Добро пожаловать, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-600 dark:text-gray-300">Здесь вы можете управлять своими магазинами и настройками.</p>
    </div>

    <!-- Виджеты -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-dashboard-widget 
            title="Магазины" 
            value="{{ auth()->user()->stores->count() }}" 
            icon="fas fa-store" 
            color="indigo" 
            trend="up" 
            change="2 за месяц" 
        />
        
        <x-dashboard-widget 
            title="Посетители" 
            value="1,248" 
            icon="fas fa-users" 
            color="green" 
            trend="up" 
            change="12% за неделю" 
        />
        
        <x-dashboard-widget 
            title="Заказы" 
            value="86" 
            icon="fas fa-shopping-cart" 
            color="blue" 
            trend="down" 
            change="5% за месяц" 
        />
    </div>

    <!-- Быстрые действия -->
    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Быстрые действия</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-quick-action 
                icon="fas fa-plus" 
                label="Магазин" 
                href="{{ route('stores.create') }}" 
                color="indigo" 
            />
            <x-quick-action 
                icon="fas fa-book" 
                label="Документация" 
                href="#" 
                color="purple" 
            />
            <x-quick-action 
                icon="fas fa-video" 
                label="Видеоуроки" 
                href="#" 
                color="red" 
            />
            <x-quick-action 
                icon="fas fa-headset" 
                label="Поддержка" 
                href="#" 
                color="green" 
            />
        </div>
    </div>
</div>
@endsection