<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Theme;
use Illuminate\Support\Facades\File;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding themes...');

        // Создаем системную тему по умолчанию
        $this->createDefaultTheme();
        
        // Создаем минималистичную тему
        $this->createMinimalTheme();
        
        $this->command->info('Themes seeded successfully!');
    }

    /**
     * Создать системную тему по умолчанию
     */
    private function createDefaultTheme(): void
    {
        $theme = Theme::updateOrCreate(
            ['package_name' => 'themes/default'],
            [
                'name' => 'Default Theme',
                'slug' => 'default',
                'version' => '1.0.0',
                'author' => 'SaaS E-commerce Team',
                'description' => 'Системная тема по умолчанию с базовым функционалом',
                'features' => [
                    'product_catalog',
                    'product_search',
                    'shopping_cart',
                    'user_authentication',
                    'responsive_design',
                    'basic_customization'
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
                    ],
                    'layout' => [
                        'type' => 'object',
                        'properties' => [
                            'header_fixed' => [
                                'type' => 'boolean',
                                'default' => true,
                                'title' => 'Зафиксированная шапка'
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
            ]
        );

        $this->command->info("✓ Default theme created/updated: {$theme->name}");
    }

    /**
     * Создать минималистичную тему
     */
    private function createMinimalTheme(): void
    {
        $theme = Theme::updateOrCreate(
            ['package_name' => 'themes/minimal'],
            [
                'name' => 'Minimal Theme',
                'slug' => 'minimal',
                'version' => '1.0.0',
                'author' => 'SaaS E-commerce Team',
                'description' => 'Минималистичная тема с чистым дизайном и фокусом на контенте',
                'features' => [
                    'product_catalog',
                    'product_search',
                    'shopping_cart',
                    'user_authentication',
                    'responsive_design',
                    'minimal_design',
                    'high_performance'
                ],
                'config_schema' => [
                    'colors' => [
                        'type' => 'object',
                        'properties' => [
                            'primary' => [
                                'type' => 'string',
                                'format' => 'color',
                                'default' => '#000000',
                                'title' => 'Основной цвет'
                            ],
                            'secondary' => [
                                'type' => 'string',
                                'format' => 'color',
                                'default' => '#666666',
                                'title' => 'Вторичный цвет'
                            ]
                        ]
                    ],
                    'layout' => [
                        'type' => 'object',
                        'properties' => [
                            'max_width' => [
                                'type' => 'string',
                                'default' => '1200px',
                                'title' => 'Максимальная ширина контента'
                            ],
                            'spacing' => [
                                'type' => 'string',
                                'enum' => ['compact', 'normal', 'spacious'],
                                'default' => 'normal',
                                'title' => 'Размер отступов'
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
                'preview_image' => '/themes/minimal/preview.jpg',
                'is_active' => true,
                'is_system' => false,
            ]
        );

        $this->command->info("✓ Minimal theme created/updated: {$theme->name}");
    }
}
