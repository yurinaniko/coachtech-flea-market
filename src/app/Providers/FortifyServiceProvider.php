<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Actions\Fortify\CreateNewUser;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use App\Actions\Fortify\LoginResponse as CustomLoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // ログイン後遷移
        $this->app->singleton(LoginResponse::class, CustomLoginResponse::class);

        // 登録後遷移（メール認証画面へ）
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect()->route('verification.notice');
                }
            };
        });
        Fortify::registerView(function () {
        return view('auth.register');
        });

        Fortify::loginView(function () {
        return view('auth.login');
        });

        Fortify::createUsersUsing(CreateNewUser::class);
    }
}
