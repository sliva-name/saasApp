<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_name',
        'shop_email',
        'shop_phone',
        'shop_address',
        'currency',
        'timezone',
        'logo_path',
        'favicon_path',
        'social_links',
        'payment_methods',
        'shipping_methods',
        'tax_settings'
    ];

    protected $casts = [
        'social_links' => 'array',
        'payment_methods' => 'array',
        'shipping_methods' => 'array',
        'tax_settings' => 'array'
    ];

    public static function getSettings()
    {
        return cache()->remember('shop_settings', now()->addDay(), function () {
            return self::firstOrCreate([]);
        });
    }
}
