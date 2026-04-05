<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $seller1 = User::where('email', 'seller1@test.com')->first();
        $seller2 = User::where('email', 'seller2@test.com')->first();

        $item1 = Item::where('name','腕時計')->first();
        $item2 = Item::where('name','HDD')->first();
        $item3 = Item::where('name','玉ねぎ3束')->first();
        $item4 = Item::where('name','マイク')->first();
        $item5 = Item::where('name','ショルダーバッグ')->first();

        Purchase::create([
            'user_id' => $seller2->id,
            'item_id' => $item1->id,
            'price' => $item1->price,
            'status' => 'completed',
            'is_completed' => false,
            'buyer_reviewed' => null,
            'seller_reviewed' => null,
            'payment_method' => 'card',
            'sending_postcode' => '150-0001',
            'sending_address' => '東京都渋谷区',
            'sending_building' => 'テストビル101',
        ]);

        Purchase::create([
            'user_id' => $seller1->id,
            'item_id' => $item4->id,
            'price' => $item4->price,
            'status' => 'pending',
            'is_completed' => false,
            'buyer_reviewed' => null,
            'seller_reviewed' => null,
            'payment_method' => 'konbini',
            'sending_postcode' => '530-0001',
            'sending_address' => '大阪府大阪市',
            'sending_building' => 'サンプルマンション202',
        ]);

        Purchase::create([
            'user_id' => $seller1->id,
            'item_id' => $item5->id,
            'price' => $item5->price,
            'status' => 'completed',
            'is_completed' => false,
            'buyer_reviewed' => 3,
            'seller_reviewed' => null,
            'payment_method' => 'card',
            'sending_postcode' => '930-0001',
            'sending_address' => '富山県富山市',
            'sending_building' => 'テストハイツ303',
        ]);

        Purchase::create([
            'user_id' => $seller2->id,
            'item_id' => $item2->id,
            'price' => $item2->price,
            'status' => 'completed',
            'is_completed' => true,
            'buyer_reviewed' => 4,
            'seller_reviewed' => 5,
            'payment_method' => 'card',
            'sending_postcode' => '060-0001',
            'sending_address' => '北海道札幌市',
            'sending_building' => 'テストタワー404',
        ]);

        Purchase::create([
            'user_id' => $seller2->id,
            'item_id' => $item3->id,
            'price' => $item3->price,
            'status' => 'completed',
            'is_completed' => false,
            'buyer_reviewed' => null,
            'seller_reviewed' => null,
            'payment_method' => 'card',
            'sending_postcode' => '060-0001',
            'sending_address' => '北海道札幌市',
            'sending_building' => 'テストタワー404',
        ]);
    }
}