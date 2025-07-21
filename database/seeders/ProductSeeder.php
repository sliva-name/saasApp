<?php

namespace Database\Seeders;

use App\Console\Commands\FlushTenantProductsFromScout;
use App\Helpers\TenantContext;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {

        TenantContext::forEachTenant(function ($tenant) {
            Product::query()->delete();
            $this->command?->warn("Удалены все записи");
            $categories = Category::all();

            if ($categories->isEmpty()) {
                $this->command?->warn("⛔ Нет категорий у тенанта {$tenant->id}. Пропускаем...");
                return;
            }

            Product::factory()
                ->count(30)
                ->make()
                ->each(function ($product) use ($categories) {
                    $product->category_id = $categories->random()->id;
                    $product->save();

                    $imageUrl = 'https://placehold.co/600x400.svg?text=' . urlencode($product->name);

                    $product->addMediaFromUrl($imageUrl)
                        ->toMediaCollection('image');

                    $this->command?->info("Добавлено изображение для товара: {$product->name}");
                });

            $this->command?->info("✅ Сидированы 30 товаров для тенанта: {$tenant->id}");
        });
    }
}
