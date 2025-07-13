<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stancl\Tenancy\Database\Models\Tenant;

class SetupTenant extends Command
{
    protected $signature = 'tenancy:setup {tenant}';
    protected $description = 'Setup a new tenant';

    public function handle()
    {
        $tenant = Tenant::find($this->argument('tenant'));

        tenancy()->initialize($tenant);

        // Запускаем миграции для магазина
        $this->call('migrate', [
            '--path' => 'database/migrations/tenant',
            '--database' => 'tenant',
            '--force' => true,
        ]);

        // Устанавливаем базовые данные
        $this->call('db:seed', [
            '--class' => 'TenantDatabaseSeeder',
            '--database' => 'tenant',
        ]);

        tenancy()->end();

        $this->info("Tenant {$tenant->id} setup successfully!");
    }
}
