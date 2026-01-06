<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        auth()->login($user);
        // 認証メール送信
        $user->sendEmailVerificationNotification();
        return redirect()->route('verification.notice');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
        return back()->withErrors([
        'login' => 'ログイン情報が登録されていません',
        ])->withInput();
        }
        $request->session()->regenerate();
        return redirect()->route('mypage.index');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showVerifyEmail()
    {
        return view('auth.verify-email');
    }

}
