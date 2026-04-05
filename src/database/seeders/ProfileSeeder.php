<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profile;
use App\Models\User;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::take(3)->get();

        $data = [
            [
                'img_url' => null,
                'postal_code' => '100-0001',
                'address' => '東京都渋谷区',
                'building' => 'テストビル101',
            ],
            [
                'img_url' => null,
                'postal_code' => '530-0001',
                'address' => '大阪府大阪市',
                'building' => 'サンプルマンション202',
            ],
            [
                'img_url' => null,
                'postal_code' => '930-0001',
                'address' => '富山県富山市',
                'building' => 'テストハイツ303',
            ],
        ];

        foreach ($users as $index => $user) {
            Profile::updateOrCreate(
                ['user_id' => $user->id],
                array_merge(['user_id' => $user->id], $data[$index])
            );
        }
    }
}