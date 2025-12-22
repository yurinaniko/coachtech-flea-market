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
            'email_verified_at' => now(),
            ]
        );

        for ($i = 1; $i <= 10; $i++) {
            User::updateOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'name' => "ユーザー{$i}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }

        User::factory()->count(5)->create();
    }
}