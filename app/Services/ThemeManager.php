<?php

namespace App\Services;

use App\Models\Theme;
use App\Models\ThemeConfig;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Facades\Tenancy;

class ThemeManager
{
    protected string $themesPath;
    protected ?ThemeConfig $currentConfig = null;

    public function __construct()
    {
        $this->themesPath = base_path('themes');
    }

    /**
     * Получить все доступные темы
     */
    public function getAvailableThemes(): Collection
    {
        return $this->central(function () {
            return Theme::active()->get();
        });
    }

    /**
     * Получить тему по package_name
     */
    public function getTheme(string $packageName): ?Theme
    {
        return $this->central(function () use ($packageName) {
            return Theme::where('package_name', $packageName)->first();
        });
    }

    /**
     * Получить активную тему для текущего тенанта
     */
    public function getCurrentTheme(): ?Theme
    {
        $config = $this->getCurrentConfig();
        
        if (!$config) {
            return $this->getDefaultTheme();
        }

        return $this->getTheme($config->theme_package_name);
    }

    /**
     * Получить активную конфигурацию темы для текущего тенанта
     */
    public function getCurrentConfig(): ?ThemeConfig
    {
        if ($this->currentConfig === null) {
            $this->currentConfig = ThemeConfig::active()->first();
        }

        return $this->currentConfig;
    }

    /**
     * Получить тему по умолчанию
     */
    public function getDefaultTheme(): ?Theme
    {
        return $this->central(function () {
            return Theme::where('is_system', true)
                       ->where('package_name', 'themes/default')
                       ->first();
        });
    }

    /**
     * Активировать тему для текущего тенанта
     */
    public function activateTheme(string $packageName, array $config = []): ThemeConfig
    {
        // Проверяем, что тема существует и доступна
        $theme = $this->getTheme($packageName);
        
        if (!$theme || !$theme->is_active) {
            throw new \InvalidArgumentException("Theme {$packageName} not found or not active");
        }

        // Проверяем совместимость
        if (!$theme->isCompatible()) {
            throw new \InvalidArgumentException("Theme {$packageName} is not compatible with current system");
        }

        // Ищем существующую конфигурацию или создаем новую
        $themeConfig = ThemeConfig::where('theme_package_name', $packageName)->first();
        
        if (!$themeConfig) {
            $themeConfig = ThemeConfig::create([
                'theme_package_name' => $packageName,
                'config' => array_merge($theme->getDefaultConfig(), $config),
                'is_active' => false
            ]);
        } else {
            // Обновляем конфигурацию
            $currentConfig = $themeConfig->config ?? [];
            $themeConfig->update([
                'config' => array_merge($currentConfig, $config)
            ]);
        }

        // Активируем тему
        $themeConfig->activate();

        // Сбрасываем кеш
        $this->clearCache();

        return $themeConfig;
    }

    /**
     * Установить новую тему из пакета
     */
    public function installTheme(string $packagePath): Theme
    {
        if (!File::exists($packagePath)) {
            throw new \InvalidArgumentException("Theme package not found at {$packagePath}");
        }

        // Читаем манифест темы
        $manifestPath = $packagePath . '/theme.json';
        if (!File::exists($manifestPath)) {
            throw new \InvalidArgumentException("Theme manifest not found");
        }

        $manifest = json_decode(File::get($manifestPath), true);
        if (!$manifest) {
            throw new \InvalidArgumentException("Invalid theme manifest");
        }

        // Валидируем обязательные поля
        $requiredFields = ['name', 'package_name', 'version'];
        foreach ($requiredFields as $field) {
            if (!isset($manifest[$field])) {
                throw new \InvalidArgumentException("Required field '{$field}' missing in theme manifest");
            }
        }

        // Проверяем, не установлена ли уже тема
        $existingTheme = $this->getTheme($manifest['package_name']);
        if ($existingTheme) {
            throw new \InvalidArgumentException("Theme {$manifest['package_name']} already installed");
        }

        // Копируем файлы темы
        $targetPath = $this->themesPath . '/' . $manifest['package_name'];
        File::copyDirectory($packagePath, $targetPath);

        // Создаем запись в базе данных
        return $this->central(function () use ($manifest) {
            return Theme::create([
                'name' => $manifest['name'],
                'slug' => $manifest['slug'] ?? str_replace('/', '-', $manifest['package_name']),
                'package_name' => $manifest['package_name'],
                'version' => $manifest['version'],
                'author' => $manifest['author'] ?? null,
                'description' => $manifest['description'] ?? null,
                'features' => $manifest['features'] ?? [],
                'config_schema' => $manifest['config_schema'] ?? [],
                'requirements' => $manifest['requirements'] ?? [],
                'entry_point' => $manifest['entry_point'] ?? 'index.js',
                'preview_image' => $manifest['preview_image'] ?? null,
                'is_active' => true,
                'is_system' => false
            ]);
        });
    }

