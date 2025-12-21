<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Item;
use App\Models\User;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $items = Item::all();
        $users = User::all();

        foreach ($items as $item) {
            $user = $users->random();

            Comment::create([
                'item_id' => $item->id,
                'user_id' => $user->id,
                'comment' => "{$user->name}からのテストコメントです。",
            ]);
        }
    }
}