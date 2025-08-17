<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'SaaS Platform') }} | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body class="h-full bg-neutral-50 dark:bg-neutral-900 text-gray-600 dark:text-gray-400 transition-colors duration-200">
    <div class="min-h-full flex flex-col">
        <div class="flex flex-1 overflow-hidden">
            @include('partials.sidebar')
            <main class="flex-1 overflow-y-auto p-6 bg-neutral-50 dark:bg-neutral-900 transition-colors duration-200">
                @include('partials.notifications')
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
