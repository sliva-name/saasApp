<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class ThemeConfig extends Model
{
    protected $fillable = [
        'theme_package_name',
        'tenant_id',
        'config',
        'custom_css',
        'custom_components',
        'is_active'
    ];

    protected $casts = [
        'config' => 'array',
        'custom_css' => 'array',
        'custom_components' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        // Автоматически добавляем tenant_id для изоляции
        static::addGlobalScope('tenant', function ($builder) {
            if (tenant()) {
                $builder->where('tenant_id', tenant()->id);
            }
        });

        static::creating(function ($config) {
            if (tenant()) {
                $config->tenant_id = tenant()->id;
            }
        });
    }

    /**
     * Получить тему из центральной базы данных
     */
    public function getTheme(): ?Theme
    {
        // Временно отключаем мультитенантность для доступа к центральной БД
        return app(\Stancl\Tenancy\TenancyManager::class)->central(function () {
            return Theme::where('package_name', $this->theme_package_name)->first();
        });
    }

    /**
     * Получить полную конфигурацию (дефолтная + пользовательская)
     */
    public function getFullConfig(): array
    {
        $theme = $this->getTheme();
        $defaultConfig = $theme ? $theme->getDefaultConfig() : [];
        $customConfig = $this->config ?? [];

        return array_merge($defaultConfig, $customConfig);
    }

    /**
     * Получить кастомные CSS стили в виде строки
     */
    public function getCustomCssString(): string
    {
        $customCss = $this->custom_css ?? [];
        
        if (empty($customCss)) {
            return '';
        }

        $cssString = '';
        foreach ($customCss as $selector => $styles) {
            $cssString .= "{$selector} {\n";
            foreach ($styles as $property => $value) {
                $cssString .= "  {$property}: {$value};\n";
            }
            $cssString .= "}\n\n";
        }

        return $cssString;
    }

    /**
     * Проверить, переопределен ли компонент
     */
    public function hasCustomComponent(string $componentName): bool
    {
        $customComponents = $this->custom_components ?? [];
        return isset($customComponents[$componentName]);
    }

    /**
     * Получить путь к кастомному компоненту
     */
    public function getCustomComponent(string $componentName): ?string
    {
        $customComponents = $this->custom_components ?? [];
        return $customComponents[$componentName] ?? null;
    }

    /**
     * Сохранить кастомный компонент
     */
    public function setCustomComponent(string $componentName, string $componentPath): void
    {
        $customComponents = $this->custom_components ?? [];
        $customComponents[$componentName] = $componentPath;
        $this->custom_components = $customComponents;
        $this->save();
    }

    /**
     * Удалить кастомный компонент
     */
    public function removeCustomComponent(string $componentName): void
    {
        $customComponents = $this->custom_components ?? [];
        unset($customComponents[$componentName]);
        $this->custom_components = $customComponents;
        $this->save();
    }

    /**
     * Получить активную конфигурацию темы для текущего тенанта
     */
    public static function getActiveConfig(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Активировать эту конфигурацию (деактивировать остальные)
     */
    public function activate(): void
    {
        // Деактивируем все другие конфигурации
        static::where('id', '!=', $this->id)->update(['is_active' => false]);
        
        // Активируем текущую
        $this->update(['is_active' => true]);
    }

    /**
     * Скоп для активных конфигураций
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
