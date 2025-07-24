<?php

namespace App\Providers;

use App\Listeners\ReindexProductOnMediaAdded;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Event::listen(
            'eloquent.created: ' . Media::class,
            [ReindexProductOnMediaAdded::class, 'handle']
        );
    }
}
