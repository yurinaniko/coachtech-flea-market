<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

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

        return redirect()->route('profile.create');
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        $credentials = [
        'email' => $validated['email'],
        'password' => $validated['password'],
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $profile = $user->profile;

            // プロフィール未設定 → profile.create
            if (!$profile || empty($profile->postal_code) || empty($profile->address)) {
            return redirect()->route('profile.create');
            }

            // 設定済み → 商品一覧（あなたの希望どおり）
            return redirect()->route('mypage.index');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません。',
        ]);
    }
}
