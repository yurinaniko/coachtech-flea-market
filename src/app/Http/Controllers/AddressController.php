<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('mypage.address.edit', compact('user'));
    }

    public function update(AddressRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $validated['postal_code'],
                'address'     => $validated['address'],
                'building'    => $validated['building'] ?? null,
                ]
        );

        // ユーザー情報をリフレッシュ
        Auth::user()->fresh();

        return redirect()->route('purchase.index', ['item' => session('current_item_id')])
                    ->with('success', '住所を更新しました！');
    }
}