<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'image_url' => $this->getFirstMediaUrl('image'),
            'category_name' => optional($this->category)->name,
            'category_slug' => optional($this->category)->slug,
            'media' => $this->getMedia('image')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'original_url' => $media->getUrl(),
                ];
            }),
        ];
    }
}

