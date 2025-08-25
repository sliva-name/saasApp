<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;

class Theme extends Model
{
    protected $fillable = [
        'name',
        'slug', 
        'package_name',
        'version',
        'author',
        'preview_image',
        'description',
        'features',
        'config_schema',
        'requirements',
        'entry_point',
        'is_active',
        'is_system'
    ];

    protected $casts = [
        'features' => 'array',
        'config_schema' => 'array',
        'requirements' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * Связь с магазинами
     */
    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    /**
     * Получить путь к директории темы
     */
    public function getThemePathAttribute(): string
    {
        return base_path("themes/{$this->package_name}");
    }

    /**
     * Получить путь к Vue компонентам темы
     */
    public function getVueComponentsPathAttribute(): string
    {
        return "{$this->theme_path}/resources/js/components";
    }

    /**
     * Получить путь к CSS файлам темы
     */
    public function getCssPathAttribute(): string
    {
        return "{$this->theme_path}/resources/css";
    }

    /**
     * Получить конфигурацию темы по умолчанию
     */
    public function getDefaultConfig(): array
    {
        $configPath = "{$this->theme_path}/config/default.json";
        
        if (File::exists($configPath)) {
            return json_decode(File::get($configPath), true) ?? [];
        }
        
        return [];
    }

    /**
     * Проверить, доступна ли функция в теме
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * Получить список Vue компонентов темы
     */
    public function getVueComponents(): array
    {
        $componentsPath = $this->vue_components_path;
        
        if (!File::exists($componentsPath)) {
            return [];
        }
        
        $components = [];
        $files = File::allFiles($componentsPath);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'vue') {
                $relativePath = str_replace($componentsPath . '/', '', $file->getPathname());
                $componentName = str_replace(['/', '.vue'], ['', ''], $relativePath);
                $components[$componentName] = $relativePath;
            }
        }
        
        return $components;
    }

    /**
     * Получить манифест темы
     */
    public function getManifest(): array
    {
        $manifestPath = "{$this->theme_path}/theme.json";
        
        if (File::exists($manifestPath)) {
            return json_decode(File::get($manifestPath), true) ?? [];
        }
        
        return [];
    }

    /**
     * Проверить совместимость темы с системой
     */
    public function isCompatible(): bool
    {
        $requirements = $this->requirements ?? [];
        
        // Проверка версии Laravel
        if (isset($requirements['laravel'])) {
            $currentVersion = app()->version();
            if (!$this->versionSatisfies($currentVersion, $requirements['laravel'])) {
                return false;
            }
        }
        
        // Проверка версии PHP
        if (isset($requirements['php'])) {
            if (!$this->versionSatisfies(phpversion(), $requirements['php'])) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Простая проверка версий (можно улучшить с помощью composer/semver)
     */
    private function versionSatisfies(string $current, string $required): bool
    {
        return version_compare($current, $required, '>=');
    }

    /**
     * Скопы для активных тем
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Скопы для пользовательских тем (не системных)
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }
}
