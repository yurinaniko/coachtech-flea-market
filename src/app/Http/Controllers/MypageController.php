<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

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
            $items = Auth::user()->favorites;
            if ($keyword) {
                $items = $items->filter(function($item) use ($keyword) {
                    return mb_stripos($item->name, $keyword) !== false;
                })->values();
            }
        } else {
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
    // 初回ログイン時の振り分け専用
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
            $items = $user->items()->get();
        } elseif ($page === 'buy') {
            $items = $user->purchases()->get();
        } else {
            $items = collect();
        }
        return view('mypage.profile', compact('user', 'profile', 'items', 'page'));
    }
}