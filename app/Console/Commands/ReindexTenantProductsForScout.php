<?php

namespace App\Console\Commands;

use App\Helpers\TenantContext;
use App\Models\Product;
use Illuminate\Console\Command;

class ReindexTenantProductsForScout extends Command
{
    protected $signature = 'tenants:scout-reindex';

    protected $description = 'Полностью переиндексирует продукты всех тенантов в MeiliSearch (flush + import)';

    public function handle()
    {
        TenantContext::forEachTenant(function ($tenant) {
            $this->info("🔄 Переиндексация для тенанта: {$tenant->id}");

            $this->line("🗑 Удаление...");
            Product::query()->unsearchable();

            $this->line("📥 Импорт...");
            Product::query()->searchable();
        });

        $this->info('✅ Все продукты переиндексированы в MeiliSearch');

        return self::SUCCESS;
    }
}
