<?php

namespace App\Console\Commands;

use App\Helpers\TenantContext;
use App\Models\Product;
use Illuminate\Console\Command;

class FlushTenantProductsFromScout extends Command
{
    protected $signature = 'tenants:scout-flush';

    protected $description = 'Удаляет продукты всех тенантов из MeiliSearch';

    public function handle()
    {
        TenantContext::forEachTenant(function ($tenant) {
            $this->info("🗑 Удаление продуктов для тенанта: {$tenant->id}");
            Product::query()->unsearchable();
        });

        $this->info('✅ Все продукты удалены из MeiliSearch');

        return self::SUCCESS;
    }
}
