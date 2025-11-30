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

    // プロフィールページ
    public function profile()
    {
        $user = Auth::user();
        return view('mypage.profile', compact('user'));
    }
}
