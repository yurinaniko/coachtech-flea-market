<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    public function index($itemId)
    {
        $item = Item::findOrFail($itemId);
        $user = Auth::user();

        // プレースホルダー住所
        $placeholder = [
            'postal_code' => 'XXX-YYYY',
            'address'     => 'ここに住所が自動的に入ります',
            'building'    => '',
        ];

        return view('purchase.index', [
            'item' => $item,
            'user' => $user,
            'placeholder' => $placeholder,
        ]);
    }
    public function create()
    {
        $user = auth()->user();
        $profile = $user->profile;

        return view('mypage.profile-create', compact('user', 'profile'));
    }

    public function store(ProfileRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/profile', $imageName);
            $validated['img_url'] = 'profile/' . $imageName;
        }

        // --- profiles テーブル保存 ---
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'img_url' => $validated['img_url'] ?? optional($user->profile)->img_url,
                'postal_code' => $validated['postal_code'],
                'address'     => $validated['address'],
                'building'    => $validated['building'],
            ]
        );

        return redirect()->route('mypage.index')->with('success', 'プロフィールを更新しました！');
    }

    public function edit()
    {
        $user = auth()->user();

        // プロフィールが無ければ作成して返す
        $profile = $user->profile()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'img_url' => null,
                'postal_code' => '',
                'address' => '',
                'building' => '',
        ]
        );
        return view('mypage.profile-edit', compact('user', 'profile'));
    }

    public function update(ProfileRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();
        $profile = $user->profile;

        // --- 画像が新しくアップロードされた場合 ---
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/profile', $imageName);
            $validated['img_url'] = 'profile/' . $imageName;
        }

        // --- profiles テーブル更新 or 作成 ---
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'img_url' => $validated['img_url'] ?? optional($profile)->img_url,
                'postal_code' => $validated['postal_code'],
                'address'     => $validated['address'],
                'building'    => $validated['building'],
            ]
        );

        return redirect()
            ->route('mypage.profile', ['page' => 'sell'])
            ->with('success', 'プロフィールを更新しました！');
    }
}