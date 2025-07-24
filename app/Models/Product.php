<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes, Searchable, HasFactory;

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
    /**
     * Настройка данных для индексации в MeiliSearch
     */
    public function toSearchableArray(): array
    {
        return array_merge($this->toArray(),[
            'id' => (int) $this->id,
            'price' => (float) $this->price,
            'image_url' => $this->getFirstMediaUrl('image'),
            'created_at' => $this->created_at->timestamp,
        ]);
    }
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
