<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'images/watch.jpeg',
                'condition' => '良好',
                'user_id' => 1,
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'images/hdd.jpeg',
                'condition' => '目立った傷や汚れなし',
                'user_id' => 1,
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'images/onion.jpeg',
                'condition' => 'やや傷や汚れあり',
                'user_id' => 1,
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand' => '',
                'description' => 'クラシックなデザインの革靴',
                'image' => 'images/shoes.jpeg',
                'condition' => '状態が悪い',
                'user_id' => 1,
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => 'レノボ',
                'description' => '高性能なノートパソコン',
                'image' => 'images/laptop.jpeg',
                'condition' => '良好',
                'user_id' => 1,
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'image' => 'images/mike.jpeg',
                'condition' => '目立った傷や汚れなし',
                'user_id' => 1,
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => '',
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'images/bag.jpeg',
                'condition' => 'やや傷や汚れあり',
                'user_id' => 1,
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'image' => 'images/tumbler.jpeg',
                'condition' => '状態が悪い',
                'user_id' => 1,
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image' => 'images/coffee-mill.jpeg',
                'condition' => '良好',
                'user_id' => 1,
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => '',
                'description' => '便利なメイクアップセット',
                'image' => 'images/makeup.jpeg',
                'condition' => '目立った傷や汚れなし',
                'user_id' => 1,
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}