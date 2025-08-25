<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Models\ThemeConfig;
use App\Services\ThemeManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ThemeController extends Controller
{
    public function __construct(
        private ThemeManager $themeManager
    ) {}

    /**
     * Получить активную тему тенанта
     */
    public function getActiveTenant(): JsonResponse
    {
        try {
            // Проверяем, что мы в контексте тенанта
            if (!tenant()) {
                return response()->json([
                    'package_name' => 'themes/default',
                    'config' => [
                        'colors' => [
                            'primary' => '#3b82f6',
                            'secondary' => '#64748b'
                        ]
                    ]
                ]);
            }

            // Получаем реальную активную тему
            $activeTheme = $this->getActiveTenantTheme();
            
            // Добавляем базовую конфигурацию
            $activeTheme['config'] = array_merge($activeTheme['config'], [
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
                ],
                'branding' => [
                    'site_name' => 'Demo Store',
                    'description' => 'Your amazing online store',
                    'logo' => null
                ]
            ]);
            
            return response()->json($activeTheme);

            /* TODO: Включить когда БД будет готова
            $config = $this->themeManager->getCurrentConfig();
            
            if (!$config) {
                $defaultTheme = $this->themeManager->getDefaultTheme();
                return response()->json([
                    'package_name' => $defaultTheme ? $defaultTheme->package_name : 'themes/default',
                    'config' => $defaultTheme ? $defaultTheme->getDefaultConfig() : []
                ]);
            }

            return response()->json([
                'package_name' => $config->theme_package_name,
                'config' => $config->getFullConfig()
            ]);
            */
        } catch (\Exception $e) {
            Log::error('[ThemeController] Failed to get active tenant theme: ' . $e->getMessage());
            
            return response()->json([
                'package_name' => 'themes/default',
                'config' => []
            ]);
        }
    }

    /**
     * Получить информацию о теме
     */
    public function show(string $packageName): JsonResponse
    {
        try {
            // Пока что возвращаем заглушку для демо
            if ($packageName === 'themes/default' || $packageName === 'themes%2Fdefault') {
                return response()->json([
                    'id' => 1,
                    'name' => 'Default Theme',
                    'package_name' => 'themes/default',
                    'version' => '1.0.0',
                    'author' => 'SaaS E-commerce Team',
                    'description' => 'Системная тема по умолчанию',
                    'features' => [
                        'product_catalog',
                        'product_search',
                        'shopping_cart',
                        'user_authentication',
                        'responsive_design'
                    ],
                    'config_schema' => [
                        'colors' => [
                            'type' => 'object',
                            'properties' => [
                                'primary' => [
                                    'type' => 'string',
                                    'format' => 'color',
                                    'default' => '#3b82f6'
                                ]
                            ]
                        ]
                    ],
                    'requirements' => [
                        'php' => '>=8.2',
                        'laravel' => '>=11.0',
                        'vue' => '>=3.3'
                    ],
                    'entry_point' => 'index.js',
                    'preview_image' => '/themes/default/preview.jpg',
                    'is_active' => true,
                    'is_system' => true,
                    'created_at' => '2024-01-01T00:00:00Z',
                    'updated_at' => '2024-01-01T00:00:00Z'
                ]);
            }

            // Декодируем package name если он URL-encoded
            $packageName = urldecode($packageName);
            
            // Получаем тему из центральной базы данных
            $theme = tenancy()->central(function () use ($packageName) {
                return \App\Models\Theme::where('package_name', $packageName)->first();
            });
            
            if (!$theme) {
                return response()->json(['error' => 'Theme not found'], 404);
            }

            return response()->json([
                'id' => $theme->id,
                'name' => $theme->name,
                'package_name' => $theme->package_name,
                'version' => $theme->version,
                'author' => $theme->author,
                'description' => $theme->description,
                'features' => $theme->features,
                'config_schema' => $theme->config_schema,
                'requirements' => $theme->requirements,
                'entry_point' => $theme->entry_point ?? 'index.js',
                'preview_image' => $theme->preview_image,
                'is_active' => $theme->is_active,
                'is_system' => $theme->is_system,
                'created_at' => $theme->created_at?->toISOString(),
                'updated_at' => $theme->updated_at?->toISOString()
            ]);


        } catch (\Exception $e) {
            Log::error('[ThemeController] Failed to get theme info: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Получить список доступных тем
     */
    public function index(): JsonResponse
    {
        try {
            $themes = $this->themeManager->getAvailableThemes();
            
            return response()->json([
                'data' => $themes->map(function ($theme) {
                    return [
                        'id' => $theme->id,
                        'name' => $theme->name,
                        'package_name' => $theme->package_name,
                        'version' => $theme->version,
                        'author' => $theme->author,
                        'description' => $theme->description,
                        'features' => $theme->features,
                        'preview_image' => $theme->preview_image,
                        'is_system' => $theme->is_system,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Активировать тему для текущего тенанта
     */
    public function activate(Request $request): JsonResponse
    {
        $request->validate([
            'package_name' => 'required|string',
            'config' => 'sometimes|array'
        ]);

        try {
            $config = $this->themeManager->activateTheme(
                $request->package_name,
                $request->config ?? []
            );

            return response()->json([
                'message' => 'Theme activated successfully',
                'config' => $config->getFullConfig()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Получить конфигурацию активной темы
     */
    public function getConfig(): JsonResponse
    {
        try {
            $config = $this->themeManager->getThemeConfig();
            
            return response()->json($config);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Обновить конфигурацию темы
     */
    public function updateConfig(Request $request): JsonResponse
    {
        $request->validate([
            'config' => 'required|array'
        ]);

        try {
            $this->themeManager->updateThemeConfig($request->config);
            $config = $this->themeManager->getThemeConfig();
            
            return response()->json($config);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Получить компоненты активной темы
     */
    public function getComponents(): JsonResponse
    {
        try {
            // Получаем активную тему тенанта
            $activeTheme = $this->getActiveTenantTheme();
            $themeName = $activeTheme['package_name'] ?? 'themes/default';
            
            // Извлекаем название папки темы из package_name
            $themeFolder = str_replace('themes/', '', $themeName);
            
            // Определяем компоненты в зависимости от темы
            $components = $this->getThemeComponents($themeFolder);
            
            return response()->json($components);
        } catch (\Exception $e) {
            Log::error('[ThemeController] Failed to get components: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Получить компоненты конкретной темы
     */
    private function getThemeComponents(string $themeFolder): array
    {
        $basePath = "/themes/themes/{$themeFolder}/resources/js";
        
        // Проверяем, существует ли папка темы
        $themePath = base_path("themes/themes/{$themeFolder}");
        if (!file_exists($themePath)) {
            // Fallback к default теме
            $themeFolder = 'default';
            $basePath = "/themes/themes/default/resources/js";
        }
        
        return [
            // Компоненты из папки components
            'TenantApp' => "{$basePath}/components/TenantApp.vue",
            'ProductCard' => "{$basePath}/components/ProductCard.vue", 
            'Header' => "{$basePath}/components/Header.vue",
            'Footer' => "{$basePath}/components/Footer.vue",
            'Navigation' => "{$basePath}/components/Navigation.vue",
            'ProductCatalog' => "{$basePath}/components/ProductCatalog.vue",
            'ProductDetail' => "{$basePath}/components/ProductDetail.vue",
            'ProductGrid' => "{$basePath}/components/ProductGrid.vue",
            'SearchBar' => "{$basePath}/components/SearchBar.vue",
            'FiltersSidebar' => "{$basePath}/components/FiltersSidebar.vue",
            
            // Страницы из папки pages
            'Home' => "{$basePath}/pages/Home.vue",
            'Products' => "{$basePath}/pages/Products.vue",
            'About' => "{$basePath}/pages/About.vue",
            'Contact' => "{$basePath}/pages/Contact.vue",
            'AccountPage' => "{$basePath}/pages/AccountPage.vue",
            'LoginPage' => "{$basePath}/pages/LoginPage.vue",
            'RegisterPage' => "{$basePath}/pages/RegisterPage.vue",
            'CartPage' => "{$basePath}/pages/CartPage.vue",
            'CategoryPage' => "{$basePath}/pages/CategoryPage.vue",
            'ForgotPasswordPage' => "{$basePath}/pages/ForgotPasswordPage.vue",
            'ResetPasswordPage' => "{$basePath}/pages/ResetPasswordPage.vue"
        ];
    }

    /**
     * Получить активную тему тенанта (внутренний метод)
     */
    private function getActiveTenantTheme(): array
    {
        try {
            if (!tenant()) {
                return [
                    'package_name' => 'themes/default',
                    'config' => []
                ];
            }

            // Store и Tenant - это одна и та же модель в stancl/tenancy
            // tenant() возвращает Store модель
            $store = tenant();
            
            if (!$store->theme_id) {
                Log::warning('[ThemeController] Store has no theme_id set: ' . $store->id);
                return [
                    'package_name' => 'themes/default',
                    'config' => []
                ];
            }

            // Получаем тему из центральной базы данных
            $theme = tenancy()->central(function () use ($store) {
                return \App\Models\Theme::find($store->theme_id);
            });
            
            if (!$theme) {
                Log::error('[ThemeController] Theme not found for theme_id: ' . $store->theme_id);
                return [
                    'package_name' => 'themes/default',
                    'config' => []
                                ];
            }

            return [
                'package_name' => $theme->package_name,
                'config' => []
            ];
        } catch (\Exception $e) {
            Log::error('[ThemeController] Error getting active tenant theme: ' . $e->getMessage());
            return [
                'package_name' => 'themes/default',
                'config' => []
            ];
        }
    }

    /**
     * Проверить совместимость темы
     */
    public function checkCompatibility(string $packageName): JsonResponse
    {
        try {
            $theme = $this->themeManager->getTheme($packageName);
            
            if (!$theme) {
                return response()->json(['error' => 'Theme not found'], 404);
            }

            $compatible = $theme->isCompatible();
            
            return response()->json([
                'compatible' => $compatible,
                'requirements' => $theme->requirements,
                'theme' => [
                    'name' => $theme->name,
                    'version' => $theme->version,
                    'package_name' => $theme->package_name
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Получить превью темы
     */
    public function getPreview(string $packageName): JsonResponse
    {
        try {
            $theme = $this->themeManager->getTheme($packageName);
            
            if (!$theme) {
                return response()->json(['error' => 'Theme not found'], 404);
            }

            $previewData = [
                'name' => $theme->name,
                'description' => $theme->description,
                'preview_image' => $theme->preview_image,
                'features' => $theme->features,
                'config_schema' => $theme->config_schema,
                'default_config' => $theme->getDefaultConfig()
            ];

            return response()->json($previewData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
