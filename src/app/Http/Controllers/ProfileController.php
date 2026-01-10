<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
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
        if (isset($validated['name'])) {
            $user->update([
                'name' => $validated['name'],
            ]);
        }
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/profile', $imageName);
            $validated['img_url'] = 'profile/' . $imageName;
        }
        $profile = $user->profile;

        if ($profile) {
            $profile->update([
                'img_url' => $validated['img_url'] ?? $profile->img_url,
                'postal_code' => $validated['postal_code'],
                'address' => $validated['address'],
                'building' => $validated['building'] ?? null,
            ]);
        } else {
            $user->profile()->create([
            'img_url' => $validated['img_url'] ?? null,
            'postal_code' => $validated['postal_code'],
            'address' => $validated['address'],
            'building' => $validated['building'] ?? null,
            ]);
        }
        return redirect()->route('mypage.index');
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

        if (isset($validated['name'])) {
            $user->update([
                'name' => $validated['name'],
            ]);
        }
        // --- 画像が新しくアップロードされた場合 ---
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/profile', $imageName);
            $validated['img_url'] = 'profile/' . $imageName;
        }
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'img_url' => $validated['img_url'] ?? optional($profile)->img_url,
                'postal_code' => $validated['postal_code'],
                'address'     => $validated['address'],
                'building' => $validated['building'] ?? null,
            ]
        );
        return redirect()
            ->route('mypage.profile', ['page' => 'sell']);
    }
}