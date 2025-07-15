<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'category_id',
    ];
    protected $casts = [
        'attributes' => 'array',
    ];
    protected static function booted()
    {
        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
        });
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->useDisk('public');;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
