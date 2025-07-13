<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Models\Tenant as BaseModel;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
class Tenant extends BaseModel implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'data', // Для хранения кастомных данных в JSON
    ];

    /**
     * Отношение с подписками
     */
    public function subscriptions()
    {
        return $this->hasMany(\App\Models\Subscription::class, 'tenant_id');
    }

    /**
     * Активная подписка
     */
    public function activeSubscription()
    {
        return $this->hasOne(\App\Models\Subscription::class, 'tenant_id')
            ->where('is_active', true)
            ->where('expires_at', '>', now());
    }

    /**
     * Владелец магазина (если храним ID в data)
     */
    public function owner()
    {
        return $this->belongsTo(\App\Models\User::class, 'data->owner_id');
    }

    /**
     * Получение настроек магазина из данных тенанта
     */
    public function getSettingsAttribute()
    {
        return $this->data['settings'] ?? [];
    }
}
