<?php

namespace App\Helpers;

use Closure;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Tenancy;
class TenantContext
{
    /**
     * Запускает код в контексте тенанта.
     *
     * @param Tenant|int|string $tenant Модель или ID
     * @param Closure $callback Код, который должен быть выполнен внутри
     * @return mixed Результат выполнения
     */
    public static function run(Tenant|int|string $tenant, Closure $callback): mixed
    {
        $tenancy = app(Tenancy::class);

        if (!$tenant instanceof Tenant) {
            $tenant = \App\Models\Store::findOrFail($tenant); // или как у тебя называется модель тенанта
        }

        $tenancy->initialize($tenant);

        try {
            return $callback();
        } finally {
            $tenancy->end(); // гарантированно завершает даже при ошибках
        }
    }

    /**
     * Пробегается по всем тенантам и запускает callback.
     *
     * @param Closure $callback
     * @return void
     */
    public static function forEachTenant(Closure $callback): void
    {
        foreach (\App\Models\Store::all() as $tenant) {
            self::run($tenant, fn() => $callback($tenant));
        }
    }

    public static function mapAcrossTenants(Closure $callback): \Illuminate\Support\Collection
    {
        $results = collect();

        self::forEachTenant(function ($tenant) use (&$results, $callback) {
            $data = $callback($tenant);
            $results->push($data);
        });

        return $results->flatten(1);
    }
}
