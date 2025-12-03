<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Http\Requests\ProfileRequest;

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
    $page = $request->query('page', 'sell');  // 初期タブは「出品した商品」

    // タブで切替
    if ($page === 'sell') {
        $items = $user->items()->get();
    } elseif ($page === 'buy') {
        $items = $user->purchases()->get();
    } else {
        $items = collect();
    }

    return view('mypage.profile', compact('user', 'items', 'page'));
}

    // 初回プロフィール作成画面
    public function create()
    {
        $user = Auth::user();
        return view('mypage.profile-create', compact('user'));
    }

    // 初回プロフィール登録処理
    public function store(ProfileRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();   // ← validateはProfileRequestに任せる

        // -----画像アップロード-----
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->storeAs('public/profile', $imageName);
            $validated['image'] = 'profile/' . $imageName;
        }

        $user->update($validated);

        return redirect()->route('mypage.index');
    }

    // プロフィール編集画面
    public function edit()
    {
        $user = Auth::user();
        return view('mypage.profile-edit', compact('user'));
    }

    // プロフィール更新処理
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // -----画像アップロード-----
        if ($request->hasFile('image')) {
            // 古い画像削除（必要なら）
            if ($user->image && \Storage::exists('public/' . $user->image)) {
            \Storage::delete('public/' . $user->image);
            }

            // 新しい画像保存
            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->storeAs('public/profile', $imageName);
            $validated['image'] = 'profile/' . $imageName;
        }

        $user->update($validated);

        return redirect()->route('mypage.index');
    }
}

