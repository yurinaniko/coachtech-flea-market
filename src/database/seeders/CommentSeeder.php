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
        $itemIds = Item::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();

        foreach ($itemIds as $itemId) {
            $userId = $userIds[array_rand($userIds)];

            Comment::create([
                'item_id' => $itemId,
                'user_id' => $userId,
                'comment' => "これはユーザー{$userId}からのテストコメントです！",
            ]);
        }
    }
}