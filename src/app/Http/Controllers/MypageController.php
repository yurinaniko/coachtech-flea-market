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
        $validated = $request->validated();
        $user = Auth::user();

        // --- プロフィール画像アップロード ---
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/profile', $imageName);
            $validated['image'] = 'profile/' . $imageName;
        }

        // --- users テーブル保存 ---
        $user->update([
            'name' => $validated['name'],
            'image' => $validated['image'] ?? $user->image,
        ]);

       // --- addresses テーブル保存（初回は create）---
        $user->address()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $validated['postal_code'],
                'address' => $validated['address'],
                'building' => $validated['building'],
            ]
        );

        return redirect()->route('mypage.index');
    }

    // プロフィール編集画面
    public function edit()
    {
        $user = Auth::user();
        $address = $user->address;
        return view('mypage.profile-edit', compact('user','address'));
    }

    // プロフィール更新処理
    public function update(ProfileRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();

        // --- 画像処理 ---
    if ($request->hasFile('image')) {
        $imageName = time() . '.' . $request->image->extension();
        $request->image->storeAs('public/profile', $imageName);
        $validated['image'] = 'profile/' . $imageName;
    }

    // --- users テーブル更新 ---
    $user->update([
        'name' => $validated['name'],
        'image' => $validated['image'] ?? $user->image,
    ]);

    // --- addresses テーブル更新 or 作成 ---
    $user->address()->updateOrCreate(
        ['user_id' => $user->id],
        [
            'postal_code' => $validated['postal_code'],
            'address' => $validated['address'],
            'building' => $validated['building'],
        ]
    );

        return redirect()->route('mypage.index');
    }
}

