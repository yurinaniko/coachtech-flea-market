<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Item;
use App\Models\User;
use App\Models\Purchase;

class ItemCommentSeeder extends Seeder
{
    public function run()
    {
        $items = Item::all();
        $users = User::all();
        foreach ($items as $item) {

            Comment::create([
                'item_id' => $item->id,
                'user_id' => $item->user_id,
                'purchase_id' => null,
                'comment' => '本日中に発送可能です',
                'is_read' => true,
            ]);
            $purchase = Purchase::where('item_id', $item->id)->first();

            if ($purchase) {
                Comment::create([
                    'item_id' => $item->id,
                    'user_id' => $purchase->user_id,
                    'purchase_id' => null,
                    'comment' => '購入検討しています。200円引きは可能でしょうか？',
                    'is_read' => true,
                ]);
            }
        }
    }
}
