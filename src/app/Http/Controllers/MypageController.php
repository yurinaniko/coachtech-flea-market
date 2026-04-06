<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Purchase;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('tab', 'recommend');
        $keyword = $request->query('keyword');
        if (!Auth::check()) {
            return view('mypage.index', [
                'items' => collect(),
                'page' => $page,
                'keyword' => $keyword,
            ]);
        }

        if ($page === 'mylist') {
            $items = Auth::user()->favorites()->with('purchase')->get();
            if ($keyword) {
                $items = $items->filter(function($item) use ($keyword) {
                    return mb_stripos($item->name, $keyword) !== false;
                })->values();
            }
        }
        else {
            $query = Item::where('user_id', '!=', Auth::id());
                if ($keyword) {
                    $items = $query
                        ->where('name', 'like', '%' . $keyword . '%')
                        ->get();
                } else {
                    $items = $query->get();
                }
        }
        return view('mypage.index', compact('items', 'page', 'keyword'));
    }

    public function profileGate()
    {
        $user = Auth::user();
        $profile = $user->profile;
        if ($profile) {
            return redirect()->route('profile.edit');
        } else {
            return redirect()->route('profile.create');
        }
    }

    public function profile(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;
        $page = $request->query('page', 'sell');
        if ($page === 'sell') {
            $items = $user->items()->with('purchase')->get();
        } elseif ($page === 'buy') {
            $items = Item::whereHas('purchase', function ($q) {
                $q->where('user_id', Auth::id());
            })->get();
        }elseif ($page === 'trading') {
            $items = Item::whereHas('purchase', function ($q) {
                $q->where(function ($q2) {
                    $q2->where(function ($q3) {
                        $q3->where('user_id', Auth::id())
                        ->whereNull('buyer_reviewed');
                    })
                    ->orWhere(function ($q3) {
                        $q3->whereHas('item', function ($q4) {
                            $q4->where('user_id', Auth::id());
                        })
                    ->whereNull('seller_reviewed');
                    });
                });
            })
            ->with(['purchase' => function ($q) {
                $q->withCount([
                    'comments as unread_count' => function ($q2) {
                        $q2->where('is_read', false)
                        ->where('user_id', '!=', Auth::id());
                    }
                ])
                ->withMax('comments', 'created_at');
            }])
            ->get();
        }
        $items = $items->sortByDesc(function ($item) {
            $purchase = $item->purchase;
            $isCompleted = $purchase?->is_completed ?? false;

            return [
                $purchase->unread_count ?? 0,
                $purchase->comments_max_created_at ?? null,
            ];
        });
        $unreadCount = Comment::where('is_read', false)
            ->where('user_id', '!=', Auth::id())
            ->whereHas('purchase', function ($q) {
                $q->where(function ($q2) {
                    $q2->where(function ($q3) {
                        $q3->where('user_id', Auth::id())
                        ->whereNull('buyer_reviewed');
                    })
                    ->orWhere(function ($q3) {
                        $q3->whereHas('item', function ($q4) {
                            $q4->where('user_id', Auth::id());
                        })
                        ->whereNull('seller_reviewed');
                    });
                });
            })
        ->count();

        $receivedRatings = Purchase::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                    ->whereNotNull('seller_reviewed');
        })
        ->orWhere(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                    ->whereNotNull('buyer_reviewed');
        })
        ->get();

        $ratings = Purchase::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
            ->whereNotNull('seller_reviewed');
        })
        ->orWhere(function ($q) use ($user) {
            $q->whereHas('item', function ($q2) use ($user) {
                $q2->where('user_id', $user->id);
            })
            ->whereNotNull('buyer_reviewed');
        })
        ->get()
        ->map(function ($purchase) use ($user) {
            if ($purchase->user_id === $user->id) {
                return $purchase->seller_reviewed;
            }
                return $purchase->buyer_reviewed;
        })
        ->filter()
        ->values();

        $avgRating = $ratings->count() ? round($ratings->avg()) : 0;

        return view('mypage.profile', compact(
            'user',
            'profile',
            'items',
            'page',
            'unreadCount',
            'avgRating'
        ));
    }
}