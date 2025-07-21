<?php

namespace Database\Seeders;

use App\Helpers\TenantContext;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        TenantContext::forEachTenant(function () {
            Category::factory()
                ->count(5)
                ->create()
                ->each(function ($parent) {
                    Category::factory()->count(3)->withParent($parent)->create();
                });
        });

        $this->command->info('✅ Категории и подкатегории успешно созданы');
    }
}
