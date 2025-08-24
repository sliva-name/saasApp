<?php

namespace App\Services;

use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Str;

class StoreCreator
{
    public function create(User $user, string $shopName, string $plan, ?string $customDomain = null, ?int $themeId = null): Store
    {
        $slug = $this->generateSlug($user);

        $store = Store::create([
            'name' => $shopName,
            'user_id' => $user->id,
            'plan' => $plan,
            'slug' => $slug,
            'theme_id' => $themeId,
        ]);

        $domain = $customDomain ?: $this->generateUniqueSubdomain($user, $slug);

        $store->domains()->create([
            'domain' => $domain,
        ]);

        return $store;
    }

    protected function generateSlug(User $user): string
    {
        return Str::slug($user->name) . '-' . Str::random(6);
    }

    protected function generateUniqueSubdomain(User $user, ?string $slug = null): string
    {
        $slug ??= $this->generateSlug($user);

        return "{$slug}.localhost"; //TODO Переделать бы надо
    }
}