    /**
     * Удалить тему
     */
    public function uninstallTheme(string $packageName): bool
    {
        $theme = $this->getTheme($packageName);
        
        if (!$theme) {
            throw new \InvalidArgumentException("Theme {$packageName} not found");
        }

        if ($theme->is_system) {
            throw new \InvalidArgumentException("Cannot uninstall system theme");
        }

        // Проверяем, не используется ли тема в магазинах
        $storesCount = $this->central(function () use ($theme) {
            return $theme->stores()->count();
        });

        if ($storesCount > 0) {
            throw new \InvalidArgumentException("Cannot uninstall theme that is being used by stores");
        }

        // Удаляем файлы темы
        $themePath = $this->themesPath . '/' . $packageName;
        if (File::exists($themePath)) {
            File::deleteDirectory($themePath);
        }

        // Удаляем запись из базы данных
        return $this->central(function () use ($theme) {
            return $theme->delete();
        });
    }

    /**
     * Получить Vue компоненты активной темы
     */
    public function getThemeComponents(): array
    {
        $cacheKey = 'theme_components_' . tenant('id');
        
        return Cache::remember($cacheKey, 3600, function () {
            $theme = $this->getCurrentTheme();
            
            if (!$theme) {
                return [];
            }

            return $theme->getVueComponents();
        });
    }

    /**
     * Получить конфигурацию активной темы
     */
    public function getThemeConfig(): array
    {
        $config = $this->getCurrentConfig();
        
        if (!$config) {
            $defaultTheme = $this->getDefaultTheme();
            return $defaultTheme ? $defaultTheme->getDefaultConfig() : [];
        }

        return $config->getFullConfig();
    }

    /**
     * Обновить конфигурацию темы
     */
    public function updateThemeConfig(array $config): void
    {
        $themeConfig = $this->getCurrentConfig();
        
        if (!$themeConfig) {
            throw new \RuntimeException("No active theme configuration found");
        }

        $currentConfig = $themeConfig->config ?? [];
        $themeConfig->update([
            'config' => array_merge($currentConfig, $config)
        ]);

        $this->clearCache();
    }

    /**
     * Сканировать директорию тем и обновить базу данных
     */
    public function scanThemes(): Collection
    {
        if (!File::exists($this->themesPath)) {
            File::makeDirectory($this->themesPath, 0755, true);
        }

        $themes = collect();
        $directories = File::directories($this->themesPath);

        foreach ($directories as $dir) {
            $packageName = str_replace($this->themesPath . '/', '', $dir);
            $manifestPath = $dir . '/theme.json';

            if (File::exists($manifestPath)) {
                $manifest = json_decode(File::get($manifestPath), true);
                
                if ($manifest && isset($manifest['package_name'])) {
                    $themes->push($this->syncThemeFromManifest($manifest, $dir));
                }
            }
        }

        return $themes;
    }

    /**
     * Синхронизировать тему из манифеста
     */
    protected function syncThemeFromManifest(array $manifest, string $themePath): Theme
    {
        return $this->central(function () use ($manifest) {
            return Theme::updateOrCreate(
                ['package_name' => $manifest['package_name']],
                [
                    'name' => $manifest['name'],
                    'slug' => $manifest['slug'] ?? str_replace(['/', '_'], '-', $manifest['package_name']),
                    'version' => $manifest['version'],
                    'author' => $manifest['author'] ?? null,
                    'description' => $manifest['description'] ?? null,
                    'features' => $manifest['features'] ?? [],
                    'config_schema' => $manifest['config_schema'] ?? [],
                    'requirements' => $manifest['requirements'] ?? [],
                    'entry_point' => $manifest['entry_point'] ?? 'index.js',
                    'preview_image' => $manifest['preview_image'] ?? null,
                    'is_active' => $manifest['is_active'] ?? true,
                    'is_system' => $manifest['is_system'] ?? false
                ]
            );
        });
    }

    /**
     * Очистить кеш тем
     */
    public function clearCache(): void
    {
        if (tenant()) {
            Cache::forget('theme_components_' . tenant('id'));
            Cache::forget('theme_config_' . tenant('id'));
        }
        
        $this->currentConfig = null;
    }

    /**
     * Выполнить код в контексте центральной базы данных
     */
    protected function central(callable $callback)
    {
        if (!tenant()) {
            return $callback();
        }

        return app(\Stancl\Tenancy\TenancyManager::class)->central($callback);
    }

    /**
     * Получить статистику использования тем
     */
    public function getThemeStats(): array
    {
        return $this->central(function () {
            $themes = Theme::withCount('stores')->get();
            
            return $themes->map(function ($theme) {
                return [
                    'package_name' => $theme->package_name,
                    'name' => $theme->name,
                    'version' => $theme->version,
                    'stores_count' => $theme->stores_count,
                    'is_system' => $theme->is_system,
                    'is_active' => $theme->is_active
                ];
            })->toArray();
        });
    }
}
