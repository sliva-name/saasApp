<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::firstOrNew([
            'name' => 'ghost',
            'email' => 'antyuhov2@gmail.com',
            'password' => Hash::make('mama2miya')
        ]);
    }
}
