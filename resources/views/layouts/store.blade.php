<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Магазин')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">
<header class="bg-white shadow p-4 mb-6">
    <div class="container mx-auto flex justify-between">
        <h1 class="text-xl font-bold">Мой магазин</h1>
    </div>
</header>

<main class="container mx-auto px-4">
    @yield('content')
</main>
</body>
</html>
