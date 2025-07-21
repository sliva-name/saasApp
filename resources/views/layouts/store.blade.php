<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Магазин')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/tenant.js'])
</head>
<body class="bg-gray-50 text-gray-800">

<!-- Хедер -->
<header class="bg-white shadow py-4 mb-6">
    <div class="container mx-auto flex justify-between items-center px-4">
        <a href="{{ route('store.index') }}" class="text-2xl font-bold text-blue-600">
            Мой Магазин
        </a>

        <nav class="space-x-4 text-gray-600">
            <a href="{{ route('store.index') }}" class="hover:underline">Главная</a>
            {{-- Можно добавить ссылку на корзину, личный кабинет и т.п. --}}
        </nav>
    </div>
</header>

<!-- Vue App -->
<main class="container mx-auto px-4">
    <div id="tenant-app">
        <!-- Компонент Vue будет смонтирован сюда -->
    </div>
</main>

<!-- Футер -->
<footer class="mt-12 bg-gray-100 py-6 text-center text-sm text-gray-500">
    &copy; {{ now()->year }} Мой Магазин. Все права защищены.
</footer>

</body>
</html>
