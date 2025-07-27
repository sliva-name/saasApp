<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Магазин')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/tenant.js'])
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">




<!-- Vue App -->
    <div id="tenant-app">
        <!-- Компонент Vue будет смонтирован сюда -->
    </div>

<!-- Футер -->
<footer class="bg-gray-900 mt-12 flex-0">
    <div class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="flex flex-col items-center justify-center text-teal-600">
            <img width="200" src="{{ asset('logo.png') }}" alt="logo-footer">

            <p class="mx-auto mt-6 max-w-md text-center leading-relaxed text-gray-400">
                Все права защищены. Сайт запрещен для копирования.
            </p>

            <ul class="mt-12 flex flex-wrap justify-center gap-6 md:gap-8 lg:gap-12">
                <li>
                    <a class="text-gray-400 transition hover:text-white" href="/about">
                        О нас
                    </a>
                </li>

                <li>
                    <a class="text-gray-400 transition hover:text-white" href="/">
                        Способы оплаты
                    </a>
                </li>

                <li>
                    <a class="text-gray-400 transition hover:text-white" href="/">
                        Возврат товара
                    </a>
                </li>

                <li>
                    <a class="text-gray-400 transition hover:text-white" href="/">
                        Возврат денежных средств
                    </a>
                </li>

                <li>
                    <a class="text-gray-400 transition hover:text-white" href="/contacts">
                        Контакты
                    </a>
                </li>

                <li>
                    <a class="text-gray-400 transition hover:text-white" href="/">
                        Реквизиты
                    </a>
                </li>
            </ul>
        </div>
    </div>
</footer>


</body>
</html>
