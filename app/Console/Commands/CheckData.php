<?php

namespace App\Console\Commands;

use App\Helpers\TenantContext;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;

class CheckData extends Command
{
    protected $signature = 'check:data';
    protected $description = 'Проверить данные в базе';

    public function handle()
    {
        $this->info('🔍 Проверка данных в базе...');

        TenantContext::forEachTenant(function ($tenant) {
            $this->info("\n📊 Тенант: {$tenant->id}");

            $categoriesCount = Category::count();
            $productsCount = Product::count();

            $this->info("📁 Категории: {$categoriesCount}");
            $this->info("📦 Товары: {$productsCount}");

            if ($categoriesCount > 0) {
                $this->info("\n📂 Список категорий:");
                Category::all()->each(function ($category) {
                    $this->line("  - {$category->name} (ID: {$category->id}, Slug: {$category->slug})");
                });
            }

            if ($productsCount > 0) {
                $this->info("\n📦 Первые 5 товаров:");
                Product::take(5)->get()->each(function ($product) {
                    $this->line("  - {$product->name} (ID: {$product->id}, Цена: {$product->price})");
                });
            }

            if ($categoriesCount === 0 || $productsCount === 0) {
                $this->warn("⚠️  Данные отсутствуют! Запустите сидеры:");
                $this->line("   make migrate-fresh");
                $this->line("   make seed");
            }
        });
    }
}
