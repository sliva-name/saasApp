<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;
class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'price' => 0.00,
                'features' => json_encode([
                    'Базовые функции магазина',
                    'До 10 товаров',
                    '1 пользователь',
                    'Стандартные темы',
                    'Поддомен localhost'
                ]),
                'is_active' => true,
                'max_products' => 10,
                'max_users' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Basic',
                'price' => 990.00,
                'features' => json_encode([
                    'Все функции Free',
                    'До 100 товаров',
                    'До 3 пользователей',
                    'Выбор темы',
                    'Свой поддомен',
                    'Базовая аналитика'
                ]),
                'is_active' => true,
                'max_products' => 100,
                'max_users' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pro',
                'price' => 2990.00,
                'features' => json_encode([
                    'Все функции Basic',
                    'Неограниченное количество товаров',
                    'До 10 пользователей',
                    'Кастомные темы',
                    'Свой домен',
                    'Расширенная аналитика',
                    'Приоритетная поддержка'
                ]),
                'is_active' => true,
                'max_products' => null, // неограничено
                'max_users' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        foreach ($plans as $plan) {
            Plan::firstOrCreate($plan);
        }
    }
}
