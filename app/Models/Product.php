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
        'tenant_id',
    ];
    protected $casts = [
        'attributes' => 'array',
    ];
    /**
     * Настройка данных для индексации в MeiliSearch
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'tenant_id' => $this->tenant_id,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'stock' => (int) $this->stock,
            'category_id' => (int) $this->category_id,
            'image_url' => $this->getFirstMediaUrl('image'),
            'created_at' => $this->created_at->timestamp,
        ];
    }

    protected static function booted()
{
    static::creating(function ($product) {
        $product->slug = Str::slug($product->name);
        $product->tenant_id ??= tenant('id'); 
    });
    
    static::addGlobalScope('tenant', function ($builder) {
        $builder->where('tenant_id', tenant()->id);
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
