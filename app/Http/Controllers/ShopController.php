<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
class ShopController extends Controller
{
    // Форма регистрации магазина
    public function create()
    {
        //$plans = Plan::where('is_active', true)->get();
        return view('shop.create');
    }

    // Обработка создания магазина
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'shop_domain' => 'required|string|alpha_dash|max:50|unique:domains,domain',
            'plan_id' => 'required|exists:plans,id',

            // Данные владельца
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 1. Создаем пользователя-владельца
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 2. Создаем tenant (магазин)
        $tenant = Tenant::create([
            'id' => Str::slug($validated['shop_name']),
            'data' => [
                'owner_id' => $user->id,
                'shop_name' => $validated['shop_name'],
                'plan_id' => $validated['plan_id'],
            ],
        ]);

        // 3. Привязываем домен
        $domain = $tenant->domains()->create([
            'domain' => $validated['shop_domain'] . '.' . config('tenancy.central_domains')[0],
        ]);

        // 4. Настраиваем подписку
        $tenant->subscriptions()->create([
            'plan_id' => $validated['plan_id'],
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'is_active' => true,
        ]);

        // 5. Автоматическая настройка магазина
        tenancy()->initialize($tenant);
        $this->setupTenant($tenant, $user);
        tenancy()->end();

        // Авторизуем пользователя
        auth()->login($user);

        return redirect('http://' . $domain->domain);
    }

    protected function setupTenant($tenant, $user)
    {
        // 1. Запускаем миграции для магазина
        \Artisan::call('tenancy:migrate', [
            '--tenant' => $tenant->id,
        ]);

        // 2. Создаем базовые настройки магазина
        \App\Models\ShopSetting::create([
            'shop_name' => $tenant->data['shop_name'],
            'shop_email' => $user->email,
            'currency' => 'USD',
        ]);

        // 3. Назначаем пользователю роль владельца
        $user->assignRole('shop-owner');
    }
}
