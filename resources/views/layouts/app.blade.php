<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'SaaS') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- если используешь Vite --}}
</head>
<body class="bg-gray-100 text-gray-800">
<nav class="bg-white shadow mb-6">
    <div class="container mx-auto px-4 py-4 flex justify-between">
        <a href="/" class="text-xl font-bold">SaaS</a>
        @auth
            <div class="space-x-4">
                <a href="{{ route('stores.create') }}" class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Создать магазин</a>
                <span>{{ auth()->user()->name }}</span>
            </div>
        @endauth
    </div>
</nav>

<main class="container mx-auto px-4">
    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
