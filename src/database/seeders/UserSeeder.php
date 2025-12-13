<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
        ['email' => 'test@test.com'],
        [
        'name' => 'テストユーザー',
        'password' => Hash::make('password'),
        ]
);

        User::factory()->count(5)->create();
    }
}