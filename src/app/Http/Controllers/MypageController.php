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

        if ($page === 'favorite') {
            $items = Auth::user()->favorites;
        } else {
            $items = Item::inRandomOrder()->take(10)->get();
        }

        return view('mypage.index', compact('items', 'page'));
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