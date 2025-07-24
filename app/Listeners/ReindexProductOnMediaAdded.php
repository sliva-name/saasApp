<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ReindexProductOnMediaAdded
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Media $media): void
    {
        $model = $media->model;

        if ($model instanceof \App\Models\Product) {
            $model->searchable(); // Laravel Scout обновит MeiliSearch
        }
        \Log::info('Media created: ' . $media->id);
    }
}
