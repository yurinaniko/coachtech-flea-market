<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Http\Requests\StoreChatRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;

class ChatController extends Controller
{
    public function show($itemId)
    {
        $purchase = Purchase::where('item_id', $itemId)
            ->where(function ($q) {
                $q->where('user_id', auth()->id())
                ->orWhereHas('item', function ($q2) {
                    $q2->where('user_id', auth()->id());
                });
            })
            ->first();
        if (!$purchase) {
            abort(404);
        }
        Comment::where('purchase_id', $purchase->id)
            ->where('user_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        $comments = Comment::with('user.profile')
            ->where('purchase_id', $purchase->id)
            ->orderBy('created_at')
            ->get();
        $item = $purchase->item;
        $user = $purchase->user_id === auth()->id()
            ? $purchase->item->user
            : $purchase->user;
        $transactions = Purchase::where(function ($q) {
            $q->where('user_id', auth()->id())
            ->whereNull('buyer_reviewed');
        })
        ->orWhere(function ($q) {
            $q->whereHas('item', function ($q2) {
                $q2->where('user_id', auth()->id());
            })
            ->whereNull('seller_reviewed');
        })
        ->with('item')
        ->withCount([
            'comments as unread_count' => function ($q) {
                $q->where('is_read', false)
                ->where('user_id', '!=', auth()->id());
            }
        ])
        ->withMax('comments', 'created_at')
        ->orderByDesc('unread_count')
        ->orderByDesc('comments_max_created_at')
        ->get();

        return view('chat.show', compact(
            'comments',
            'purchase',
            'item',
            'user',
            'transactions'
        ));
    }

    public function store(StoreChatRequest $request, $itemId)
    {
        $purchase = Purchase::where('item_id', $itemId)
            ->where(function ($q) {
                $q->where('user_id', auth()->id())
                ->orWhereHas('item', function ($q2) {
                    $q2->where('user_id', auth()->id());
                });
            })
        ->firstOrFail();
        if ($purchase->is_completed) {
            abort(403);
        }
        if ($request->comment_id) {
            $comment = Comment::findOrFail($request->comment_id);
            if ($comment->user_id !== auth()->id()) {
                abort(403);
            }
            $imagePath = $comment->image;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
            }
            $comment->update([
                'comment' => $request->comment,
                'image' => $imagePath,
            ]);
            return back();
        }
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }
        Comment::create([
            'user_id' => auth()->id(),
            'item_id' => $itemId,
            'purchase_id' => $purchase->id,
            'comment' => $request->comment,
            'is_read' => false,
            'image' => $imagePath,
        ]);

        return back();
    }

    public function review(Request $request, $purchaseId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);
        $purchase = Purchase::with('item.user')->findOrFail($purchaseId);
        if ($purchase->user_id === auth()->id()) {
            $purchase->buyer_reviewed = $request->rating;
            $seller = $purchase->item->user;
            $buyer = $purchase->user;
            Mail::to($seller->email)->send(
                new TransactionCompletedMail($purchase, $buyer->name)
            );
        } else {
            $purchase->seller_reviewed = $request->rating;
        }
        if (!is_null($purchase->buyer_reviewed) && !is_null($purchase->seller_reviewed)) {
            $purchase->is_completed = true;
        }
        $purchase->save();
        return redirect()
            ->route('mypage.index', ['page' => 'trading']);
    }

    public function destroy($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }
        $comment->delete();
        return back();
    }
}
