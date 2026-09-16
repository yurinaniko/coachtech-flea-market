<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use App\Models\Item;
use App\Models\Purchase;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $itemId)
    {
        // 存在しない item_id を送られると外部キー違反で 500 になる。先に 404 で弾く。
        $item = Item::findOrFail($itemId);

        $purchase = Purchase::where('item_id', $item->id)
            ->where('user_id', auth()->id())
            ->first();

        Comment::create([
            'user_id' => auth()->id(),
            'item_id' => $item->id,
            'purchase_id' => optional($purchase)->id,
            'comment' => $request->comment,
        ]);
        return redirect()->back();
    }
}
