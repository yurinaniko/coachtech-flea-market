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
            ['email' => 'seller1@test.com'],
            [
                'name' => '出品者A',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        User::updateOrCreate(
            ['email' => 'seller2@test.com'],
            [
                'name' => '出品者B',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        User::updateOrCreate(
            ['email' => 'test@test.com'],
            [
                'name' => 'テストユーザー',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}