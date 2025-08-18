<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Store extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public static function getCustomColumns(): array
    {
        return array_merge(parent::getCustomColumns(), [
            'user_id',
            'plan',
            'slug',
            'theme_id',
        ]);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }
}
