<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->count(3)->create();
        $userIds = User::pluck('id')->toArray();

        $items = [
            [
                'user_id' => 1,
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'img_url' => 'images/watch.jpeg',
                'condition_id' => 1,
                'category_id' => [1,5],
            ],
            [
                'user_id' => 1,
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'img_url' => 'images/hdd.jpeg',
                'condition_id' => 2,
                'category_id' => [2],
            ],
            [
                'user_id' => 1,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'img_url' => 'images/onion.jpeg',
                'condition_id' => 3,
                'category_id' => [11],
            ],
            [
                'user_id' => 1,
                'name' => '革靴',
                'price' => 4000,
                'brand' => '',
                'description' => 'クラシックなデザインの革靴',
                'img_url' => 'images/shoes.jpeg',
                'condition_id' => 4,
                'category_id' => [1,5],
            ],
            [
                'user_id' => 1,
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => 'レノボ',
                'description' => '高性能なノートパソコン',
                'img_url' => 'images/laptop.jpeg',
                'condition_id' => 1,
                'category_id' => [2],
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'img_url' => 'images/mike.jpeg',
                'condition_id' => 2,
                'category_id' => [2],
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => '',
                'description' => 'おしゃれなショルダーバッグ',
                'img_url' => 'images/bag.jpeg',
                'condition_id' => 3,
                'category_id' => [1,4],
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'img_url' => 'images/tumbler.jpeg',
                'condition_id' => 4,
                'category_id' => [10],
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'img_url' => 'images/coffee-mill.jpeg',
                'condition_id' => 1,
                'category_id' => [10],
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => '',
                'description' => '便利なメイクアップセット',
                'img_url' => 'images/makeup.jpeg',
                'condition_id' => 2,
                'category_id' => [6],
            ],
        ];
            foreach ($items as $itemData) {

            $itemData['user_id'] = $userIds[array_rand($userIds)];

            $categoryIds = $itemData['category_id'];
            unset($itemData['category_id']);

            // itemsテーブルに商品作成
            $item = Item::create($itemData);

           // 中間テーブルに登録
            $item->categories()->attach($categoryIds);
        }
    }
}