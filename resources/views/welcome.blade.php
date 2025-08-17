<!DOCTYPE html>
<html lang="ru" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopCraft - Современный конструктор магазинов</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            light: '#6366f1',
                            DEFAULT: '#4f46e5',
                            dark: '#4338ca',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fade-in': 'fadeIn 0.5s ease-out',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .dark .glass {
            background: rgba(23, 23, 23, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-600 bg-neutral-50 dark:bg-neutral-900 dark:text-gray-400">
    <!-- Курсорные эффекты -->
    <div class="fixed inset-0 pointer-events-none">
        <div id="cursor-effect" class="absolute w-64 h-64 rounded-full bg-indigo-500/10 blur-3xl opacity-0 transition-all duration-500"></div>
    </div>

    <!-- Навигация -->
    <nav class="glass fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center space-x-2">
                    <div class="p-2 rounded-lg bg-indigo-600 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-indigo-500">ShopCraft</span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Возможности</a>
                    <a href="#templates" class="text-sm font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Шаблоны</a>
                    <a href="#pricing" class="text-sm font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Цены</a>
                    <a href="#testimonials" class="text-sm font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Отзывы</a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <button id="theme-toggle" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-neutral-800 transition-colors">
                        <i class="fas fa-moon dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:block"></i>
                    </button>
                    <a href="{{ route('login') }}" class="text-sm font-medium hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors hidden md:block">Вход</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-medium hover:shadow-lg hover:from-indigo-700 hover:to-indigo-600 transition-all">
                        Начать бесплатно
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Герой секция -->
    <section class="pt-32 pb-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8 animate-fade-in">
                <h1 class="text-5xl md:text-6xl font-bold leading-tight text-gray-900 dark:text-white">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-indigo-500">Создайте</span><br>
                    <span>магазин своей мечты</span>
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-lg">
                    Самый современный конструктор интернет-магазинов с искусственным интеллектом. Начните продавать онлайн за 15 минут без технических навыков.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="{{ route('register') }}" class="px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 text-white font-medium hover:shadow-lg hover:shadow-indigo-500/30 hover:from-indigo-700 hover:to-indigo-600 transition-all">
                        Начать бесплатно
                    </a>
                    <a href="#demo" class="px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-700 font-medium hover:bg-gray-100 dark:hover:bg-neutral-800 transition-colors flex items-center space-x-2">
                        <i class="fas fa-play-circle text-indigo-500"></i>
                        <span>Смотреть демо</span>
                    </a>
                </div>
                <div class="flex items-center pt-8 space-x-4">
                    <div class="flex -space-x-2">
                        <img src="https://randomuser.me/api/portraits/women/12.jpg" class="w-10 h-10 rounded-full border-2 border-white dark:border-neutral-900" alt="User">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" class="w-10 h-10 rounded-full border-2 border-white dark:border-neutral-900" alt="User">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" class="w-10 h-10 rounded-full border-2 border-white dark:border-neutral-900" alt="User">
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <p><span class="font-semibold">10,000+</span> предпринимателей уже используют ShopCraft</p>
                    </div>
                </div>
            </div>
            
            <div class="relative">
                <div class="absolute -top-10 -left-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl"></div>
                <div class="relative glass p-1 rounded-2xl shadow-2xl">
                    <img src="https://cdn.dribbble.com/users/383277/screenshots/18055765/media/1a8b7f47c8148a8f9a7f5d8a0e9c5b4e.png" class="rounded-2xl w-full h-auto" alt="Dashboard Preview">
                    <div class="absolute -bottom-6 -right-6 bg-white dark:bg-neutral-800 p-4 rounded-xl shadow-lg">
                        <div class="flex items-center space-x-2">
                            <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg text-green-600 dark:text-green-400">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Доход за месяц</p>
                                <p class="font-bold text-gray-900 dark:text-white">+$12,348</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Логотипы клиентов -->
    <section class="py-12 bg-white dark:bg-neutral-800 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-8">НАМ ДОВЕРЯЮТ</p>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 items-center">
                <img src="https://logo.clearbit.com/nike.com" class="h-8 opacity-60 hover:opacity-100 transition-opacity grayscale hover:grayscale-0" alt="Nike">
                <img src="https://logo.clearbit.com/adidas.com" class="h-10 opacity-60 hover:opacity-100 transition-opacity grayscale hover:grayscale-0" alt="Adidas">
                <img src="https://logo.clearbit.com/spotify.com" class="h-8 opacity-60 hover:opacity-100 transition-opacity grayscale hover:grayscale-0" alt="Spotify">
                <img src="https://logo.clearbit.com/airbnb.com" class="h-8 opacity-60 hover:opacity-100 transition-opacity grayscale hover:grayscale-0" alt="Airbnb">
                <img src="https://logo.clearbit.com/netflix.com" class="h-8 opacity-60 hover:opacity-100 transition-opacity grayscale hover:grayscale-0" alt="Netflix">
                <img src="https://logo.clearbit.com/slack.com" class="h-10 opacity-60 hover:opacity-100 transition-opacity grayscale hover:grayscale-0" alt="Slack">
            </div>
        </div>
    </section>

    <!-- Возможности -->
    <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400">ВОЗМОЖНОСТИ</span>
            <h2 class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">Все, что нужно для успешных продаж</h2>
            <p class="mt-4 text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                ShopCraft предоставляет полный набор инструментов для запуска, управления и масштабирования вашего интернет-магазина.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="glass p-8 rounded-2xl hover:shadow-xl transition-all">
                <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-6">
                    <i class="fas fa-bolt text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">AI-генератор товаров</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Наш ИИ создаст профессиональные описания товаров и SEO-метаданные за секунды, экономя ваше время.
                </p>
            </div>
            
            <div class="glass p-8 rounded-2xl hover:shadow-xl transition-all">
                <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-6">
                    <i class="fas fa-paint-brush text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">3D-конструктор</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Создавайте уникальные дизайны с помощью нашего интуитивного редактора. Никакого кода - только креатив.
                </p>
            </div>
            
            <div class="glass p-8 rounded-2xl hover:shadow-xl transition-all">
                <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-6">
                    <i class="fas fa-globe text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">Мультиязычность</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Продавайте по всему миру с автоматическим переводом и локальными платежными системами.
                </p>
            </div>
            
            <div class="glass p-8 rounded-2xl hover:shadow-xl transition-all">
                <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-6">
                    <i class="fas fa-chart-pie text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">Аналитика в реальном времени</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Получайте детальные отчеты о поведении покупателей и эффективности маркетинга.
                </p>
            </div>
            
            <div class="glass p-8 rounded-2xl hover:shadow-xl transition-all">
                <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-6">
                    <i class="fas fa-robot text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">AI-ассистент</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Персональный помощник подскажет как увеличить продажи и оптимизировать магазин.
                </p>
            </div>
            
            <div class="glass p-8 rounded-2xl hover:shadow-xl transition-all">
                <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-6">
                    <i class="fas fa-mobile-alt text-xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">Мобильное приложение</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Управляйте магазином с телефона. Получайте уведомления о заказах в реальном времени.
                </p>
            </div>
        </div>
    </section>

    <!-- Демо видео -->
    <section id="demo" class="py-20 bg-gradient-to-r from-indigo-600 to-indigo-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="glass max-w-4xl mx-auto rounded-2xl overflow-hidden shadow-2xl relative">
                <div class="aspect-w-16 aspect-h-9 bg-black">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <button class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center hover:scale-110 transition-transform">
                            <i class="fas fa-play text-white text-2xl"></i>
                        </button>
                    </div>
                    <img src="https://cdn.dribbble.com/users/1787323/screenshots/14168396/media/5d0cd8415005e2bb2b8b8260d7f6d4e5.png" class="w-full h-full object-cover" alt="Video Thumbnail">
                </div>
                <div class="p-6 text-left">
                    <h3 class="text-xl font-bold text-white">Как работает ShopCraft</h3>
                    <p class="mt-2 text-indigo-100">Посмотрите 2-минутное видео о том, как создать магазин за 15 минут</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Шаблоны -->
    <section id="templates" class="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400">ШАБЛОНЫ</span>
            <h2 class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">Современные дизайны для любого бизнеса</h2>
            <p class="mt-4 text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Выберите из 50+ профессиональных шаблонов, которые можно полностью настроить под ваш бренд.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all">
                <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
                    <img src="https://cdn.dribbble.com/users/15687/screenshots/18131040/media/4a4a1f8e8a8e0e0e0e0e0e0e0e0e0e0.png" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Template 1">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6 opacity-0 group-hover:opacity-100 transition-opacity">
                    <div>
                        <h3 class="text-white font-bold text-xl">Minimal</h3>
                        <p class="text-white/80 mt-1">Для модных брендов</p>
                        <button class="mt-3 px-4 py-2 bg-white text-gray-900 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors">
                            Выбрать шаблон
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all">
                <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
                    <img src="https://cdn.dribbble.com/users/15687/screenshots/18131040/media/4a4a1f8e8a8e0e0e0e0e0e0e0e0e0e0.png" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Template 2">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6 opacity-0 group-hover:opacity-100 transition-opacity">
                    <div>
                        <h3 class="text-white font-bold text-xl">Bold</h3>
                        <p class="text-white/80 mt-1">Для технологичных продуктов</p>
                        <button class="mt-3 px-4 py-2 bg-white text-gray-900 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors">
                            Выбрать шаблон
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all">
                <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
                    <img src="https://cdn.dribbble.com/users/15687/screenshots/18131040/media/4a4a1f8e8a8e0e0e0e0e0e0e0e0e0e0.png" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Template 3">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6 opacity-0 group-hover:opacity-100 transition-opacity">
                    <div>
                        <h3 class="text-white font-bold text-xl">Elegant</h3>
                        <p class="text-white/80 mt-1">Для премиум брендов</p>
                        <button class="mt-3 px-4 py-2 bg-white text-gray-900 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors">
                            Выбрать шаблон
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-12">
            <a href="#" class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-700 rounded-xl font-medium hover:bg-gray-100 dark:hover:bg-neutral-800 transition-colors">
                <span>Посмотреть все шаблоны</span>
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </section>

    <!-- Отзывы -->
    <section id="testimonials" class="py-20 bg-gray-100 dark:bg-neutral-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400">ОТЗЫВЫ</span>
                <h2 class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">Что говорят наши клиенты</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="glass p-8 rounded-2xl">
                    <div class="flex items-center mb-4">
                        <div class="flex items-center space-x-1 text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        "ShopCraft полностью изменил мой бизнес. За месяц мои продажи выросли на 200%. Очень рекомендую!"
                    </p>
                    <div class="flex items-center">
                        <img src="https://randomuser.me/api/portraits/women/32.jpg" class="w-10 h-10 rounded-full mr-4" alt="User">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Анна К.</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Владелец магазина одежды</p>
                        </div>
                    </div>
                </div>
                
                <div class="glass p-8 rounded-2xl">
                    <div class="flex items-center mb-4">
                        <div class="flex items-center space-x-1 text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        "Никогда не думал, что создание магазина может быть таким простым. Техподдержка просто супер - помогают 24/7."
                    </p>
                    <div class="flex items-center">
                        <img src="https://randomuser.me/api/portraits/men/54.jpg" class="w-10 h-10 rounded-full mr-4" alt="User">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Дмитрий С.</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Основатель бренда электроники</p>
                        </div>
                    </div>
                </div>
                
                <div class="glass p-8 rounded-2xl">
                    <div class="flex items-center mb-4">
                        <div class="flex items-center space-x-1 text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        "AI-ассистент просто волшебник! Он предложил мне стратегию продвижения, которая увеличила конверсию на 35%."
                    </p>
                    <div class="flex items-center">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" class="w-10 h-10 rounded-full mr-4" alt="User">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Ольга М.</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Владелец ювелирного магазина</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Цены -->
    <section id="pricing" class="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400">ЦЕНЫ</span>
            <h2 class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">Гибкие тарифы для любого бизнеса</h2>
            <p class="mt-4 text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Начните бесплатно и обновляйтесь по мере роста вашего магазина.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <div class="glass p-8 rounded-2xl hover:shadow-xl transition-all">
                <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">Старт</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Для тестирования идеи</p>
                <div class="mb-6">
                    <span class="text-4xl font-bold text-gray-900 dark:text-white">$0</span>
                    <span class="text-gray-500">/месяц</span>
                </div>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span class="text-gray-600 dark:text-gray-400">До 10 товаров</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span class="text-gray-600 dark:text-gray-400">Базовый шаблон</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span class="text-gray-600 dark:text-gray-400">Онлайн-платежи</span>
                    </li>
                    <li class="flex items-center text-gray-400">
                        <i class="fas fa-times text-red-400 mr-2"></i>
                        <span class="text-gray-600 dark:text-gray-400">AI-ассистент</span>
                    </li>
                </ul>
                <a href="{{ route('register') }}" class="block w-full py-3 px-4 text-center rounded-lg border border-gray-300 dark:border-gray-700 font-medium hover:bg-gray-100 dark:hover:bg-neutral-800 transition-colors">
                    Начать бесплатно
                </a>
            </div>
            
            <div class="glass p-8 rounded-2xl border-2 border-indigo-500 relative hover:shadow-xl transition-all">
                <div class="absolute top-0 right-0 bg-indigo-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">ПОПУЛЯРНЫЙ</div>
                <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">Профи</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Для растущего бизнеса</p>
                <div class="mb-6">
                    <span class="text-4xl font-bold text-gray-900 dark:text-white">$29</span>
                    <span class="text-gray-500">/месяц</span>
                </div>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span class="text-gray-600 dark:text-gray-400">До 100 товаров</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span class="text-gray-600 dark:text-gray-400">Все шаблоны</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span class="text-gray-600 dark:text-gray-400">Расширенная аналитика</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span class="text-gray-600 dark:text-gray-400">AI-ассистент</span>
                    </li>
                </ul>
                <a href="{{ route('register') }}" class="block w-full py-3 px-4 text-center rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-500 text-white font-medium hover:shadow-lg hover:shadow-indigo-500/30 hover:from-indigo-700 hover:to-indigo-600 transition-all">
                    Попробовать 14 дней
                </a>
            </div>
            
            <div class="glass p-8 rounded-2xl hover:shadow-xl transition-all">
                <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">Премиум</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Для масштабирования</p>
                <div class="mb-6">
                    <span class="text-4xl font-bold text-gray-900 dark:text-white">$99</span>
                    <span class="text-gray-500">/месяц</span>
                </div>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span class="text-gray-600 dark:text-gray-400">Неограниченные товары</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span class="text-gray-600 dark:text-gray-400">Приоритетная поддержка</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span class="text-gray-600 dark:text-gray-400">Персональный менеджер</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span class="text-gray-600 dark:text-gray-400">Расширенный AI</span>
                    </li>
                </ul>
                <a href="{{ route('register') }}" class="block w-full py-3 px-4 text-center rounded-lg border border-gray-300 dark:border-gray-700 font-medium hover:bg-gray-100 dark:hover:bg-neutral-800 transition-colors">
                    Обсудить детали
                </a>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-6">Готовы начать продавать онлайн?</h2>
            <p class="text-xl text-indigo-100 mb-8">Создайте свой магазин сегодня и получите 14 дней бесплатного периода.</p>
            <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-white text-gray-900 rounded-xl font-bold hover:bg-gray-100 transition-colors shadow-lg">
                <span>Начать бесплатно</span>
                <i class="fas fa-arrow-right ml-3"></i>
            </a>
        </div>
    </section>

    <!-- Футер -->
    <footer class="bg-white dark:bg-neutral-900">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
                <div class="col-span-2">
                    <div class="flex items-center space-x-2">
                        <div class="p-2 rounded-lg bg-indigo-600 text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-indigo-500">ShopCraft</span>
                    </div>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">
                        Самый современный конструктор интернет-магазинов с искусственным интеллектом.
                    </p>
                    <div class="flex space-x-4 mt-6">
                        <a href="#" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Продукт</h3>
                    <ul class="mt-4 space-y-3">
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">Возможности</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">Шаблоны</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">Цены</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">Интеграции</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Ресурсы</h3>
                    <ul class="mt-4 space-y-3">
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">Документация</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">Блог</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">Вебинары</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">Поддержка</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Компания</h3>
                    <ul class="mt-4 space-y-3">
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">О нас</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">Карьера</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">Контакты</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">Партнеры</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-800 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-sm">
                    © 2023 ShopCraft. Все права защищены.
                </p>
                <div class="mt-4 md:mt-0 flex space-x-6">
                    <a href="#" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-sm">Политика конфиденциальности</a>
                    <a href="#" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-sm">Условия использования</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Тема
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;
        
        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
        });
        
        // Проверяем сохраненную тему
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        }
        
        // Курсор эффект
        document.addEventListener('mousemove', (e) => {
            const cursorEffect = document.getElementById('cursor-effect');
            cursorEffect.style.left = `${e.clientX - 128}px`;
            cursorEffect.style.top = `${e.clientY - 128}px`;
            cursorEffect.style.opacity = '1';
        });
        
        // Плавная прокрутка
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        
        // Анимация при скролле
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('section').forEach(section => {
            observer.observe(section);
        });
    </script>
</body>
</html>