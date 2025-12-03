<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $itemId)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:255',
    ]);

        Comment::create([
            'item_id' => $itemId,
            'user_id' => auth()->id(),
            'comment' => $validated['comment'],
    ]);

        return redirect()->back()->with('success', 'コメントを追加しました');
    }
}
