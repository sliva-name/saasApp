<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Ошибка 404 — Магазин не найден</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css']) {{-- Подключение Tailwind --}}
</head>
<body class="bg-gray-50 text-gray-800 flex items-center justify-center min-h-screen">

<div class="max-w-xl w-full px-6 text-center animate-fade-in">
    {{-- Иллюстрация --}}
    <div class="mb-10">
        <svg class="mx-auto w-48 h-48" viewBox="0 0 500 300" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 200C120 180 150 180 180 200C210 220 240 220 270 200C300 180 330 180 360 200" stroke="#3B82F6" stroke-width="8" stroke-linecap="round"/>
            <circle cx="150" cy="150" r="10" fill="#EF4444"/>
            <circle cx="350" cy="150" r="10" fill="#EF4444"/>
            <path d="M200 250Q250 280 300 250" stroke="#9CA3AF" stroke-width="6" fill="none"/>
            <text x="170" y="120" fill="#1F2937" font-size="50" font-weight="bold">404</text>
        </svg>
    </div>

    {{-- Заголовок --}}
    <h1 class="text-4xl font-extrabold text-gray-900 mb-3">Магазин не найден</h1>

    {{-- Подзаголовок --}}
    <p class="text-gray-600 text-base leading-relaxed mb-8">
        Поддомен <strong class="text-gray-800">{{ request()->getHost() }}</strong> не соответствует ни одному магазину.<br>
        Возможно, он был удалён или адрес введён с ошибкой.
    </p>

    {{-- Кнопка --}}
    <a href="{{ config('app.url') }}"
       class="inline-block px-6 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition duration-200">
        Вернуться на главную
    </a>
</div>

</body>
</html>
