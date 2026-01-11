<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();

        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'img_url' => 'dummy/watch.jpeg',
                'condition_id' => 1,
                'category_id' => [1,5],
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'img_url' => 'dummy/hdd.jpeg',
                'condition_id' => 2,
                'category_id' => [2],
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'img_url' => 'dummy/onion.jpeg',
                'condition_id' => 3,
                'category_id' => [11],
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand' => '',
                'description' => 'クラシックなデザインの革靴',
                'img_url' => 'dummy/shoes.jpeg',
                'condition_id' => 4,
                'category_id' => [1,5],
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => '',
                'description' => '高性能なノートパソコン',
                'img_url' => 'dummy/laptop.jpeg',
                'condition_id' => 1,
                'category_id' => [2],
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'img_url' => 'dummy/mike.jpeg',
                'condition_id' => 2,
                'category_id' => [2],
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => '',
                'description' => 'おしゃれなショルダーバッグ',
                'img_url' => 'dummy/bag.jpeg',
                'condition_id' => 3,
                'category_id' => [1,4],
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'img_url' => 'dummy/tumbler.jpeg',
                'condition_id' => 4,
                'category_id' => [10],
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'img_url' => 'dummy/coffee-mill.jpeg',
                'condition_id' => 1,
                'category_id' => [10],
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => '',
                'description' => '便利なメイクアップセット',
                'img_url' => 'dummy/makeup.jpeg',
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