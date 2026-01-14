<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $itemId)
    {
        Comment::create([
            'item_id' => $itemId,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);
        return redirect()->back();
    }
}
