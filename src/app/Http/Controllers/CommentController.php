<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use App\Models\Purchase;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $itemId)
    {
        $purchase = Purchase::where('item_id', $itemId)
            ->where('user_id', auth()->id())
            ->first();

        Comment::create([
            'user_id' => auth()->id(),
            'item_id' => $itemId,
            'purchase_id' => optional($purchase)->id,
            'comment' => $request->comment,
        ]);
        return redirect()->back();
    }
}
