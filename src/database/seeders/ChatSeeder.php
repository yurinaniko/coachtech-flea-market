<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Purchase;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        $purchases = Purchase::with('item')->get();

        foreach ($purchases as $purchase) {
            $item = $purchase->item;

            if ($item->name === '腕時計') {

                Comment::create([
                    'item_id' => $item->id,
                    'purchase_id' => $purchase->id,
                    'user_id' => $purchase->user_id,
                    'comment' => '購入しました！',
                    'is_read' => false,
                ]);

                Comment::create([
                    'item_id' => $item->id,
                    'purchase_id' => $purchase->id,
                    'user_id' => $item->user_id,
                    'comment' => 'ありがとうございます！',
                    'is_read' => true,
                ]);
            }

            if ($item->name === 'マイク') {
                Comment::create([
                    'item_id' => $item->id,
                    'purchase_id' => $purchase->id,
                    'user_id' => $purchase->user_id,
                    'comment' => '購入しました！',
                    'is_read' => true,
                ]);

                Comment::create([
                    'item_id' => $item->id,
                    'purchase_id' => $purchase->id,
                    'user_id' => $item->user_id,
                    'comment' => '発送準備しています',
                    'is_read' => false,
                ]);
            }

            if ($item->name === 'HDD') {
                Comment::create([
                    'item_id' => $item->id,
                    'purchase_id' => $purchase->id,
                    'user_id' => $purchase->user_id,
                    'comment' => '購入しました！',
                    'is_read' => true,
                ]);

                Comment::create([
                    'item_id' => $item->id,
                    'purchase_id' => $purchase->id,
                    'user_id' => $item->user_id,
                    'comment' => 'ありがとうございます！',
                    'is_read' => true,
                ]);
            }
        }
    }
}
