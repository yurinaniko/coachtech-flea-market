<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    // 住所編集画面表示
    public function edit()
    {
        $address = Auth::user()->address;   // 1対1リレーション

        return view('mypage.address.edit', compact('address'));
    }

    // 更新処理
    public function update(AddressRequest $request)
    {
        $user = Auth::user();

        $user->address()->updateOrCreate(
        ['user_id' => $user->id],
        $request->validated()
        );

        return back()->with('success', '住所を更新しました');
    }
}