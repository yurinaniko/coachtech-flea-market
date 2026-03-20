<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class LoginUser
{
    public function __invoke(Request $request)
    {
        if (empty($request->email)) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスを入力してください'],
            ]);
        }

        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスの形式で入力してください'],
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            return $user;
        }

        throw ValidationException::withMessages([
            'email' => ['ログイン情報が登録されていません'],
        ]);
    }
}