<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Actions\Fortify\LoginUser;
use App\Actions\Fortify\CreateNewUser;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use App\Actions\Fortify\LoginResponse as CustomLoginResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class FortifyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::authenticateUsing(new LoginUser());
        $this->app->singleton(LoginResponse::class, CustomLoginResponse::class);

        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });
        Fortify::registerView(function () {
            return view('auth.register');
        });
        Fortify::loginView(function () {
            return view('auth.login');
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });

        // config/fortify.php の limiters で 'two-factor' を参照しているが未定義だと
        // 2FAチャレンジ経路で例外になり、総当たり防御も効かない。IP単位で制限する。
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id') . $request->ip());
        });

        // 登録・パスワードリセット等のFortifyルート全体の安全網（大量登録・メール爆撃の抑止）。
        // config/fortify.php の 'middleware' に 'throttle:fortify' を追加して有効化する。
        RateLimiter::for('fortify', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });
    }
}
