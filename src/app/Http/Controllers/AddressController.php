<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('mypage.address.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'postal_code' => 'required',
            'address' => 'required',
            'building' => 'nullable',
        ]);

        $user = Auth::user();

        // --- profiles テーブルを更新 ---
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
                'building'    => $request->building,
            ]
        );

        // ユーザー情報をリフレッシュ
        Auth::user()->fresh();

        return redirect()->route('purchase.index', ['item' => session('current_item_id')])
                    ->with('success', '住所を更新しました！');
    }
}