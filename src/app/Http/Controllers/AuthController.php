<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        Auth::login($user);

        // 【初回かどうか判定】プロフィール未設定 → create
        if (empty($user->postal_code) || empty($user->address)) {
            return redirect()->route('mypage.profile.create');
        }

        // 設定済み → edit（通常の編集画面）
        return redirect()->route('mypage.profile.edit');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // 【ログイン時も判定】未設定 → create
            if (empty($user->postal_code) || empty($user->address)) {
                return redirect()->route('mypage.profile.create');
            }

            // 設定済み → 商品一覧（あなたの希望どおり）
            return redirect()->route('mypage.index');
        }

        return back()->withErrors([
            'email' => '認証情報が登録されていません。',
        ]);
    }
}
