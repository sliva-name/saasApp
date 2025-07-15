<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Store;
use App\MoonShine\Resources\CategoryResource;
use App\MoonShine\Resources\ProductResource;
use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use App\MoonShine\Resources\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRoleResource;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  MoonShine  $core
     * @param  MoonShineConfigurator  $config
     *
     */
    public function boot(CoreContract $core, ConfiguratorContract $config): void
    {
        $core
            ->resources([
                MoonShineUserResource::class,
                MoonShineUserRoleResource::class,
                CategoryResource::class,
                ProductResource::class
            ])
            ->pages([
                ...$config->getPages(),
            ]);
        $config
            ->userField('username', 'email')
            ->userField('password', 'password')
            ->userField('name', 'name')
        ;
    }
}
