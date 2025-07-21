<?php

namespace App\Console\Commands;

use App\Helpers\TenantContext;
use App\Models\Product;
use Illuminate\Console\Command;

class ImportTenantProductsToScout extends Command
{
    protected $signature = 'tenants:scout-import';

    protected $description = 'Импортирует продукты всех тенантов в MeiliSearch';

    public function handle()
    {
        TenantContext::forEachTenant(function ($tenant){
            $this->info("🏬 Импорт для тенанта: {$tenant->id}");
            Product::query()->searchable();
        });

        $this->info('✅ Все продукты отправлены в MeiliSearch');

        return self::SUCCESS;
    }
}
