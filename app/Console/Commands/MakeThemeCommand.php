<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeThemeCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'make:theme 
                           {name : The name of the theme}
                           {--author= : Author of the theme}
                           {--description= : Description of the theme}
                           {--package= : Package name (e.g., themes/my-theme)}
                           {--force : Overwrite existing theme}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new theme package with boilerplate files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $author = $this->option('author') ?? 'Unknown';
        $description = $this->option('description') ?? "Custom theme: {$name}";
        $packageName = $this->option('package') ?? 'themes/' . Str::slug($name);
        
        // Проверяем package name format
        if (!preg_match('/^themes\/[a-z0-9\-]+$/', $packageName)) {
            $this->error('Package name must be in format: themes/theme-name (lowercase, hyphens only)');
            return Command::FAILURE;
        }

        $themePath = base_path($packageName);
        
        // Проверяем, существует ли уже тема
        if (File::exists($themePath) && !$this->option('force')) {
            $this->error("Theme already exists at: {$themePath}");
            $this->info('Use --force to overwrite existing theme.');
            return Command::FAILURE;
        }

        $this->info("Creating theme: {$name}");
        $this->info("Package: {$packageName}");
        $this->info("Path: {$themePath}");

        try {
            $this->createThemeStructure($themePath, $name, $packageName, $author, $description);
            $this->info('Theme created successfully!');
            
            $this->line('');
            $this->info('Next steps:');
            $this->line('1. Customize your theme files in: ' . $themePath);
            $this->line('2. Run: php artisan theme:manage scan');
            $this->line('3. Run: php artisan theme:manage activate ' . $packageName);
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to create theme: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    /**
     * Создать структуру темы
     */
    private function createThemeStructure(
        string $themePath, 
        string $name, 
        string $packageName, 
        string $author, 
        string $description
    ): void {
        // Создаем основные директории
        $directories = [
            $themePath,
            "{$themePath}/config",
            "{$themePath}/resources",
            "{$themePath}/resources/js",
            "{$themePath}/resources/js/components",
            "{$themePath}/resources/js/pages", 
            "{$themePath}/resources/js/composables",
            "{$themePath}/resources/js/styles",
            "{$themePath}/resources/css",
            "{$themePath}/resources/images",
            "{$themePath}/docs",
        ];

        foreach ($directories as $dir) {
            File::makeDirectory($dir, 0755, true);
        }

        // Создаем манифест темы
        $this->createThemeManifest($themePath, $name, $packageName, $author, $description);
        
        // Создаем конфигурацию по умолчанию
        $this->createDefaultConfig($themePath);
        
        // Создаем основные файлы
        $this->createIndexFile($themePath, $name);
        $this->createTenantAppComponent($themePath, $name);
        $this->createNavigationComponent($themePath);
        $this->createThemeStyles($themePath, $name);
        $this->createReadme($themePath, $name, $description);
        
        $this->line("✓ Created theme structure");
    }

    /**
     * Создать манифест темы
     */
    private function createThemeManifest(
        string $themePath, 
        string $name, 
        string $packageName, 
        string $author, 
        string $description
    ): void {
        $slug = Str::slug($name);
        $manifest = [
            'name' => $name,
            'package_name' => $packageName,
            'slug' => $slug,
            'version' => '1.0.0',
            'author' => $author,
            'description' => $description,
            'entry_point' => 'index.js',
            'preview_image' => "/{$packageName}/preview.jpg",
            'is_system' => false,
            'is_active' => true,
            'features' => [
                'product_catalog',
                'product_search',
                'shopping_cart',
                'user_authentication',
                'responsive_design',
                'custom_styling'
            ],
            'requirements' => [
                'php' => '>=8.2',
                'laravel' => '>=11.0',
                'vue' => '>=3.3'
            ],
            'config_schema' => [
                'colors' => [
                    'type' => 'object',
                    'properties' => [
                        'primary' => [
                            'type' => 'string',
                            'format' => 'color',
                            'default' => '#3b82f6',
                            'title' => 'Основной цвет'
                        ],
                        'secondary' => [
                            'type' => 'string',
                            'format' => 'color',
                            'default' => '#64748b',
                            'title' => 'Вторичный цвет'
                        ]
                    ]
                ]
            ]
        ];

        File::put(
            "{$themePath}/theme.json",
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        
        $this->line("✓ Created theme.json");
    }

    /**
     * Создать конфигурацию по умолчанию
     */
    private function createDefaultConfig(string $themePath): void
    {
        $config = [
            'colors' => [
                'primary' => '#3b82f6',
                'secondary' => '#64748b',
                'accent' => '#ec4899',
                'background' => '#ffffff',
                'surface' => '#f8fafc',
                'text' => '#1e293b'
            ],
            'layout' => [
                'header_fixed' => true,
                'sidebar_enabled' => true,
                'footer_enabled' => true
            ],
            'typography' => [
                'font_family' => 'Inter, sans-serif',
                'font_size_base' => '16px'
            ]
        ];

        File::put(
            "{$themePath}/config/default.json",
            json_encode($config, JSON_PRETTY_PRINT)
        );
        
        $this->line("✓ Created default config");
    }

    /**
     * Создать основной index.js файл
     */
    private function createIndexFile(string $themePath, string $name): void
    {
        $content = <<<JS
/**
 * {$name} Theme - Entry Point
 */

// Импортируем основные компоненты
import TenantApp from './components/TenantApp.vue'
import Navigation from './components/Navigation.vue'

// Импортируем стили
import './styles/theme.css'

/**
 * Карта компонентов темы
 */
export const components = {
  TenantApp,
  Navigation,
}

/**
 * Маршруты темы
 */
export const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('./pages/HomePage.vue'),
  },
]

/**
 * Хуки жизненного цикла темы
 */
export const hooks = {
  beforeMount(app) {
    console.log('[{$name} Theme] Initializing...')
  },
  
  mounted(app) {
    console.log('[{$name} Theme] Mounted successfully')
    document.body.classList.add('theme-{$this->getSlugFromName($name)}')
  },
  
  beforeUnmount(app) {
    console.log('[{$name} Theme] Unmounting...')
    document.body.classList.remove('theme-{$this->getSlugFromName($name)}')
  }
}

/**
 * Конфигурация темы
 */
export const config = {
  name: '{$name}',
  version: '1.0.0',
  features: [
    'product_catalog',
    'product_search',
    'shopping_cart',
    'user_authentication',
    'responsive_design',
    'custom_styling'
  ]
}

/**
 * Экспорт по умолчанию
 */
export default {
  components,
  routes,
  hooks,
  config
}
JS;

        File::put("{$themePath}/resources/js/index.js", $content);
        $this->line("✓ Created index.js");
    }

    /**
     * Создать основной компонент приложения
     */
    private function createTenantAppComponent(string $themePath, string $name): void
    {
        $slug = $this->getSlugFromName($name);
        $content = <<<VUE
<template>
  <div class="theme-{$slug}">
    <!-- Навигация -->
    <Navigation />
    
    <!-- Основной контент -->
    <main class="main-content">
      <div class="container">
        <router-view />
      </div>
    </main>
    
    <!-- Футер -->
    <footer class="footer">
      <div class="container">
        <p>&copy; 2024 {$name}. Все права защищены.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import Navigation from './Navigation.vue'

onMounted(() => {
  console.log('[{$name}] Theme loaded')
})
</script>

<style scoped>
.theme-{$slug} {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.footer {
  background-color: #f8fafc;
  padding: 1rem 0;
  text-align: center;
  color: #64748b;
}
</style>
VUE;

        File::put("{$themePath}/resources/js/components/TenantApp.vue", $content);
        $this->line("✓ Created TenantApp component");
    }

    /**
     * Создать компонент навигации
     */
    private function createNavigationComponent(string $themePath): void
    {
        $content = <<<VUE
<template>
  <nav class="navigation">
    <div class="nav-container">
      <div class="nav-brand">
        <router-link to="/" class="brand-link">
          Мой Магазин
        </router-link>
      </div>
      
      <div class="nav-menu">
        <router-link to="/" class="nav-link">Каталог</router-link>
        <router-link to="/cart" class="nav-link">Корзина</router-link>
        <router-link to="/account" class="nav-link">Аккаунт</router-link>
      </div>
    </div>
  </nav>
</template>

<script setup>
// Логика навигации
</script>

<style scoped>
.navigation {
  background-color: white;
  border-bottom: 1px solid #e2e8f0;
  padding: 1rem 0;
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.brand-link {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1e293b;
  text-decoration: none;
}

.nav-menu {
  display: flex;
  gap: 2rem;
}

.nav-link {
  color: #64748b;
  text-decoration: none;
  transition: color 0.2s;
}

.nav-link:hover {
  color: #3b82f6;
}
</style>
VUE;

        File::put("{$themePath}/resources/js/components/Navigation.vue", $content);
        $this->line("✓ Created Navigation component");
    }

    /**
     * Создать стили темы
     */
    private function createThemeStyles(string $themePath, string $name): void
    {
        $slug = $this->getSlugFromName($name);
        $content = <<<CSS
/**
 * {$name} Theme Styles
 */

/* Переменные темы */
.theme-{$slug} {
  --color-primary: #3b82f6;
  --color-secondary: #64748b;
  --color-accent: #ec4899;
  --color-background: #ffffff;
  --color-surface: #f8fafc;
  --color-text: #1e293b;
  
  --font-family: 'Inter', sans-serif;
  --border-radius: 0.375rem;
  --shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Базовые стили */
.theme-{$slug} {
  font-family: var(--font-family);
  color: var(--color-text);
  background-color: var(--color-background);
}

/* Кнопки */
.theme-{$slug} .btn {
  padding: 0.5rem 1rem;
  border-radius: var(--border-radius);
  border: none;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

.theme-{$slug} .btn-primary {
  background-color: var(--color-primary);
  color: white;
}

.theme-{$slug} .btn-primary:hover {
  background-color: color-mix(in srgb, var(--color-primary) 90%, black);
}

/* Карточки */
.theme-{$slug} .card {
  background-color: var(--color-surface);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  padding: 1rem;
}

/* Адаптивность */
@media (max-width: 768px) {
  .theme-{$slug} .nav-container {
    flex-direction: column;
    gap: 1rem;
  }
}
CSS;

        File::put("{$themePath}/resources/js/styles/theme.css", $content);
        $this->line("✓ Created theme styles");
    }

    /**
     * Создать README
     */
    private function createReadme(string $themePath, string $name, string $description): void
    {
        $content = <<<MD
# {$name}

{$description}

## Установка

1. Сканируйте темы:
```bash
php artisan theme:manage scan
```

2. Активируйте тему:
```bash
php artisan theme:manage activate themes/{$this->getSlugFromName($name)}
```

## Разработка

Файлы темы находятся в директории:
- `resources/js/components/` - Vue компоненты
- `resources/js/pages/` - Страницы
- `resources/js/styles/` - Стили
- `config/` - Конфигурация

## Конфигурация

Настройки темы можно изменить через API или админ-панель.

## Компоненты

- `TenantApp.vue` - Основной компонент приложения
- `Navigation.vue` - Навигация

## Возможности

- Адаптивный дизайн
- Кастомизация цветов
- Современные стили
- Оптимизация производительности
MD;

        File::put("{$themePath}/README.md", $content);
        $this->line("✓ Created README.md");
    }

    /**
     * Получить slug из имени
     */
    private function getSlugFromName(string $name): string
    {
        return Str::slug($name);
    }
}
