<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profile;
use App\Models\User;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        // 全ユーザー取得
        $users = User::all();

        foreach ($users as $user) {
            Profile::updateOrCreate(
                ['user_id' => $user->id],
                [
                'user_id' => $user->id,
                'img_url' => null,
                'postal_code' => '000-0000',
                'address' => 'テスト住所',
                'building' => 'テストビル101',
            ]);
        }
    }
}