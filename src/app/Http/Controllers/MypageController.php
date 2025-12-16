<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class MypageController extends Controller
{
    // マイページTOP（商品一覧）
    public function index(Request $request)
    {
        $page = $request->query('page', 'recommend');
        $keyword = $request->query('keyword');

        if (!Auth::check()) {
            return redirect()->route('items.index');
        }

            // ★ マイリスト
        if ($page === 'favorite') {
            $items = Auth::user()->favorites;

            // マイリストの中で検索したい場合
            if ($keyword) {
                $items = $items->filter(function($item) use ($keyword) {
                    return mb_stripos($item->name, $keyword) !== false;
                })->values();
            }

        } else {

            $query = Item::where('user_id', '!=', Auth::id());

            if ($keyword) {
            // キーワードあり → 通常検索
            $items = $query
                ->where('name', 'like', '%' . $keyword . '%')
                ->get();
            } else {
            // キーワードなし → ランダム10件
            $items = $query->get();
            }
        }

        return view('mypage.index', compact('items', 'page', 'keyword'));
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