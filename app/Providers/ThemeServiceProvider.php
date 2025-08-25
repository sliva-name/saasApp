<?php

namespace App\Providers;

use App\Services\ThemeManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Регистрируем ThemeManager как singleton
        $this->app->singleton(ThemeManager::class, function ($app) {
            return new ThemeManager();
        });

        // Алиас для удобства
        $this->app->alias(ThemeManager::class, 'theme.manager');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Регистрируем Blade директивы для тем
        $this->registerBladeDirectives();

        // Делимся переменными с View
        $this->shareViewVariables();

        // Регистрируем middleware для тем
        $this->registerMiddleware();

        // Публикуем ассеты тем
        $this->publishThemeAssets();
    }

    /**
     * Регистрируем Blade директивы
     */
    protected function registerBladeDirectives(): void
    {
        // @theme('component.name') - подключение компонента темы
        Blade::directive('theme', function ($expression) {
            return "<?php echo app('theme.manager')->renderComponent({$expression}); ?>";
        });

        // @themeConfig('key') - получение конфигурации темы
        Blade::directive('themeConfig', function ($expression) {
            return "<?php echo app('theme.manager')->getConfigValue({$expression}); ?>";
        });

        // @themeAsset('path') - получение пути к ассету темы
        Blade::directive('themeAsset', function ($expression) {
            return "<?php echo app('theme.manager')->getAssetUrl({$expression}); ?>";
        });

        // @themeFeature('feature') - проверка наличия функции в теме
        Blade::if('themeFeature', function ($feature) {
            $theme = app('theme.manager')->getCurrentTheme();
            return $theme && $theme->hasFeature($feature);
        });
    }

    /**
     * Делимся переменными с View
     */
    protected function shareViewVariables(): void
    {
        // Только для тенантских маршрутов
        if (app()->bound('tenant')) {
            View::composer('*', function ($view) {
                $themeManager = app('theme.manager');
                
                $view->with([
                    'currentTheme' => $themeManager->getCurrentTheme(),
                    'themeConfig' => $themeManager->getThemeConfig(),
                    'themeComponents' => $themeManager->getThemeComponents(),
                ]);
            });
        }
    }

    /**
     * Регистрируем middleware
     */
    protected function registerMiddleware(): void
    {
        // Middleware будет зарегистрирован в RouteServiceProvider
        // или в bootstrap/app.php в зависимости от версии Laravel
    }

    /**
     * Публикуем ассеты тем
     */
    protected function publishThemeAssets(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../themes' => public_path('themes'),
            ], 'theme-assets');
        }
    }
}
